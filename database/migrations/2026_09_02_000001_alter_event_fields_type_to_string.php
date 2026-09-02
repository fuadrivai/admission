<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterEventFieldsTypeToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE event_fields MODIFY type VARCHAR(50) NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE event_fields MODIFY type ENUM('text','textarea','select','radio','checkbox','email','phone','number','date') NOT NULL");
    }
}
