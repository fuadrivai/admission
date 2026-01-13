<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnancyHistory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'medication_during_pregnancy' => 'boolean',
        'illness_during_pregnancy' => 'boolean',
    ];

    public function setMedicationDuringPregnancyAttribute($value)
    {
        $this->attributes['medication_during_pregnancy'] = $this->toTinyInt($value);
    }

    public function setIllnessDuringPregnancyAttribute($value)
    {
        $this->attributes['illness_during_pregnancy'] = $this->toTinyInt($value);
    }

    private function toTinyInt($value): int
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }
}
