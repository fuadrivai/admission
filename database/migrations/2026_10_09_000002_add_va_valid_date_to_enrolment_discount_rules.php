<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->dateTime('va_valid_date')->nullable()->after('valid_date');
        });
    }

    public function down()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->dropColumn('va_valid_date');
        });
    }
};
