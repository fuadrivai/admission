<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionFinancialSection extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function document()
    {
        return $this->belongsTo(AdmissionFinancialDocument::class, 'document_id');
    }

    public function items()
    {
        return $this->hasMany(AdmissionFinancialItem::class, 'section_id')->orderBy('sort_order')->orderBy('number');
    }
}
