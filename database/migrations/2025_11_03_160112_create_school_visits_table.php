<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_visits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('School Visit Code');
            $table->string('parent_name');
            $table->string('phone_number');
            $table->string('email')->comment("parent's email");
            $table->string('zipcode');
            $table->string('city')->nullable()->comment('get from zipcode');
            $table->string('district')->nullable()->comment('get from zipcode');
            $table->string('subdistrict')->nullable()->comment('get from zipcode');
            $table->string('child_name');
            $table->foreignId('branch_id');
            $table->string('branch_name');
            $table->foreignId('level_id')->comment('preschool,primary,secondary,dc');
            $table->string('level_name');
            $table->foreignId('grade_id')->comment('grade 1,2,3,4,5,6');
            $table->string('grade_name');
            $table->string('academic_year')->comment('2024/2025, 2025/2026');
            $table->string('current_school');
            $table->string('info_from')->comment('How did you hear about booking schedules for our School Visits?');
            $table->string('info_from_message')->nullable()->comment('it will filled if choose other form info_form field');
            $table->date('date');
            $table->time('time');
            $table->integer('number_visitor')->comment('example : 2 people, 3 people');
            $table->enum('already_enrol', ['yes', 'no', 'will']);
            $table->json('roles')->comment('I declare that I am willing to follow the school rules ');
            $table->enum('status', ['present', 'registered','absent','cancelled']);
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
        Schema::dropIfExists('school_visits');
    }
}
