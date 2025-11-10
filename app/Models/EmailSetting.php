<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // protected $hidden = [
    //     'app_password',
    // ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
