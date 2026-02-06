<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function email()
    {
        return $this->belongsTo(EmailSetting::class);
    }
    
    public function whatsappCode()
    {
        return $this->belongsTo(WhatsappCode::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}
