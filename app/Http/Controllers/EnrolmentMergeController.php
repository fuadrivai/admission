<?php

namespace App\Http\Controllers;

use App\Models\Enrolment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrolmentMergeController extends Controller
{
    private const MAX_PAIRS = 20;

    public function index(Request $request)
    {
        $this->authorizeMerge();

        $request->validate([
            'pairs' => ['nullable', 'array', 'max:' . self::MAX_PAIRS],
            'pairs.*.source_code' => ['nullable', 'string', 'max:100'],
            'pairs.*.master_code' => ['nullable', 'string', 'max:100'],
        ]);

        $inputPairs = collect($request->query('pairs', []))
            ->map(function ($pair) {
                return [
                    'source_code' => trim((string) ($pair['source_code'] ?? '')),
                    'master_code' => trim((string) ($pair['master_code'] ?? '')),
                ];
            })
            ->filter(function ($pair) {
                return $pair['source_code'] !== '' || $pair['master_code'] !== '';
            })
            ->values();

        $pairs = $this->previewPairs($inputPairs);

        return view('enrolment.merge', [
            'title' => 'Merge Enrolment',
            'pairs' => $pairs,
            'inputPairs' => $inputPairs->isEmpty()
                ? collect([['source_code' => '', 'master_code' => '']])
                : $inputPairs,
            'canMerge' => $pairs->isNotEmpty() && $pairs->every(function ($pair) {
                return !$pair['blockers'];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeMerge();

        $validated = $request->validate([
            'pairs' => ['required', 'array', 'min:1', 'max:' . self::MAX_PAIRS],
            'pairs.*.source_code' => ['required', 'string', 'max:100'],
            'pairs.*.master_code' => ['required', 'string', 'max:100'],
            'confirm_merge' => ['accepted'],
        ]);

        $pairs = collect($validated['pairs'])
            ->map(function ($pair) {
                return [
                    'source_code' => trim($pair['source_code']),
                    'master_code' => trim($pair['master_code']),
                ];
            })
            ->values();

        $this->validatePairSet($pairs);

        $result = DB::transaction(function () use ($pairs) {
            $codes = $pairs->flatMap(function ($pair) {
                return [$pair['source_code'], $pair['master_code']];
            })->unique(function ($code) {
                return $this->normalizedCode($code);
            })->values();

            $records = Enrolment::whereIn('code', $codes)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy(function ($enrolment) {
                    return $this->normalizedCode($enrolment->code);
                });

            $preparedPairs = [];
            $blockers = [];
            $incomingInvoices = [];

            foreach ($pairs as $index => $pair) {
                $source = $records->get($this->normalizedCode($pair['source_code']));
                $master = $records->get($this->normalizedCode($pair['master_code']));

                if (!$source) {
                    $blockers[] = "Pasangan " . ($index + 1) . ": enrolment sumber {$pair['source_code']} tidak ditemukan.";
                }
                if (!$master) {
                    $blockers[] = "Pasangan " . ($index + 1) . ": enrolment master {$pair['master_code']} tidak ditemukan.";
                }
                if (!$source || !$master) {
                    continue;
                }

                $sourceTransactions = DB::table('enrolment_transactions')
                    ->where('enrolment_id', $source->id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();
                $masterTransactions = DB::table('enrolment_transactions')
                    ->where('enrolment_id', $master->id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();
                $pairBlockers = array_merge(
                    $this->mergeBlockers($source, $master),
                    $this->transactionBlockers($sourceTransactions)
                );

                foreach ($pairBlockers as $message) {
                    $blockers[] = "Pasangan " . ($index + 1) . " ({$source->code} → {$master->code}): {$message}";
                }

                foreach ($sourceTransactions as $transaction) {
                    if ($transaction->invoice_id === null || $transaction->invoice_id === '') {
                        continue;
                    }

                    $invoiceKey = $this->normalizedCode($transaction->invoice_id);
                    $targetKey = $master->id . ':' . $invoiceKey;
                    if (isset($incomingInvoices[$targetKey])) {
                        $blockers[] = "Pasangan " . ($index + 1) . " ({$source->code} → {$master->code}): invoice {$transaction->invoice_id} juga dipindahkan dari pasangan lain dalam batch.";
                    }
                    $incomingInvoices[$targetKey] = true;
                }

                $preparedPairs[] = [
                    'source' => $source,
                    'master' => $master,
                    'source_transactions' => $sourceTransactions,
                    'master_transactions' => $masterTransactions,
                ];
            }

            if ($blockers) {
                throw ValidationException::withMessages(['merge' => $blockers]);
            }

            $mergeResults = collect($preparedPairs)->map(function ($preparedPair) {
                return $this->mergePair($preparedPair);
            });

            return [
                'pair_count' => count($preparedPairs),
                'transaction_count' => $mergeResults->sum(function ($result) {
                    return $result['transaction_count'];
                }),
                'activity_count' => $mergeResults->sum(function ($result) {
                    return $result['activity_count'];
                }),
                'pairs' => collect($preparedPairs)->map(function ($pair) {
                    return $pair['source']->code . ' → ' . $pair['master']->code;
                })->all(),
            ];
        }, 3);

        return redirect()->route('enrolment.merge')->with(
            'success',
            "Batch merge {$result['pair_count']} pasangan berhasil: "
                . implode(', ', $result['pairs'])
                . ". {$result['transaction_count']} transaksi dan {$result['activity_count']} aktivitas prospect dipindahkan; admission tetap pada enrolment asal."
        );
    }

    private function previewPairs($inputPairs)
    {
        $codes = $inputPairs->flatMap(function ($pair) {
            return [$pair['source_code'], $pair['master_code']];
        })->filter()->unique(function ($code) {
            return $this->normalizedCode($code);
        })->values();

        $records = Enrolment::with(['branch', 'level', 'grade', 'transactions.details', 'admission.applicant'])
            ->whereIn('code', $codes)
            ->get()
            ->keyBy(function ($enrolment) {
                return $this->normalizedCode($enrolment->code);
            });

        $previewPairs = $inputPairs->map(function ($pair, $index) use ($records) {
            $source = $records->get($this->normalizedCode($pair['source_code']));
            $master = $records->get($this->normalizedCode($pair['master_code']));
            $blockers = [];

            if ($pair['source_code'] === '' || $pair['master_code'] === '') {
                $blockers[] = 'Masukkan kode sumber dan kode master untuk pasangan ini.';
            } elseif ($this->normalizedCode($pair['source_code']) === $this->normalizedCode($pair['master_code'])) {
                $blockers[] = 'Kode sumber dan master harus berbeda.';
            } else {
                if (!$source) {
                    $blockers[] = "Enrolment sumber {$pair['source_code']} tidak ditemukan.";
                }
                if (!$master) {
                    $blockers[] = "Enrolment master {$pair['master_code']} tidak ditemukan.";
                }
                if ($source && $master) {
                    $blockers = array_merge($blockers, $this->mergeBlockers($source, $master));
                }
            }

            return [
                'number' => $index + 1,
                'source' => $source,
                'master' => $master,
                'source_code' => $pair['source_code'],
                'master_code' => $pair['master_code'],
                'blockers' => $blockers,
            ];
        })->all();

        $masters = [];
        foreach ($previewPairs as $index => $pair) {
            $sourceCode = $this->normalizedCode($pair['source_code']);
            $masterCode = $this->normalizedCode($pair['master_code']);

            if ($sourceCode !== '' && isset($sources[$sourceCode])) {
                $previewPairs[$index]['blockers'][] = 'Kode sumber digunakan lebih dari sekali dalam batch.';
            }
            if ($masterCode !== '') {
                $masters[$masterCode] = true;
            }
        }

        foreach ($previewPairs as $index => $pair) {
            if ($pair['source_code'] !== '' && isset($masters[$this->normalizedCode($pair['source_code'])])) {
                $previewPairs[$index]['blockers'][] = 'Kode ini dipakai sebagai sumber dan master di batch yang sama; proses sebagai batch terpisah.';
            }
        }

        return collect($previewPairs);
    }

    private function mergePair(array $pair): array
    {
        $source = $pair['source'];
        $master = $pair['master'];
        $sourceProspectId = $source->prospects_id;
        $masterProspectId = $master->prospects_id;
        $activityIds = DB::table('prospect_activities')
            ->where('prospects_id', $sourceProspectId)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id');
        $masterActivityCount = DB::table('prospect_activities')
            ->where('prospects_id', $masterProspectId)
            ->count();
        $sourceAdmissionIds = DB::table('admissions')
            ->where('enrolment_id', $source->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id')
            ->all();
        $masterAdmissionIds = DB::table('admissions')
            ->where('enrolment_id', $master->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id')
            ->all();
        $transactionIds = $pair['source_transactions']->pluck('id');
        $transactionStatuses = $pair['source_transactions']->pluck('payment_status', 'id');
        $sourceDetailsCount = DB::table('enrolment_details')
            ->whereIn('enrolment_transaction_id', $transactionIds)
            ->lockForUpdate()
            ->count();
        $masterTransactionCount = DB::table('enrolment_transactions')
            ->where('enrolment_id', $master->id)
            ->count();
        $now = now();

        if ($transactionIds->isNotEmpty()) {
            DB::table('enrolment_transactions')
                ->whereIn('id', $transactionIds)
                ->where('enrolment_id', $source->id)
                ->update([
                    'enrolment_id' => $master->id,
                    'updated_at' => $now,
                ]);
        }

        if ($activityIds->isNotEmpty()) {
            DB::table('prospect_activities')
                ->whereIn('id', $activityIds)
                ->where('prospects_id', $sourceProspectId)
                ->update([
                    'prospects_id' => $masterProspectId,
                    'updated_at' => $now,
                ]);
        }

        $source->update([
            'merged_into_enrolment_id' => $master->id,
            'merged_at' => $now,
            'prospects_id' => null,
        ]);

        $sourceAfter = Enrolment::findOrFail($source->id);
        $masterAfter = Enrolment::findOrFail($master->id);
        $movedStatuses = DB::table('enrolment_transactions')
            ->whereIn('id', $transactionIds)
            ->orderBy('id')
            ->pluck('payment_status', 'id');
        $movedDetailsCount = DB::table('enrolment_details')
            ->whereIn('enrolment_transaction_id', $transactionIds)
            ->count();

        if (
            $sourceAfter->merged_into_enrolment_id != $master->id
            || $sourceAfter->prospects_id !== null
            || $sourceAfter->transactions()->exists()
            || $masterAfter->transactions()->count() !== $masterTransactionCount + $transactionIds->count()
            || $masterAfter->date_of_birth->toDateString() !== $master->date_of_birth->toDateString()
            || $transactionStatuses->all() !== $movedStatuses->all()
            || DB::table('prospect_activities')->where('prospects_id', $sourceProspectId)->exists()
            || DB::table('prospect_activities')->where('prospects_id', $masterProspectId)->count() !== $masterActivityCount + $activityIds->count()
            || DB::table('admissions')->where('enrolment_id', $source->id)->orderBy('id')->pluck('id')->all() !== $sourceAdmissionIds
            || DB::table('admissions')->where('enrolment_id', $master->id)->orderBy('id')->pluck('id')->all() !== $masterAdmissionIds
            || $movedDetailsCount !== $sourceDetailsCount
        ) {
            throw ValidationException::withMessages([
                'merge' => "Verifikasi setelah merge {$source->code} ke {$master->code} gagal. Seluruh batch dibatalkan.",
            ]);
        }

        return [
            'transaction_count' => $transactionIds->count(),
            'activity_count' => $activityIds->count(),
        ];
    }

    private function validatePairSet($pairs): void
    {
        $sources = [];
        $sourceSet = [];
        $masterSet = [];

        foreach ($pairs as $index => $pair) {
            $source = $this->normalizedCode($pair['source_code']);
            $master = $this->normalizedCode($pair['master_code']);

            if ($source === $master) {
                throw ValidationException::withMessages([
                    'pairs' => 'Pasangan ' . ($index + 1) . ': kode sumber dan master harus berbeda.',
                ]);
            }
            if (isset($sources[$source])) {
                throw ValidationException::withMessages([
                    'pairs' => "Kode sumber {$pair['source_code']} digunakan lebih dari sekali.",
                ]);
            }

            $sources[$source] = true;
            $sourceSet[$source] = $pair['source_code'];
            $masterSet[$master] = $pair['master_code'];
        }

        foreach ($sourceSet as $source => $code) {
            if (isset($masterSet[$source])) {
                throw ValidationException::withMessages([
                    'pairs' => "Kode {$code} dipakai sebagai sumber dan master pada batch yang sama; buat batch terpisah.",
                ]);
            }
        }
    }

    private function authorizeMerge(): void
    {
        abort_unless(auth()->check() && auth()->user()->role !== 'user', 403);
    }

    private function mergeBlockers(Enrolment $source, Enrolment $master): array
    {
        $blockers = [];

        if ($source->id === $master->id) {
            $blockers[] = 'Record sumber dan master harus berbeda.';
        }
        if ($source->isMerged() || $master->isMerged()) {
            $blockers[] = 'Record yang sudah diarsipkan/merged tidak dapat diproses lagi.';
        }
        if (!$source->prospects_id || !$master->prospects_id) {
            $blockers[] = 'Kedua enrolment harus memiliki prospect agar aktivitas dapat digabungkan.';
        } elseif ((string) $source->prospects_id === (string) $master->prospects_id) {
            $blockers[] = 'Enrolment sumber dan master sudah memakai prospect yang sama.';
        }
        if (!$master->date_of_birth) {
            $blockers[] = 'Tanggal lahir master kosong; lengkapi data sebelum merge.';
        }
        if ($this->normalizedName($source->child_name) === '' || $this->normalizedName($source->child_name) !== $this->normalizedName($master->child_name)) {
            $blockers[] = 'Nama siswa sumber dan master tidak cocok.';
        }

        foreach (['branch_id' => 'cabang', 'level_id' => 'level', 'grade_id' => 'kelas', 'academic_year_id' => 'tahun ajaran'] as $field => $label) {
            if ((string) $source->{$field} !== (string) $master->{$field}) {
                $blockers[] = "Data {$label} sumber dan master tidak cocok.";
            }
        }

        if ($source->prospects_id) {
            foreach (['school_visits' => 'school visit', 'observations' => 'observasi'] as $table => $label) {
                if (DB::table($table)->where('prospects_id', $source->prospects_id)->exists()) {
                    $blockers[] = "Prospect sumber masih memiliki data {$label}; pindahkan/tangani terlebih dahulu.";
                }
            }
        }

        $sourceInvoices = DB::table('enrolment_transactions')
            ->where('enrolment_id', $source->id)
            ->whereNotNull('invoice_id')
            ->pluck('invoice_id');
        if (DB::table('enrolment_transactions')
            ->where('enrolment_id', $source->id)
            ->whereNotNull('invoice_id')
            ->select('invoice_id')
            ->groupBy('invoice_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists()) {
            $blockers[] = 'Sumber memiliki nomor invoice duplikat; periksa transaksi sebelum merge.';
        }

        if ($sourceInvoices->isNotEmpty() && DB::table('enrolment_transactions')
            ->where('enrolment_id', $master->id)
            ->whereIn('invoice_id', $sourceInvoices)
            ->exists()) {
            $blockers[] = 'Invoice yang sama sudah ada pada master; periksa duplikasi sebelum merge.';
        }

        return $blockers;
    }

    private function transactionBlockers($sourceTransactions): array
    {
        $blockers = [];

        foreach ($sourceTransactions as $transaction) {
            if (!DB::table('enrolment_details')->where('enrolment_transaction_id', $transaction->id)->exists()) {
                $blockers[] = "Transaksi {$transaction->invoice_id} tidak memiliki detail; perbaiki sebelum merge.";
            }
        }

        return $blockers;
    }

    private function normalizedName(?string $name): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', (string) $name)));
    }

    private function normalizedCode(string $code): string
    {
        return strtolower(trim($code));
    }
}
