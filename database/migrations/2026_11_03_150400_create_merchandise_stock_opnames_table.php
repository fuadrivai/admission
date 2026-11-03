<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseStockOpnamesTable extends Migration
{
    public function up()
    {
        Schema::create('merchandise_stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->date('opname_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->index('opname_date');
        });

        Schema::create('merchandise_stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_id')->constrained('merchandise_stock_opnames')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('restrict');
            $table->unsignedInteger('system_quantity');
            $table->unsignedInteger('counted_quantity');
            $table->integer('difference');
            $table->timestamps();
            $table->unique(['opname_id', 'variant_id'], 'stock_opname_variant_unique');
        });

        Schema::table('merchandise_stock_ins', function (Blueprint $table) {
            $table->foreignId('stock_opname_id')
                ->nullable()
                ->unique('stock_in_opname_unique')
                ->constrained('merchandise_stock_opnames')
                ->onDelete('set null');
        });

        Schema::table('merchandise_stock_outs', function (Blueprint $table) {
            $table->foreignId('stock_opname_id')
                ->nullable()
                ->unique('stock_out_opname_unique')
                ->constrained('merchandise_stock_opnames')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('merchandise_stock_outs', function (Blueprint $table) {
            $table->dropForeign(['stock_opname_id']);
            $table->dropUnique('stock_out_opname_unique');
            $table->dropColumn('stock_opname_id');
        });

        Schema::table('merchandise_stock_ins', function (Blueprint $table) {
            $table->dropForeign(['stock_opname_id']);
            $table->dropUnique('stock_in_opname_unique');
            $table->dropColumn('stock_opname_id');
        });

        Schema::dropIfExists('merchandise_stock_opname_items');
        Schema::dropIfExists('merchandise_stock_opnames');
    }
}
