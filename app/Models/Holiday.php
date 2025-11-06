<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['dateFrontend'];

    public function getDateFrontendAttribute()
    {
        return Carbon::parse($this->date)->format('l, F j, Y');
    }
}
