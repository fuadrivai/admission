<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFormEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_form_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_id')->constrained('event_forms')->cascadeOnDelete();
            $table->foreignId('event_form_submission_id')->nullable()->constrained('event_form_submissions')->nullOnDelete();
            $table->foreignId('event_form_email_template_id')->nullable()->constrained('event_form_email_templates')->nullOnDelete();
            $table->string('recipient');
            $table->string('subject');
            $table->enum('status', ['pending','sent','failed',])->default('pending');
            $table->text('error')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
            
            $table->index(['event_form_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_form_email_logs');
    }
}
