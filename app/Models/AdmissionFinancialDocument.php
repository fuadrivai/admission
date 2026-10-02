<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionFinancialDocument extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'effective_at' => 'date',
    ];

    public function sections()
    {
        return $this->hasMany(AdmissionFinancialSection::class, 'document_id')->orderBy('sort_order');
    }

    public function financialAgreements()
    {
        return $this->hasMany(FinancialAgreement::class, 'financial_document_id');
    }
}
