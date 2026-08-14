<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('status', [
                'draft',
                'published',
                'closed',
                'archived',
            ])->default('draft');

            $table->text('submit_message')->nullable();
            $table->json('settings')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_forms');
    }
}
