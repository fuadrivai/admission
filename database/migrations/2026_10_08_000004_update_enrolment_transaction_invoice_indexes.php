<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnrolmentTransactionInvoiceIndexes extends Migration
{
    public function up()
    {
        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->dropUnique('enrolment_transactions_code_unique');
            $table->unique('invoice_id');
        });
    }

    public function down()
    {
        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->dropUnique('enrolment_transactions_invoice_id_unique');
            $table->unique('code');
        });
    }
}
