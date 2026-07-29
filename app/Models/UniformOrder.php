<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $with = ['branch', 'level', 'grade', 'details'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function details()
    {
        return $this->hasMany(UniformOrderDetail::class);
    }
}
