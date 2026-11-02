<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleSheetMaster extends Model
{
    protected $fillable = ['integration_key', 'name', 'google_sheet_url'];
}
