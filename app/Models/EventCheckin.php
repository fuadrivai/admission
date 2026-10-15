<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCheckin extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function invitation()
    {
        return $this->belongsTo(EventInvitation::class, 'event_invitation_id');
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
