<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFormVersion extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function form()
    {
        return $this->belongsTo(EventForm::class, 'event_form_id');
    }

    public function fields()
    {
        return $this->hasMany(EventFormField::class);
    }

    public function submissions()
    {
        return $this->hasMany(EventFormSubmission::class);
    }
}
