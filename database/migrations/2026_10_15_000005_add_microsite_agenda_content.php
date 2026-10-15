<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMicrositeAgendaContent extends Migration
{
    public function up()
    {
        Schema::table('event_microsite_revisions', function (Blueprint $table) {
            $table->longText('rundown_items')->nullable();
            $table->longText('faq_items')->nullable();
            $table->longText('speakers')->nullable();
        });
    }

    public function down()
    {
        Schema::table('event_microsite_revisions', function (Blueprint $table) {
            $table->dropColumn(['rundown_items', 'faq_items', 'speakers']);
        });
    }
}
