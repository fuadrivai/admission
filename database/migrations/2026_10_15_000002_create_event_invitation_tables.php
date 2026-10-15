<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventInvitationTables extends Migration
{
    public function up()
    {
        Schema::create('event_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_registration_id')->unique()->constrained('event_registrations')->cascadeOnDelete();
            $table->string('email');
            $table->char('code_hash', 64);
            $table->text('code_encrypted');
            $table->enum('status', ['READY', 'SENT', 'FAILED'])->default('READY');
            $table->unsignedInteger('send_attempts')->default(0);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'code_hash']);
            $table->index(['event_id', 'status']);
        });

        Schema::create('event_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_invitation_id')->constrained('event_invitations')->cascadeOnDelete();
            $table->string('recipient');
            $table->enum('type', ['INVITATION', 'REMINDER']);
            $table->enum('status', ['SENT', 'FAILED']);
            $table->string('error_code')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'type', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_email_logs');
        Schema::dropIfExists('event_invitations');
    }
}
