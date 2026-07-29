<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUniformOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('student_name');
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('parent_email');
            $table->timestamp('order_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('branch_name');
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->string('level_name');
            $table->foreignId('grade_id')->constrained('grades')->onDelete('cascade');
            $table->string('grade_name');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('bank_charger', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->string('payment_status')->nullable()->default('unpaid'); 
            $table->string('order_link')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('expired_date_va')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uniform_orders');
    }
}
