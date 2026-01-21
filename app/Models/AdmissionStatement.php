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

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function financial()
    {
        return $this->hasOne(FinancialAgreement::class);
    }

    public function agreements()
    {
        return $this->hasMany(StatementAgreement::class);
    }
}
