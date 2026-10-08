<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrolmentPrice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function items()
    {
        return $this->hasMany(EnrolmentPriceItem::class);
    }
}
