<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEnrolmentPriceItemsTable extends Migration
{
    public function up()
    {
        Schema::table('enrolment_prices', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('level_id')
                ->constrained('academic_years')->nullOnDelete();
            $table->foreignId('grade_id')->nullable()->after('academic_year_id')
                ->constrained('grades')->nullOnDelete();
        });

        Schema::create('enrolment_price_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_price_id')
                ->constrained('enrolment_prices')->cascadeOnDelete();
            $table->string('type');
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['type', 'is_required', 'is_active']);
        });

        Schema::table('enrolment_details', function (Blueprint $table) {
            $table->boolean('is_required_charge')->default(false)->after('subtotal');
        });

        $prices = DB::table('enrolment_prices')
            ->join('levels', 'levels.id', '=', 'enrolment_prices.level_id')
            ->select('enrolment_prices.*', 'levels.name as level_name')
            ->get();

        foreach ($prices as $price) {
            $isUpperSecondary = $price->type === 'form'
                && stripos($price->level_name, 'upper secondary') !== false;
            $isLegacyUpperSecondaryTotal = $isUpperSecondary && (float) $price->price === 1450000.0;

            DB::table('enrolment_price_items')->insert([
                [
                    'enrolment_price_id' => $price->id,
                    'type' => $price->type === 'form' ? 'enrolment' : 'other',
                    'name' => $price->type === 'form' ? 'Enrolment Fee' : $price->name,
                    'amount' => $isLegacyUpperSecondaryTotal ? 600000 : $price->price,
                    'is_required' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            if ($isLegacyUpperSecondaryTotal) {
                DB::table('enrolment_price_items')->insert([
                    'enrolment_price_id' => $price->id,
                    'type' => 'streaming_test',
                    'name' => 'Streaming Test',
                    'amount' => 850000,
                    'is_required' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('enrolment_price_items');

        Schema::table('enrolment_details', function (Blueprint $table) {
            $table->dropColumn('is_required_charge');
        });

        Schema::table('enrolment_prices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_year_id');
            $table->dropConstrainedForeignId('grade_id');
        });
    }
}
