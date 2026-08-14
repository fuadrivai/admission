<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFormVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_form_versions', function (Blueprint $table) {
            Schema::create('event_form_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_id')->constrained('event_forms')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('name')->nullable();
            $table->boolean('is_published')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
            $table->unique(['event_form_id','version']);
        });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_form_versions');
    }
}
