<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionFinancialDocumentsTables extends Migration
{
    public function up()
    {
        Schema::create('admission_financial_documents', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('financial');
            $table->string('name');
            $table->string('version');
            $table->string('status')->default('DRAFT');
            $table->date('effective_at')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['type', 'version']);
            $table->index(['type', 'status']);
        });

        Schema::create('admission_financial_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('admission_financial_documents')->cascadeOnDelete();
            $table->string('title_en');
            $table->string('title_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('document_id');
            $table->index('sort_order');
        });

        Schema::create('admission_financial_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('admission_financial_sections')->cascadeOnDelete();
            $table->integer('number')->default(1);
            $table->longText('text_en');
            $table->longText('text_id');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->index(['section_id', 'sort_order']);
        });

        app(\Database\Seeders\FinancialDocumentSeeder::class)->run();
    }

    public function down()
    {
        Schema::dropIfExists('admission_financial_items');
        Schema::dropIfExists('admission_financial_sections');
        Schema::dropIfExists('admission_financial_documents');
    }
}
