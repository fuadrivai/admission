<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionStatementSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admission_statement_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('admission_statement_documents')->cascadeOnDelete();
            $table->string('title_en');
            $table->string('title_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('document_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admission_statement_sections');
    }
}
