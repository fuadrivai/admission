<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPortal extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'id';

    public function getConnectionName()
    {
        return session('school_branch').'_school';
    }
}
