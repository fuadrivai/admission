<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelReasonToEnrolmentsTable extends Migration
{
    public function up()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'cancelled_at']);
        });
    }
}
