<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrolmentDetail extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_required_charge' => 'boolean',
    ];

    public function transaction()
    {
        return $this->belongsTo(EnrolmentTransaction::class, 'enrolment_transaction_id');
    }
}
