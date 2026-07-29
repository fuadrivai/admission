<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformOrderDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(UniformOrder::class, 'uniform_order_id');
    }

    public function product()
    {
        return $this->belongsTo(UniformProduct::class, 'uniform_product_id');
    }
}
