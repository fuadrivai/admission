<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('observation_time_id')->constrained()->onDelete('cascade');
            $table->string('child_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('level');
            $table->foreignId('level_id')->constrained()->onDelete('cascade');
            $table->string('grade');
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['Registered', 'Present', 'Absent'])->comment('registration status');
            $table->string('parent_name');
            $table->enum('relationship', ['Ayah', 'Ibu', 'Wali']);
            $table->string('phone');
            $table->string('email');
            $table->date('date');
            $table->time('time');
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
        Schema::dropIfExists('observations');
    }
}
