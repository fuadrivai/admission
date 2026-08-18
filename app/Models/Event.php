<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'active_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function fields()
    {
        return $this->hasMany(EventField::class);
    }

    public function forms()
    {
        return $this->fields();
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function priceOptions()
    {
        return $this->hasMany(EventPriceOption::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EventEmailTemplate::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
