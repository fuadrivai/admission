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
            $table->dateTime('valid_from')->nullable()->after('quota');
        });

        DB::table('enrolment_discount_rules')
            ->whereNotNull('valid_date')
            ->update(['valid_from' => DB::raw('DATE(valid_date)')]);
    }

    public function down()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->dropColumn('valid_from');
        });
    }
};
