<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLifecycleStatusToEnrolments extends Migration
{
    public function up()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->string('status', 16)->default('Active')->after('code')->index();
        });

        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->string('data_from', 32)->nullable()->after('source');
        });

        DB::table('enrolments')->orderBy('id')->chunkById(200, function ($enrolments) {
            foreach ($enrolments as $enrolment) {
                $hasTransaction = DB::table('enrolment_transactions')
                    ->where(function ($query) use ($enrolment) {
                        $query->where('enrolment_id', $enrolment->id);
                        if ($enrolment->invoice_id) {
                            $query->orWhere('invoice_id', $enrolment->invoice_id);
                        }
                    })
                    ->exists();
                $legacyPaymentStatus = strtoupper((string) $enrolment->payment_status);
                $hasLegacyPaymentData = $enrolment->invoice_id
                    || !in_array($legacyPaymentStatus, ['', 'UNPAID'], true)
                    || $enrolment->payment_date
                    || $enrolment->create_va_date
                    || $enrolment->expiry_va_date
                    || $enrolment->payment_url;

                if (!$hasTransaction && $hasLegacyPaymentData) {
                    $now = now();
                    $registrationAmount = (float) ($enrolment->registration_fee ?? 0)
                        + (float) ($enrolment->discount ?? 0);
                    $transactionId = DB::table('enrolment_transactions')->insertGetId([
                        'enrolment_id' => $enrolment->id,
                        'code' => $enrolment->code,
                        'invoice_id' => $enrolment->invoice_id,
                        'subtotal' => max(0, (float) ($enrolment->amount_paid ?? 0) - (float) ($enrolment->bank_charger ?? 0)),
                        'discount' => (float) ($enrolment->discount ?? 0),
                        'bank_charge' => (float) ($enrolment->bank_charger ?? 0),
                        'total_amount' => (float) ($enrolment->amount_paid ?? 0),
                        'payment_status' => $enrolment->payment_status ?: 'UNPAID',
                        'payment_date' => $enrolment->payment_date,
                        'create_va_date' => $enrolment->create_va_date,
                        'expiry_va_date' => $enrolment->expiry_va_date,
                        'payment_url' => $enrolment->payment_url,
                        'payment_place' => $enrolment->regis_place,
                        'source' => $enrolment->source_data,
                        'data_from' => $enrolment->data_from,
                        'noted' => 'Legacy payment transaction backfilled during enrolment lifecycle migration.',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('enrolment_details')->insert([
                        'enrolment_transaction_id' => $transactionId,
                        'type' => 'enrolment',
                        'description' => 'Enrolment Fee',
                        'amount' => $registrationAmount,
                        'discount' => (float) ($enrolment->discount ?? 0),
                        'subtotal' => max(0, $registrationAmount - (float) ($enrolment->discount ?? 0)),
                        'is_required_charge' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $transaction = DB::table('enrolment_transactions')
                    ->where(function ($query) use ($enrolment) {
                        $query->where('enrolment_id', $enrolment->id);
                        if ($enrolment->invoice_id) {
                            $query->orWhere('invoice_id', $enrolment->invoice_id);
                        }
                    })
                    ->when($enrolment->invoice_id, function ($query) use ($enrolment) {
                        $query->orderByRaw('CASE WHEN invoice_id = ? THEN 0 ELSE 1 END', [$enrolment->invoice_id]);
                    })
                    ->orderBy('id')
                    ->first();

                if ($transaction) {
                    if ($enrolment->invoice_id
                        && $transaction->invoice_id === $enrolment->invoice_id
                        && !DB::table('enrolment_details')
                            ->where('enrolment_transaction_id', $transaction->id)
                            ->exists()) {
                        $registrationAmount = (float) ($enrolment->registration_fee ?? 0)
                            + (float) ($enrolment->discount ?? 0);
                        DB::table('enrolment_details')->insert([
                            'enrolment_transaction_id' => $transaction->id,
                            'type' => 'enrolment',
                            'description' => 'Enrolment Fee',
                            'amount' => $registrationAmount,
                            'discount' => (float) ($enrolment->discount ?? 0),
                            'subtotal' => max(0, $registrationAmount - (float) ($enrolment->discount ?? 0)),
                            'is_required_charge' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $transactionUpdates = [];
                    if ($transaction->enrolment_id === null) {
                        $transactionUpdates['enrolment_id'] = $enrolment->id;
                    }
                    foreach ([
                        'payment_date',
                        'create_va_date',
                        'expiry_va_date',
                        'payment_url',
                    ] as $column) {
                        if ($transaction->{$column} === null && $enrolment->{$column} !== null) {
                            $transactionUpdates[$column] = $enrolment->{$column};
                        }
                    }
                    if ($transaction->payment_place === null && $enrolment->regis_place !== null) {
                        $transactionUpdates['payment_place'] = $enrolment->regis_place;
                    }
                    if ($transaction->data_from === null && $enrolment->data_from !== null) {
                        $transactionUpdates['data_from'] = $enrolment->data_from;
                    }
                    if ($transactionUpdates) {
                        DB::table('enrolment_transactions')
                            ->where('id', $transaction->id)
                            ->update($transactionUpdates);
                    }
                }

                if (!empty($enrolment->cancelled_at)
                    || strtoupper((string) $enrolment->payment_status) === 'CANCELLED') {
                    $status = 'Cancelled';
                } else {
                    $statuses = DB::table('enrolment_transactions')
                        ->where('enrolment_id', $enrolment->id)
                        ->pluck('payment_status')
                        ->map(function ($value) {
                            return strtoupper((string) $value);
                        });

                    if ($statuses->contains(function ($value) {
                        return in_array($value, ['PAID', 'SETTLED', 'COMPLETED', 'PENDING'], true);
                    })) {
                        $status = 'Active';
                    } elseif ($statuses->isNotEmpty() && $statuses->every(function ($value) {
                        return in_array($value, ['EXPIRED', 'FAILED', 'CANCELLED'], true);
                    })) {
                        $status = 'Inactive';
                    } elseif (!$statuses->isNotEmpty()
                        && strtoupper((string) $enrolment->payment_status) === 'EXPIRED') {
                        $status = 'Inactive';
                    } else {
                        $status = 'Active';
                    }
                }

                DB::table('enrolments')->where('id', $enrolment->id)->update(['status' => $status]);
            }
        });
    }

    public function down()
    {
        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->dropColumn('data_from');
        });

        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
}
