<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolVisit extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'roles' => 'array',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospects::class, 'prospects_id');
    }

    public function activities()
    {
        return $this->morphMany(ProspectActivity::class, 'activityable');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function dateTime()
    {
        $date =  date('d F Y', strtotime($this->date));
        return $date. ' '. date('H:i', strtotime($this->time));
    }
}
