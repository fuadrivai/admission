<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaEreport extends Model
{
    protected $table = 'm_siswa';
    protected $primaryKey = 'id';

    public function getConnectionName()
    {
        return session('school_branch').'_ereport_'.session('level');
    }
}
