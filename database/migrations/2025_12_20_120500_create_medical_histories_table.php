<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable();
            $table->boolean('surgery_history')->default(false);
            $table->string('surgery_note')->comment('if surgery_history is true, please specify')->nullable();
            $table->boolean('hospitalization_history')->default(false);
            $table->string('hospitalization_note')->comment('if hospitalization_history is true, please specify')->nullable();
            $table->boolean('seizure_history')->default(false);
            $table->string('seizure_note')->comment('if seizure_history is true, please specify')->nullable();
            $table->boolean('accident_history')->default(false);
            $table->string('accident_note')->comment('if accident_history is true, please specify')->nullable();
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
        Schema::dropIfExists('medical_histories');
    }
}
