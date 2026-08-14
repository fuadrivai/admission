<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFormEmailLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function form()  
    {
        return $this->belongsTo(EventForm::class);
    }

    public function submission()
    {
        return $this->belongsTo(
            EventFormSubmission::class,
            'event_form_submission_id'
        );
    }

    public function template()
    {
        return $this->belongsTo(
            EventFormEmailTemplate::class,
            'event_form_email_template_id'
        );
    }
}
