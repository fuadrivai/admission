<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmunizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable();
            $table->boolean('bcg')->default(false);
            $table->boolean('hepatitis')->default(false);
            $table->boolean('dpt')->default(false)->comment('Diphtheria, Pertussis, Tetanus');
            $table->boolean('polio')->default(false);
            $table->boolean('measles')->default(false)->comment('Campak');
            $table->boolean('hepatitis_a')->default(false);
            $table->boolean('mmr')->default(false)->comment('Measles, Mumps, Rubella');
            $table->boolean('influenza')->default(false);
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
        Schema::dropIfExists('immunizations');
    }
}
