<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillMerchandisePromotionGiftOptions extends Migration
{
    public function up()
    {
        $promotions = DB::table('merchandise_promotions')
            ->where('type', 'buy_x_get_y')
            ->get(['id']);

        foreach ($promotions as $promotion) {
            $variantIds = DB::table('merchandise_promotion_variants')
                ->where('promotion_id', $promotion->id)
                ->pluck('variant_id');

            if ($variantIds->isEmpty()) {
                $variantIds = DB::table('merchandise_variants')
                    ->join('merchandise_products', 'merchandise_products.id', '=', 'merchandise_variants.product_id')
                    ->where('merchandise_variants.is_active', true)
                    ->where('merchandise_products.is_active', true)
                    ->pluck('merchandise_variants.id');
            }

            foreach ($variantIds as $variantId) {
                $exists = DB::table('merchandise_promotion_gift_options')
                    ->where('promotion_id', $promotion->id)
                    ->where('variant_id', $variantId)
                    ->exists();
                if ($exists) {
                    continue;
                }

                $stockIn = (int) DB::table('merchandise_stock_in_items')
                    ->where('variant_id', $variantId)
                    ->sum('quantity');
                DB::table('merchandise_promotion_gift_options')->insert([
                    'promotion_id' => $promotion->id,
                    'variant_id' => $variantId,
                    'max_quantity' => min(4294967295, max(1, $stockIn)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
    }
}
