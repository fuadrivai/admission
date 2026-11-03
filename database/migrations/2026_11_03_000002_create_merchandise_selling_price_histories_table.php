<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseSellingPriceHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('merchandise_selling_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('cascade');
            $table->decimal('old_price', 12, 2)->nullable();
            $table->decimal('new_price', 12, 2);
            $table->decimal('average_unit_cost', 12, 2)->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reason', 500)->nullable();
            $table->timestamps();
            $table->index(['variant_id', 'created_at']);
        });

        $now = now();
        foreach (DB::table('merchandise_variants')->orderBy('id')->get(['id', 'price']) as $variant) {
            $stock = DB::table('merchandise_stock_in_items')
                ->where('variant_id', $variant->id)
                ->selectRaw('COALESCE(SUM(quantity), 0) as quantity, COALESCE(SUM(quantity * unit_cost), 0) as total_cost')
                ->first();

            DB::table('merchandise_selling_price_histories')->insert([
                'variant_id' => $variant->id,
                'old_price' => null,
                'new_price' => $variant->price,
                'average_unit_cost' => $stock->quantity ? round($stock->total_cost / $stock->quantity, 2) : null,
                'changed_by' => null,
                'reason' => 'Riwayat harga awal dicatat saat aktivasi Selling Price Management.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('merchandise_selling_price_histories');
    }
}
