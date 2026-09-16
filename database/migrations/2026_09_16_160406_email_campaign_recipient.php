<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailCampaignRecipient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_campaign_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')->constrained('email_campaigns')->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->json('data');
            $table->enum('status', [
                'PENDING',
                'PROCESSING',
                'SENT',
                'FAILED',
            ])->default('PENDING');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['campaign_id','status']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_campaign_recipients');
    }
}
