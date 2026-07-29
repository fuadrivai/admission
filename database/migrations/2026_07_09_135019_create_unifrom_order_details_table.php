<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnifromOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_product_id')->constrained('uniform_products')->onDelete('cascade');
            $table->foreignId('uniform_order_id')->constrained('uniform_orders')->onDelete('cascade');
            $table->string('unit_type');
            $table->string('size')->nullable();
            $table->decimal('qty', 8, 2)->comment('if unit_type = pcs -> qty is integer, if unit_type = meter -> qty is decimal (1.5 meter example)');
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->index(['uniform_product_id', 'uniform_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unifrom_order_details');
    }
}
