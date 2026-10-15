<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->dateTime('valid_date')->nullable()->change();
            $table->dateTime('va_valid_date')->nullable()->change();
        });

        DB::table('enrolment_discount_rules')
            ->whereNotNull('valid_date')
            ->whereTime('valid_date', '00:00:00')
            ->update(['valid_date' => DB::raw("DATE_ADD(valid_date, INTERVAL 86399 SECOND)")]);
    }

    public function down()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->date('valid_date')->nullable()->change();
            $table->date('va_valid_date')->nullable()->change();
        });
    }
};
