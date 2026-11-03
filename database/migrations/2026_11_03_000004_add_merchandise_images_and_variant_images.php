<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchandiseImagesAndVariantImages extends Migration
{
    public function up()
    {
        Schema::create('merchandise_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('merchandise_products')->onDelete('cascade');
            $table->string('image_path');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'display_order']);
        });

        Schema::table('merchandise_variants', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('sku');
        });

    }

    public function down()
    {
        Schema::table('merchandise_variants', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::dropIfExists('merchandise_product_images');
    }
}
