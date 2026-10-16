<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMergeTrackingToEnrolments extends Migration
{
    public function up()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->foreignId('merged_into_enrolment_id')
                ->nullable()
                ->after('cancelled_at')
                ->constrained('enrolments')
                ->restrictOnDelete();
            $table->timestamp('merged_at')->nullable()->after('merged_into_enrolment_id');
        });
    }

    public function down()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropForeign(['merged_into_enrolment_id']);
            $table->dropColumn(['merged_into_enrolment_id', 'merged_at']);
        });
    }
}
