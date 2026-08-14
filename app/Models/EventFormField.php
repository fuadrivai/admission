<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFormField extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function version()
    {
        return $this->belongsTo(
            EventFormVersion::class,
            'event_form_version_id'
        );
    }

    public function submissionValues()
    {
        return $this->hasMany(EventFormSubmissionValue::class);
    }
}
