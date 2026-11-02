<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGoogleSheetMastersTable extends Migration
{
    public function up()
    {
        Schema::create('google_sheet_masters', function (Blueprint $table) {
            $table->id();
            $table->string('integration_key')->unique();
            $table->string('name');
            $table->text('google_sheet_url')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('google_sheet_masters')->insert([
            ['integration_key' => 'school_visit', 'name' => 'School Visit', 'created_at' => $now, 'updated_at' => $now],
            ['integration_key' => 'enrolment', 'name' => 'Enrolment', 'created_at' => $now, 'updated_at' => $now],
            ['integration_key' => 'uniform', 'name' => 'Uniform', 'created_at' => $now, 'updated_at' => $now],
            ['integration_key' => 'observation', 'name' => 'Observation', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('google_sheet_masters');
    }
}
