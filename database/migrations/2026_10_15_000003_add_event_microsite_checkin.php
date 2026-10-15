<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEventMicrositeCheckin extends Migration
{
    public function up()
    {
        Schema::table('event_microsite_revisions', function (Blueprint $table) {
            $table->foreignId('student_name_field_id')->nullable()->constrained('event_fields')->nullOnDelete();
            $table->foreignId('parent_name_field_id')->nullable()->constrained('event_fields')->nullOnDelete();
        });

        Schema::table('event_invitations', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        Schema::create('event_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_registration_id')->unique()->constrained('event_registrations')->cascadeOnDelete();
            $table->foreignId('event_invitation_id')->nullable()->unique()->constrained('event_invitations')->nullOnDelete();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('source', ['FAMILY', 'STAFF', 'WALK_IN']);
            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->index(['event_id', 'checked_in_at']);
        });
    }

    public function down()
    {
        if (DB::table('event_invitations')->whereNull('email')->exists()) {
            throw new \RuntimeException('Cannot roll back Microsite check-in while walk-in codes without email addresses exist.');
        }

        Schema::dropIfExists('event_checkins');

        Schema::table('event_invitations', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });

        Schema::table('event_microsite_revisions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_name_field_id');
            $table->dropConstrainedForeignId('parent_name_field_id');
        });
    }
}
