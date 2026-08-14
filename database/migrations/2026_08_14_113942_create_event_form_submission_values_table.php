<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFormSubmissionValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_form_submission_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_submission_id')
                ->constrained('event_form_submissions')
                ->cascadeOnDelete();
            $table->foreignId('event_form_field_id')
                ->constrained('event_form_fields')
                ->restrictOnDelete();
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['event_form_submission_id','event_form_field_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_form_submission_values');
    }
}
