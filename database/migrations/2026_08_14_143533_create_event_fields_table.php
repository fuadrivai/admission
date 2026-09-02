<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete()->index();

            $table->string('field_key');
            $table->string('label');
            $table->string('type');
            $table->boolean('is_required')->default(false);
            $table->json('options_json')->nullable();   // select/radio/checkbox
            $table->unsignedInteger('order_index')->default(0)->index();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary_email')->default(false);

            $table->unique(['event_id', 'field_key']);  // anti bentrok
            $table->index(['event_id', 'order_index']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_fields');
    }
}
