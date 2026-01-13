<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Immunizations extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'bcg' => 'boolean',
        'hepatitis' => 'boolean',
        'dtp' => 'boolean',
        'polio' => 'boolean',
        'measles' => 'boolean',
        'hepatitis_a' => 'boolean',
        'mmr' => 'boolean',
        'influenza' => 'boolean',
    ];

    public function setBcgAttribute($value)
    {
        $this->attributes['bcg'] = $this->toTinyInt($value);
    }

    public function setHepatitisAttribute($value)
    {
        $this->attributes['hepatitis'] = $this->toTinyInt($value);
    }

    public function setDtpAttribute($value)
    {
        $this->attributes['dtp'] = $this->toTinyInt($value);
    }

    public function setPolioAttribute($value)
    {
        $this->attributes['polio'] = $this->toTinyInt($value);
    }

    public function setMeaslesAttribute($value)
    {
        $this->attributes['measles'] = $this->toTinyInt($value);
    }

    public function setHepatitisAAttribute($value)
    {
        $this->attributes['hepatitis_a'] = $this->toTinyInt($value);
    }

    public function setMmrAttribute($value)
    {
        $this->attributes['mmr'] = $this->toTinyInt($value);
    }

    public function setInfluenzaAttribute($value)
    {
        $this->attributes['influenza'] = $this->toTinyInt($value);
    }

    private function toTinyInt($value): int
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }
}
