<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function forms()
    {
        return $this->hasMany(EventForm::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
