<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchandiseOnlineStoreSettings extends Migration
{
    public function up()
    {
        Schema::table('merchandise_categories', function (Blueprint $table) {
            $table->boolean('is_store_visible')->default(true)->after('is_active');
            $table->unsignedInteger('display_order')->default(0)->after('is_store_visible');
        });

        Schema::table('merchandise_products', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('is_active');
            $table->unsignedInteger('display_order')->default(0)->after('is_published');
            $table->dateTime('sale_start_at')->nullable()->after('display_order');
            $table->dateTime('sale_end_at')->nullable()->after('sale_start_at');
            $table->index(['is_published', 'is_active', 'display_order'], 'merchandise_products_store_index');
        });

        Schema::table('merchandise_variants', function (Blueprint $table) {
            $table->unsignedInteger('online_stock_limit')->nullable()->after('low_stock_threshold');
        });

        Schema::create('merchandise_store_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('store_name', 150)->default('MHIS Merchandise Store');
            $table->text('description')->nullable();
            $table->boolean('is_open')->default(false);
            $table->string('closed_message', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchandise_store_settings');

        Schema::table('merchandise_variants', function (Blueprint $table) {
            $table->dropColumn('online_stock_limit');
        });

        Schema::table('merchandise_products', function (Blueprint $table) {
            $table->dropIndex('merchandise_products_store_index');
            $table->dropColumn(['is_published', 'display_order', 'sale_start_at', 'sale_end_at']);
        });

        Schema::table('merchandise_categories', function (Blueprint $table) {
            $table->dropColumn(['is_store_visible', 'display_order']);
        });
    }
}
