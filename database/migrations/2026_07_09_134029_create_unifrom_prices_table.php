<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnifromPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_product_id')->constrained('uniform_products')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('size')->nullable()->comment('if has_size is true : XS,S,M,L,XL,XXL,XXXL,OTHER(custom size),if false : NULL');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['uniform_product_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unifrom_prices');
    }
}
