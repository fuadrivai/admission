<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrolmentTransaction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'bank_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'create_va_date' => 'datetime',
        'expiry_va_date' => 'datetime',
        'payment_date' => 'datetime',
        'confirmation_email_sent_at' => 'datetime',
    ];

    public function enrolment()
    {
        return $this->belongsTo(Enrolment::class);
    }

    public function details()
    {
        return $this->hasMany(EnrolmentDetail::class);
    }
}
