<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('child_name');
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('current_school');
            $table->string('parent_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('zipcode');
            $table->string('address');
            $table->string('relationship');
            $table->string('source_module')->comment('Indicates whether the prospect is "school_visit" or "enrolment" source');
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
        Schema::dropIfExists('prospects');
    }
}
