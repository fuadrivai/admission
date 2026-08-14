<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventForm extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'settings' => 'array',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function versions()
    {
        return $this->hasMany(EventFormVersion::class);
    }

    public function submissions()
    {
        return $this->hasMany(EventFormSubmission::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EventFormEmailTemplate::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EventFormEmailLog::class);
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
