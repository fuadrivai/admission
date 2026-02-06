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

    public function getDevelopmentFeeTerbilangAttribute()
    {
        return trim(
            $this->terbilang($this->development_fee)
        ). ' rupiah';
    }
    public function getAnnualFeeTerbilangAttribute()
    {
        return trim(
            $this->terbilang($this->annual_fee)
        ). ' rupiah';
    }
    public function getSchoolFeeTerbilangAttribute()
    {
        return trim(
            $this->terbilang($this->school_fee)
        ). ' rupiah';
    }

    public function terbilang($number)
    {
        $number = (int) floor($number);

        $words = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
        ];

        if ($number < 12) {
            return $words[$number];
        } elseif ($number < 20) {
            return $this->terbilang($number - 10) . ' belas';
        } elseif ($number < 100) {
            return $this->terbilang(intval($number / 10)) . ' puluh ' . $this->terbilang($number % 10);
        } elseif ($number < 200) {
            return 'Seratus ' . $this->terbilang($number - 100);
        } elseif ($number < 1000) {
            return $this->terbilang(intval($number / 100)) . ' ratus ' . $this->terbilang($number % 100);
        } elseif ($number < 2000) {
            return 'Seribu ' . $this->terbilang($number - 1000);
        } elseif ($number < 1000000) {
            return $this->terbilang(intval($number / 1000)) . ' ribu ' . $this->terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            return $this->terbilang(intval($number / 1000000)) . ' juta ' . $this->terbilang($number % 1000000);
        } elseif ($number < 1000000000000) {
            return $this->terbilang(intval($number / 1000000000)) . ' miliar ' . $this->terbilang($number % 1000000000);
        }

        return '';
    }
}
