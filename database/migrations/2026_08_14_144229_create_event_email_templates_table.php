<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_email_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('key', 50); // INVOICE_CREATED, PAYMENT_PAID, etc
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
        
            $table->unique(['event_id', 'key']);
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_email_templates');
    }
}
