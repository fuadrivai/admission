<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventMicrositeTables extends Migration
{
    public function up()
    {
        Schema::create('event_microsites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained('events')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('event_microsite_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_microsite_id')->constrained('event_microsites')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'SUPERSEDED'])->default('DRAFT');
            $table->string('title');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('location')->nullable();
            $table->string('map_url', 2048)->nullable();
            $table->string('primary_color', 7)->default('#174A83');
            $table->string('secondary_color', 7)->default('#D9B66F');
            $table->longText('rundown')->nullable();
            $table->longText('faq')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['event_microsite_id', 'version']);
            $table->index(['event_microsite_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_microsite_revisions');
        Schema::dropIfExists('event_microsites');
    }
}
