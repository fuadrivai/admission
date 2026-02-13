<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProspectsMakeNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->string('relationship')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('place_of_birth')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->string('relationship')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('place_of_birth')->nullable(false)->change();
        });
    }
}
