<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['applicant_id', 'enrolment_id', 'branch_id', 'level_id', 'grade_id', 'created_at', 'updated_at'];

    protected $casts = [
        'is_complete' => 'boolean',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
    public function enrolment()
    {
        return $this->belongsTo(Enrolment::class);
    }
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
    public function applicantName()
    {
        return $this->applicant->fullname;
    }
    public function levelName()
    {
        return $this->level->name;
    }
    public function gradeName()
    {
        return $this->grade->name;
    }
    public function branchName()
    {
        return $this->branch->name;
    }
    public function documents()
    {
        return $this->hasMany(AdmissionDocument::class);
    }
    public function statement()
    {
        return $this->hasOne(AdmissionStatement::class);
    }
}
