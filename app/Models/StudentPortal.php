<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPortal extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'student';
    protected $primaryKey = 'id';

}
