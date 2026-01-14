<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statement_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_statement_id');
            $table->string('type')->comment('Parent, Guardian, Narcotica, Student');
            $table->boolean('agreed')->default(false);
            $table->timestamp('agreed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statement_agreements');
    }
}
