<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialDocumentIdToFinancialAgreements extends Migration
{
    public function up()
    {
        Schema::table('financial_agreements', function (Blueprint $table) {
            $table->foreignId('financial_document_id')
                ->nullable()
                ->after('admission_statement_id')
                ->constrained('admission_financial_documents')
                ->restrictOnDelete();
        });
    }

    public function down()
    {
        Schema::table('financial_agreements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('financial_document_id');
        });
    }
}
