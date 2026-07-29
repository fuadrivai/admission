<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformPrice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        "price"=>"decimal:2"
    ];

    public function product()
    {
        return $this->belongsTo(UniformProduct::class, 'uniform_product_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
