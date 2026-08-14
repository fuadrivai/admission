<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_version_id')
                ->constrained('event_form_versions')
                ->cascadeOnDelete();
            $table->string('type');
            $table->string('name');
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('default_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('width')->default('full');
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->index(['event_form_version_id','sort_order']);
            $table->unique(['event_form_version_id','name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_form_fields');
    }
}
