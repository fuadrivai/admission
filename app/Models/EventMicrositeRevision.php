<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMicrositeRevision extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function microsite()
    {
        return $this->belongsTo(EventMicrosite::class, 'event_microsite_id');
    }

    public function studentNameField()
    {
        return $this->belongsTo(EventField::class, 'student_name_field_id');
    }

    public function parentNameField()
    {
        return $this->belongsTo(EventField::class, 'parent_name_field_id');
    }
}
