<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('code')->nullable()->unique();
            $table->enum('status', ['SUBMITTED', 'PENDING','PAID','CANCELLED','EXPIRED',])->default('SUBMITTED');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
            $table->index(['event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_registrations');
    }
}
