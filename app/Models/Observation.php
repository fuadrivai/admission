<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function time()
    {
        return $this->belongsTo(ObservationTime::class);
    }
}
