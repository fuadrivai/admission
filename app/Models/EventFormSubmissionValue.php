<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFormSubmissionValue extends Model
{
    protected $guarded = ['id'];

    public function submission()
    {
        return $this->belongsTo(
            EventFormSubmission::class,
            'event_form_submission_id'
        );
    }

    public function field()
    {
        return $this->belongsTo(
            EventFormField::class,
            'event_form_field_id'
        );
    }
}
