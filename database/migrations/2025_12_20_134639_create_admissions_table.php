<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id');
            $table->foreignId('enrolment_id');
            $table->foreignId('branch_id');
            $table->foreignId('level_id');
            $table->foreignId('grade_id');
            $table->string('accademic_year');
            $table->string('code')->comment('code from prospect');
            $table->boolean('is_complete')->default(false)->comment('if it`s true, data cannot be update');
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
        Schema::dropIfExists('admissions');
    }
}
