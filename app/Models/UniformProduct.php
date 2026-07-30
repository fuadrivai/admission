<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformProduct extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        "is_active" => "boolean"
    ];

    public function prices()
    {
        return $this->hasMany(UniformPrice::class,'uniform_product_id');
    }
}
