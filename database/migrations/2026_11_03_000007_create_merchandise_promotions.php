<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMerchandisePromotions extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('merchandise_promotions')) {
            Schema::create('merchandise_promotions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('code', 50)->nullable()->unique();
                $table->string('type', 30);
                $table->string('discount_type', 20)->nullable();
                $table->decimal('discount_value', 12, 2)->default(0);
                $table->decimal('minimum_purchase', 12, 2)->nullable();
                $table->unsignedInteger('min_quantity')->nullable();
                $table->unsignedInteger('buy_quantity')->nullable();
                $table->unsignedInteger('get_quantity')->nullable();
                $table->foreignId('gift_variant_id')->nullable()->constrained('merchandise_variants')->onDelete('restrict');
                $table->decimal('bundle_price', 12, 2)->nullable();
                $table->dateTime('valid_from')->nullable();
                $table->dateTime('valid_until')->nullable();
                $table->unsignedInteger('usage_limit')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['is_active', 'valid_from', 'valid_until']);
                $table->index('type');
            });
        }

        if (!Schema::hasTable('merchandise_promotion_variants')) {
            Schema::create('merchandise_promotion_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promotion_id')->constrained('merchandise_promotions')->onDelete('cascade');
                $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['promotion_id', 'variant_id'], 'merch_promo_variant_unique');
            });
        }

        if (!Schema::hasTable('merchandise_promotion_bundle_items')) {
            Schema::create('merchandise_promotion_bundle_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promotion_id')->constrained('merchandise_promotions')->onDelete('cascade');
                $table->foreignId('variant_id')->constrained('merchandise_variants')->onDelete('restrict');
                $table->unsignedInteger('quantity');
                $table->timestamps();
                $table->unique(['promotion_id', 'variant_id'], 'merch_bundle_item_unique');
            });
        }

        if (!$this->hasUniqueIndex('merchandise_promotion_variants', ['promotion_id', 'variant_id'])) {
            Schema::table('merchandise_promotion_variants', function (Blueprint $table) {
                $table->unique(['promotion_id', 'variant_id'], 'merch_promo_variant_unique');
            });
        }
        if (!$this->hasUniqueIndex('merchandise_promotion_bundle_items', ['promotion_id', 'variant_id'])) {
            Schema::table('merchandise_promotion_bundle_items', function (Blueprint $table) {
                $table->unique(['promotion_id', 'variant_id'], 'merch_bundle_item_unique');
            });
        }

        if (!Schema::hasColumn('merchandise_orders', 'promotion_id')) {
            Schema::table('merchandise_orders', function (Blueprint $table) {
                $table->foreignId('promotion_id')->nullable()
                    ->after('bank_charger')
                    ->constrained('merchandise_promotions')
                    ->onDelete('set null');
            });
        }
        if (!Schema::hasColumn('merchandise_orders', 'promotion_name')) {
            Schema::table('merchandise_orders', function (Blueprint $table) {
                $table->string('promotion_name', 150)->nullable()->after('promotion_id');
            });
        }
        if (!Schema::hasColumn('merchandise_orders', 'promotion_code')) {
            Schema::table('merchandise_orders', function (Blueprint $table) {
                $table->string('promotion_code', 50)->nullable()->after('promotion_name');
            });
        }
        if (!Schema::hasColumn('merchandise_orders', 'discount_amount')) {
            Schema::table('merchandise_orders', function (Blueprint $table) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('promotion_code');
            });
        }
    }

    public function down()
    {
        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
            $table->dropColumn(['promotion_id', 'promotion_name', 'promotion_code', 'discount_amount']);
        });

        Schema::dropIfExists('merchandise_promotion_bundle_items');
        Schema::dropIfExists('merchandise_promotion_variants');
        Schema::dropIfExists('merchandise_promotions');
    }

    private function hasUniqueIndex(string $table, array $columns): bool
    {
        $indexes = DB::select('SHOW INDEX FROM `' . $table . '`');
        $uniqueIndexes = [];

        foreach ($indexes as $index) {
            if ((int) $index->Non_unique === 0) {
                $uniqueIndexes[$index->Key_name][(int) $index->Seq_in_index] = $index->Column_name;
            }
        }

        foreach ($uniqueIndexes as $indexColumns) {
            ksort($indexColumns);
            if (array_values($indexColumns) === $columns) {
                return true;
            }
        }

        return false;
    }
}
