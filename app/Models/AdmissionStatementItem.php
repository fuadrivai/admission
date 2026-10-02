<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionStatementItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(AdmissionStatementSection::class, 'section_id');
    }

    public function statementAgreements()
    {
        return $this->hasMany(StatementAgreement::class, 'statement_item_id');
    }
}
