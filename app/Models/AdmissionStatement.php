<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionStatement extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'completed_at' => 'datetime',
        'is_complete' => 'boolean'
    ];
}
