<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['branch_id', 'grade_id', 'branch_id', 'level_id', 'prospects_id', 'created_at', 'updated_at'];

    protected $casts = [
        'already_visit'      => 'boolean',
        'open_day_visited'   => 'boolean',
        'date_of_birth'      => 'date',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospects::class, 'prospects_id');
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
        return date('d F Y', strtotime($this->payment_date));
    }
}
