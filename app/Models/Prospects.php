<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospects extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function enrolment()
    {
        return $this->hasOne(Enrolment::class);
    }
    public function schoolVisit()
    {
        return $this->hasOne(Enrolment::class);
    }

    public function activities()
    {
        return $this->hasMany(ProspectActivity::class);
    }
}
