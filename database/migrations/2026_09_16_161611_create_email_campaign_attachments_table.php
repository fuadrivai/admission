<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampaignAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_campaign_attachments', function (Blueprint $table) {
             $table->id();

            $table->foreignId('campaign_id')
                ->constrained('email_campaigns')
                ->cascadeOnDelete();

            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');

            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->timestamps();

            $table->index('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_campaign_attachments');
    }
}
