<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Health extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'food_allergy' => 'boolean',
        'drug_allergy' => 'boolean',
        'uses_glasses' => 'boolean',
        'uses_hearing_aid' => 'boolean',
        'routine_medication' => 'boolean',
    ];

    public function setFoodAllergyAttribute($value)
    {
        $this->attributes['food_allergy'] = $this->toTinyInt($value);
    }

    public function setDrugAllergyAttribute($value)
    {
        $this->attributes['drug_allergy'] = $this->toTinyInt($value);
    }

    public function setUsesGlassesAttribute($value)
    {
        $this->attributes['uses_glasses'] = $this->toTinyInt($value);
    }

    public function setUsesHearingAidAttribute($value)
    {
        $this->attributes['uses_hearing_aid'] = $this->toTinyInt($value);
    }

    public function setRoutineMedicationAttribute($value)
    {
        $this->attributes['routine_medication'] = $this->toTinyInt($value);
    }

    private function toTinyInt($value): int
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }
}
