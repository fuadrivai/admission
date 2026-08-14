<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRegistrationAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_registration_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')
                ->constrained('event_registrations')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('field_id')->nullable()->index();
            $table->string('field_key')->nullable()->index();
            $table->string('field_label')->nullable();
            $table->string('field_type')->nullable();

            $table->longText('value')->nullable();
            $table->timestamps();

            $table->index(['registration_id','field_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_registration_answers');
    }
}
