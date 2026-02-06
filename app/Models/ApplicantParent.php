<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantParent extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function dateBirth()
    {
        return date('d F Y', strtotime($this->birth_date));
    }

    public function monthlyIncome()
    {
        return number_format($this->monthly_income, 0, '.', ',');
    }
}
