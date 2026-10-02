<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionStatementDocument extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'effective_at' => 'date',
    ];

    public function sections()
    {
        return $this->hasMany(AdmissionStatementSection::class, 'document_id')->orderBy('sort_order');
    }

    public function items()
    {
        return $this->hasManyThrough(AdmissionStatementItem::class, AdmissionStatementSection::class, 'document_id', 'section_id');
    }

    public function statementAgreements()
    {
        return $this->hasMany(StatementAgreement::class, 'statement_document_id');
    }
}
