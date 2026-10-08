<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrolmentDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('enrolment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_transaction_id')
                ->constrained('enrolment_transactions')
                ->cascadeOnDelete();
            $table->string('type');
            $table->string('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrolment_details');
    }
}
