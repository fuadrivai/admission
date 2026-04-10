<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeZipcodeAndAddAddressToSchoolvisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_visits', function (Blueprint $table) {
            $table->string('zipcode')->nullable()->change();
            $table->text('address')->nullable()->after('subdistrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_visits', function (Blueprint $table) {
            $table->string('zipcode')->nullable(false)->change();
            $table->dropColumn('address');
        });
    }
}
