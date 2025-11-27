<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ParentUser extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'parents';
    protected $primaryKey = 'parents_id';

    protected $fillable = ['fullname', 'username', 'password'];

}
