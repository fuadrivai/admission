<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFieldAnswer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function eventRegistration()
    {
        return $this->belongsTo(EventRegistration::class);
    }

    public function eventField()
    {
        return $this->belongsTo(EventField::class);
    }
}
