<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectActivity extends Model
{
    use HasFactory;

    protected $guarded = ['id']; 

    public function prospect()
    {
        return $this->belongsTo(Prospects::class);
    }

    public function activityable()
    {
        return $this->morphTo();
    }
}
