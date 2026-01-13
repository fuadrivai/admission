<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'surgery_history' => 'boolean',
        'hospitalization_history' => 'boolean',
        'seizure_history' => 'boolean',
        'accident_history' => 'boolean',
    ];

    public function setSurgeryHistoryAttribute($value)
    {
        $this->attributes['surgery_history'] = $this->toTinyInt($value);
    }

    public function setHospitalizationHistoryAttribute($value)
    {
        $this->attributes['hospitalization_history'] = $this->toTinyInt($value);
    }

    public function setSeizureHistoryAttribute($value)
    {
        $this->attributes['seizure_history'] = $this->toTinyInt($value);
    }

    public function setAccidentHistoryAttribute($value)
    {
        $this->attributes['accident_history'] = $this->toTinyInt($value);
    }

    private function toTinyInt($value): int
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }
}
