<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentItemMetadataToStatementAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statement_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('statement_agreements', 'statement_document_id')) {
                $table->foreignId('statement_document_id')->nullable()->constrained('admission_statement_documents')->nullOnDelete();
            }

            if (!Schema::hasColumn('statement_agreements', 'statement_item_id')) {
                $table->foreignId('statement_item_id')->nullable()->constrained('admission_statement_items')->nullOnDelete();
            }

            if (!Schema::hasColumn('statement_agreements', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }

            if (!Schema::hasColumn('statement_agreements', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statement_agreements', function (Blueprint $table) {
            if (Schema::hasColumn('statement_agreements', 'statement_document_id')) {
                $table->dropConstrainedForeignId('statement_document_id');
            }

            if (Schema::hasColumn('statement_agreements', 'statement_item_id')) {
                $table->dropConstrainedForeignId('statement_item_id');
            }

            if (Schema::hasColumn('statement_agreements', 'ip_address')) {
                $table->dropColumn('ip_address');
            }

            if (Schema::hasColumn('statement_agreements', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
        });
    }
}
