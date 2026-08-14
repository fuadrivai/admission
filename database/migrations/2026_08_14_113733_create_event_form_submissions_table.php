<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFormSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_id')
                ->constrained('event_forms')
                ->cascadeOnDelete();
            $table->foreignId('event_form_version_id')
                ->constrained('event_form_versions')
                ->restrictOnDelete();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('status')->default('submitted');
            $table->string('email')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['event_form_id','status']);
            $table->index(['event_form_version_id','submitted_at']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_form_submissions');
    }
}
