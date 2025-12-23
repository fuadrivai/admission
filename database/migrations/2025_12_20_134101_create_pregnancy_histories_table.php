<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePregnancyHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pregnancy_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable();
            $table->boolean('medication_during_pregnancy')->default(false);
            $table->string('medication_note')->comment('if medication_during_pregnancy is true, please specify')->nullable();
            $table->boolean('illness_during_pregnancy')->default(false);
            $table->string('illness_note')->comment('if illness_during_pregnancy is true, please specify')->nullable();
            $table->string('gestational_age')->nullable();
            $table->enum('delivery_method', ['normal', 'cesarean'])->nullable();
            $table->decimal('birth_weight', 10, 2)->default(0)->comment('in kilograms');
            $table->decimal('birth_height', 10, 2)->default(0)->comment('in centimeters');
            $table->integer('walk_age_month')->comment('in months')->default(0);
            $table->integer('talk_age_month')->comment('in months')->default(0);
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
        Schema::dropIfExists('pregnancy_histories');
    }
}
