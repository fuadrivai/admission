<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementAgreement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'agreed' => 'boolean',
    ];

    public function admissionStatement()
    {
        return $this->belongsTo(AdmissionStatement::class, 'admission_statement_id');
    }

    public function statementDocument()
    {
        return $this->belongsTo(AdmissionStatementDocument::class, 'statement_document_id');
    }

    public function statementItem()
    {
        return $this->belongsTo(AdmissionStatementItem::class, 'statement_item_id');
    }
}
