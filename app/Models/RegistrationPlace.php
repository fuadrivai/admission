<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationPlace extends Model
{
    protected $fillable = ['name', 'code', 'is_other', 'is_active'];

    protected $casts = ['is_other' => 'boolean', 'is_active' => 'boolean'];
}
