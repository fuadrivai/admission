<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public const STATUSES = [
        'Registered',
        'Present',
        'Absent',
        'Expired',
    ];

    public function time()
    {
        return $this->belongsTo(ObservationTime::class);
    }

    public function setStatusAttribute($value)
    {
        if (! in_array($value, self::STATUSES)) {
            throw new \InvalidArgumentException("Invalid status: {$value}");
        }

        $this->attributes['status'] = $value;
    }
}
