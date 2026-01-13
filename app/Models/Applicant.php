<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'ever_not_school'   => 'boolean',
        'therapy_history'   => 'boolean',
        'dev_check'         => 'boolean',
    ];

    public function parents()
    {
        return $this->hasMany(ApplicantParent::class);
    }
    public function health()
    {
        return $this->hasOne(Health::class);
    }
    public function immunization()
    {
        return $this->hasOne(Immunizations::class);
    }
    public function medicalHistory()
    {
        return $this->hasOne(MedicalHistory::class);
    }
    public function pregnancyHistory()
    {
        return $this->hasOne(PregnancyHistory::class);
    }
    public function declaration()
    {
        return $this->hasOne(ParentDeclaration::class);
    }
}
