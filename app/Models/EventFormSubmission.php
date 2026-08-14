<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFormSubmission extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function form()
    {
        return $this->belongsTo(EventForm::class);
    }

    public function version()
    {
        return $this->belongsTo(
            EventFormVersion::class,
            'event_form_version_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function values()
    {
        return $this->hasMany(EventFormSubmissionValue::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EventFormEmailLog::class);
    }
}
