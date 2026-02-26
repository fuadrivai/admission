<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospects_id')->constrained()->cascadeOnDelete();
            $table->morphs('activityable');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index('prospects_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospect_activities');
    }
}
