<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMicrosite extends Model
{
    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function revisions()
    {
        return $this->hasMany(EventMicrositeRevision::class);
    }

    public function publishedRevision()
    {
        return $this->hasOne(EventMicrositeRevision::class)
            ->where('status', 'PUBLISHED')
            ->latestOfMany();
    }

    public function draftRevision()
    {
        return $this->hasOne(EventMicrositeRevision::class)
            ->where('status', 'DRAFT')
            ->latestOfMany();
    }
}
