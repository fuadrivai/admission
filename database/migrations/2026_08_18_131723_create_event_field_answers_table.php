<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFieldAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_field_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
            $table->foreignId('event_field_id')->constrained('event_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index(['event_registration_id', 'event_field_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_field_answers');
    }
}
