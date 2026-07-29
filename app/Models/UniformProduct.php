<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformProduct extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $with = ['prices'];

    public function prices()
    {
        return $this->hasMany(UniformPrice::class,'product_id');
    }
}
