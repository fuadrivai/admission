<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionStatementItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admission_statement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('admission_statement_sections')->cascadeOnDelete();
            $table->integer('number');
            $table->longText('text_en');
            $table->longText('text_id');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->index('section_id');
            $table->index(['section_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admission_statement_items');
    }
}
