<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('healths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', 'Unknown'])->nullable();
            $table->boolean('food_allergy')->default(false);
            $table->string('food_allergy_note')->comment('if food_allergy is true, please specify')->nullable();
            $table->boolean('drug_allergy')->default(false);
            $table->string('drug_allergy_note')->comment('if drug_allergy is true, please specify')->nullable();
            $table->boolean('uses_glasses')->default(false);
            $table->boolean('uses_hearing_aid')->default(false);
            $table->boolean('routine_medication')->default(false);
            $table->string('routine_medication_note')->comment('if routine_medication is true, please specify')->nullable();
            $table->string('family_disease_history')->nullable();
            $table->string('referral_health_facility')->nullable();
            $table->string('emergency_contact')->nullable();
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
        Schema::dropIfExists('healths');
    }
}
