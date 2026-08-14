<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventPriceOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_price_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            $table->string('code', 50);
            $table->string('name', 120);
            $table->unsignedBigInteger('amount');
            $table->string('currency', 10)->default('IDR');

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order_index')->default(0);

            // opsional (boleh dipakai nanti)
            $table->unsignedInteger('quota')->nullable();
            $table->unsignedInteger('sold_count')->default(0);
            $table->timestamp('sales_start_at')->nullable();
            $table->timestamp('sales_end_at')->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'code']);
            $table->index(['event_id', 'is_active']);
            $table->index(['event_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_price_options');
    }
}
