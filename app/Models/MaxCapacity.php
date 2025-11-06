<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaxCapacity extends Model
{
    use HasFactory;
    protected $table = 'max_capacity';
    protected $guarded = ['id'];
}
