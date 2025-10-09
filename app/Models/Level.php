<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
