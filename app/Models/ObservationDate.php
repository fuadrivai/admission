<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObservationDate extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function times()
    {
        return $this->hasMany(ObservationTime::class);
    }
}
