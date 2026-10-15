<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmationEmailSentAtToEnrolmentTransactions extends Migration
{
    public function up()
    {
        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->dateTime('confirmation_email_sent_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->dropColumn('confirmation_email_sent_at');
        });
    }
}
