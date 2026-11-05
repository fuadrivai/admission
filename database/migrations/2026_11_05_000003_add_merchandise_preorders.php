<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchandisePreorders extends Migration
{
    public function up()
    {
        Schema::table('merchandise_variants', function (Blueprint $table) {
            $table->boolean('is_preorder')->default(false);
            $table->date('preorder_expected_at')->nullable();
        });

        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->string('order_type', 20)->default('READY');
            $table->string('fulfillment_status', 30)->default('READY');
            $table->date('preorder_expected_at')->nullable();
            $table->dateTime('fulfilled_at')->nullable();
            $table->foreignId('fulfilled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->index(['order_type', 'payment_status', 'fulfillment_status'], 'merchandise_preorder_status_index');
        });

        Schema::table('merchandise_order_items', function (Blueprint $table) {
            $table->boolean('is_preorder')->default(false);
            $table->date('preorder_expected_at')->nullable();
        });

        Schema::create('merchandise_preorder_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')
                ->constrained('merchandise_order_items')
                ->onDelete('cascade');
            $table->foreignId('stock_in_item_id')
                ->constrained('merchandise_stock_in_items')
                ->onDelete('restrict');
            $table->unsignedInteger('quantity');
            $table->foreignId('allocated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['order_item_id', 'stock_in_item_id'], 'merchandise_preorder_allocation_unique');
            $table->index('stock_in_item_id', 'merchandise_preorder_stockin_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchandise_preorder_allocations');

        Schema::table('merchandise_order_items', function (Blueprint $table) {
            $table->dropColumn(['is_preorder', 'preorder_expected_at']);
        });

        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->dropForeign(['fulfilled_by']);
            $table->dropIndex('merchandise_preorder_status_index');
            $table->dropColumn([
                'order_type',
                'fulfillment_status',
                'preorder_expected_at',
                'fulfilled_at',
                'fulfilled_by',
            ]);
        });

        Schema::table('merchandise_variants', function (Blueprint $table) {
            $table->dropColumn(['is_preorder', 'preorder_expected_at']);
        });
    }
}
