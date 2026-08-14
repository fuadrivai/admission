<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFormEmailTemplate extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(EventForm::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EventFormEmailLog::class);
    }
}
