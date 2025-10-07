<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusEnumToStringInObservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('observations', function (Blueprint $table) {
            $table->string('status')->default('Registered')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observations', function (Blueprint $table) {
            Schema::table('observations', function (Blueprint $table) {
                // rollback ke enum
                $table->enum('status', ['Registered', 'Present', 'Absent', 'Expired'])
                    ->default('Registered')
                    ->change();
            });
        });
    }
}
