<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('age')->comment('e.g.: 5 years 3 months, age when applying')->nullable();
            $table->decimal('height', 10, 2)->nullable()->default(0)->comment('in cm');
            $table->decimal('weight', 10, 2)->nullable()->default(0)->comment('in kg');
            $table->string('religion')->nullable();
            $table->string('ethnicity')->nullable();
            $table->string('citizenship')->nullable();
            $table->integer('siblings_count')->default(0);
            $table->integer('birth_order')->default(0);
            $table->string('home_language')->nullable();
            $table->string('other_languages')->nullable();
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('living_with')->nullable();
            $table->string('living_with_other')->nullable()->comment('if living_with is other, please specify');
            $table->decimal('distance_km', 10, 2)->nullable()->default(0)->comment('in km');
            $table->string('previous_school')->nullable();
            $table->string('previous_school_address')->nullable();
            $table->string('graduation_year')->nullable();
            $table->boolean('ever_not_school')->default(false);
            $table->string('not_school_duration')->comment('e.g.: 3 months, 1 year')->nullable();
            $table->string('not_school_reason')->comment('e.g.: ')->nullable();
            $table->boolean('dev_check')->default(false);
            $table->string('dev_diagnosis')->comment('if dev_check is true, please specify')->nullable();
            $table->boolean('therapy_history')->default(false);
            $table->string('therapy_detail')->comment('if therapy_history is true, please specify')->nullable();
            $table->string('emergency_contact_priority')->nullable();
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
        Schema::dropIfExists('applicants');
    }
}
