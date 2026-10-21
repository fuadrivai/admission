<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalEnrolmentReport extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'snapshot_month' => 'integer',
        'active_children' => 'integer',
        'cancelled_children' => 'integer',
        'enrolment_expired' => 'integer',
        'enrolment_pending' => 'integer',
        'enrolment_paid' => 'integer',
        'dp_expired' => 'integer',
        'dp_pending' => 'integer',
        'dp_paid' => 'integer',
        'target' => 'integer',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
