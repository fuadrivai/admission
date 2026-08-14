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
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();

            $table->string('status')->default('registered')->index(); // registered|pending|paid
            $table->string('registration_code')->nullable()->unique();

            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->unsignedBigInteger('price_option_id')->nullable();
            $table->string('price_name_snapshot', 120)->nullable();
            $table->unsignedBigInteger('amount_snapshot')->default(0);
            $table->timestamp('active_until')->nullable();

            $table->timestamps();
            $table->index(['event_id','created_at', 'price_option_id']);
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
