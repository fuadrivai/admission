<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventWalkInTokensTable extends Migration
{
    public function up()
    {
        Schema::create('event_walk_in_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->char('token_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'token_hash']);
            $table->index(['event_id', 'expires_at', 'used_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_walk_in_tokens');
    }
}
