<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrolmentTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('enrolment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->nullable()->constrained('enrolments')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('invoice_id')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('bank_charge', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_status')->default('pending');
            $table->dateTime('create_va_date')->nullable();
            $table->dateTime('expiry_va_date')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('payment_place')->nullable();
            $table->string('source')->nullable();
            $table->text('noted')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('request_key')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrolment_transactions');
    }
}
