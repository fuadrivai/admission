<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAgreement extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'agreed_at' => 'datetime',
        'agree_full_payment_terms' => 'boolean',
        'agree_development_fee_policy' => 'boolean',
        'agree_annual_and_school_fee_policy' => 'boolean',
        'agree_exam_fee' => 'boolean',
        'agree_learning_material_fee' => 'boolean',
        'agree_additional_activity_fee' => 'boolean',
        'agree_monthly_school_fee_payment' => 'boolean',
        'agree_ittihada_fee' => 'boolean',
        'agree_full_financial_obligation' => 'boolean',
        'agree_financial_terms_and_consequences' => 'boolean',
        'agree_truth_and_consent' => 'boolean',
    ];

    public function statement()
    {
        return $this->belongsTo(AdmissionStatement::class);
    }
}
