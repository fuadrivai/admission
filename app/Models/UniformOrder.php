<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $with = ['branch', 'level', 'grade', 'details'];
    protected $casts = [
        'order_date' => 'datetime',
        'payment_date' => 'datetime',
        'picked_up_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function details()
    {
        return $this->hasMany(UniformOrderDetail::class);
    }

    public function pickupUser()
    {
        return $this->belongsTo(User::class, 'picked_up_by');
    }
}
