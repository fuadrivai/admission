<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrolmentPriceItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function enrolmentPrice()
    {
        return $this->belongsTo(EnrolmentPrice::class);
    }
}
