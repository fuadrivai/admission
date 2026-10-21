<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    protected $hidden = [
        'branch_id',
        'grade_id',
        'level_id',
        'prospects_id',
        'academic_year_id',
        'created_at',
        'updated_at',
    ];

    protected $with = ['branch','year'];

    protected $casts = [
        'already_visit'      => 'boolean',
        'open_day_visited'   => 'boolean',
        'date_of_birth'      => 'date',
    ];

    public function year()
    {
        return $this->belongsTo(AcademicYear::class,'academic_year_id');
    }

    public function prospect()
    {
        return $this->belongsTo(Prospects::class, 'prospects_id');
    }

    public function activities()
    {
        return $this->morphMany(ProspectActivity::class, 'activityable');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }
    
    public function admission()
    {
        return $this->hasOne(Admission::class);
    }

    public function transactions()
    {
        return $this->hasMany(EnrolmentTransaction::class);
    }

    public function initialPaymentTransaction()
    {
        return $this->hasOne(EnrolmentTransaction::class)
            ->whereHas('details', function ($query) {
                $query->whereIn('type', ['enrolment', 'streaming_test']);
            })
            ->orderBy('id');
    }

    public function getPaymentStatusAttribute($value): ?string
    {
        $transaction = $this->getRelationValue('initialPaymentTransaction');
        if (!$transaction) {
            $transaction = $this->initialPaymentTransaction()->first();
        }

        return $transaction ? strtoupper((string) $transaction->payment_status) : null;
    }

    public function registrationPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function syncLifecycleStatusFromTransactions(): void
    {
        if (strtoupper((string) $this->status) === 'CANCELLED') {
            return;
        }

        $statuses = $this->transactions()
            ->pluck('payment_status')
            ->map(function ($status) {
                return strtoupper((string) $status);
            });

        if ($statuses->contains(function ($status) {
            return in_array($status, ['PAID', 'SETTLED', 'COMPLETED', 'PENDING'], true);
        })) {
            $this->status = 'Active';
        } elseif ($statuses->isNotEmpty() && $statuses->every(function ($status) {
            return in_array($status, ['EXPIRED', 'FAILED', 'CANCELLED'], true);
        })) {
            $this->status = 'Inactive';
        } else {
            return;
        }

        $this->save();
    }

    public function mergedInto()
    {
        return $this->belongsTo(self::class, 'merged_into_enrolment_id');
    }

    public function mergedFrom()
    {
        return $this->hasMany(self::class, 'merged_into_enrolment_id');
    }

    public function isMerged(): bool
    {
        return $this->merged_into_enrolment_id !== null;
    }

    public function hasPaidRegistrationPayment(): bool
    {
        return $this->initialPaymentTransaction()
            ->whereRaw('UPPER(payment_status) IN (?, ?, ?)', ['PAID', 'SETTLED', 'COMPLETED'])
            ->exists();
    }

    public function hasActiveDpPayment(): bool
    {
        return $this->transactions()
            ->whereRaw('UPPER(payment_status) IN (?, ?, ?, ?)', ['PAID', 'SETTLED', 'COMPLETED', 'PENDING'])
            ->whereHas('details', function ($query) {
                $query->where('type', 'dp');
            })
            ->exists();
    }

    public function canCreateDp(): bool
    {
        return strtoupper((string) $this->status) === 'ACTIVE'
            && $this->hasPaidRegistrationPayment()
            && !$this->hasActiveDpPayment();
    }

    public function amountPaid()
    {
        return number_format($this->amount_paid, 2);
    }

    public function registrationFee()
    {
        return number_format($this->registration_fee, 2);
    }

    public function bankCharger()
    {
        return number_format($this->bank_charger, 2);
    }

    public function birthDateFormatted()
    {
        return date('d F Y', strtotime($this->date_of_birth));
    }

    public function paymentDateFormatted()
    {
        $transaction = $this->initialPaymentTransaction()->first();

        return $transaction && $transaction->payment_date
            ? $transaction->payment_date->format('d F Y')
            : '-';
    }

    public function avatarName()
    {
        $name = $this->child_name;

        if (!$name) {
            return 'NA';
        }

        $words = explode(' ', trim($name));

        $initials = collect($words)
            ->filter()            // hapus spasi kosong
            ->take(2)             // ambil max 2 kata
            ->map(function ($word) {
                return strtoupper(substr($word, 0, 1));
            })
            ->implode('');

        return $initials ?: 'NA';
    }
}
