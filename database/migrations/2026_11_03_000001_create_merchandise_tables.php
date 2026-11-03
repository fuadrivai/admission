<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseTables extends Migration
{
    public function up()
    {
        Schema::create('merchandise_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('merchandise_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('merchandise_categories')->onDelete('restrict');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['category_id', 'is_active']);
        });

        Schema::create('merchandise_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('merchandise_products')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('color', 80)->default('');
            $table->string('sku', 64)->nullable()->unique();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'name', 'color']);
        });

        Schema::create('merchandise_stock_ins', function (Blueprint $table) {
            $table->id();
            $table->date('stock_in_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->index('stock_in_date');
        });

        Schema::create('merchandise_stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_id')->constrained('merchandise_stock_ins')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('restrict');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();
            $table->index(['variant_id', 'stock_in_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchandise_stock_in_items');
        Schema::dropIfExists('merchandise_stock_ins');
        Schema::dropIfExists('merchandise_variants');
        Schema::dropIfExists('merchandise_products');
        Schema::dropIfExists('merchandise_categories');
    }
}
