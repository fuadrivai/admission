<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseStockInCostCorrectionsTable extends Migration
{
    public function up()
    {
        Schema::create('merchandise_stock_in_cost_corrections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_in_item_id');
            $table->decimal('old_unit_cost', 15, 2);
            $table->decimal('new_unit_cost', 15, 2);
            $table->string('reason', 255);
            $table->unsignedBigInteger('corrected_by')->nullable();
            $table->timestamps();

            $table->index('stock_in_item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchandise_stock_in_cost_corrections');
    }
}
