<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformPrice extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(UniformProduct::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
