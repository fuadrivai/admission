<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandisePromotionGiftOptions extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('merchandise_promotion_gift_options')) {
            Schema::create('merchandise_promotion_gift_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promotion_id')
                    ->constrained('merchandise_promotions')
                    ->onDelete('cascade');
                $table->foreignId('variant_id')
                    ->constrained('merchandise_variants')
                    ->onDelete('restrict');
                $table->unsignedInteger('max_quantity');
                $table->timestamps();
                $table->unique(['promotion_id', 'variant_id'], 'merch_promo_gift_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('merchandise_promotion_gift_options');
    }
}
