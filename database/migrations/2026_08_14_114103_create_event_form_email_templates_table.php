<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFormEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_form_email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_id')
                ->constrained('event_forms')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('confirmation');
            $table->string('subject');
            $table->longText('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['event_form_id','type','is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_form_email_templates');
    }
}
