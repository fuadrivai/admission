<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEmailLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function invitation()
    {
        return $this->belongsTo(EventInvitation::class, 'event_invitation_id');
    }
}
