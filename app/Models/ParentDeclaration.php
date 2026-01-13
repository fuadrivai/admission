<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentDeclaration extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'agreed'    => 'boolean',
        'agreed_at' => 'datetime',
    ];

    public function setAgreedAttribute($value)
    {
        $isAgreed = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $this->attributes['agreed'] = $isAgreed;

        if ($isAgreed) {
            $this->attributes['agreed_at'] = Carbon::now();
        }
    }
}
