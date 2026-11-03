<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseCartAndOrderTables extends Migration
{
    public function up()
    {
        Schema::create('merchandise_carts', function (Blueprint $table) {
            $table->id();
            $table->string('cart_key', 100)->unique();
            $table->timestamps();
        });

        Schema::create('merchandise_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('merchandise_carts')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(['cart_id', 'variant_id']);
        });

        Schema::create('merchandise_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('external_id', 64)->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->string('branch_name', 100);
            $table->string('parent_name', 150);
            $table->string('email', 150);
            $table->string('phone', 40);
            $table->string('portal_username', 100);
            $table->unsignedInteger('total_items');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_status', 20)->default('PENDING');
            $table->text('payment_url')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->index(['payment_status', 'expires_at']);
        });

        Schema::create('merchandise_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('merchandise_orders')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('restrict');
            $table->string('product_name', 150);
            $table->string('variant_name', 180);
            $table->string('sku', 64)->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
            $table->index(['variant_id', 'order_id']);
        });

        Schema::create('merchandise_stock_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->unique()->constrained('merchandise_orders')->onDelete('set null');
            $table->dateTime('stock_out_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('merchandise_stock_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_id')->constrained('merchandise_stock_outs')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('restrict');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->timestamps();
            $table->index(['variant_id', 'stock_out_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchandise_stock_out_items');
        Schema::dropIfExists('merchandise_stock_outs');
        Schema::dropIfExists('merchandise_order_items');
        Schema::dropIfExists('merchandise_orders');
        Schema::dropIfExists('merchandise_cart_items');
        Schema::dropIfExists('merchandise_carts');
    }
}
