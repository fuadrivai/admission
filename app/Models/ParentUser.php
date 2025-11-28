<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentUser extends Model
{
    protected $table = 'parents';
    protected $primaryKey = 'parents_id';

    protected $fillable = ['fullname', 'username', 'password'];

    public function getConnectionName()
    {
        return session('school_branch').'_school';
    }

}
