<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObservationTime extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $appends = ['rest'];

    public function date()
    {
        return $this->belongsTo(ObservationDate::class);
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }

    public function getRestAttribute()
    {
        return $this->max_quota - $this->observations()->count();
    }
}
