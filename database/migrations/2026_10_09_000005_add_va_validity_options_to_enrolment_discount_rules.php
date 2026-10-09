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
            $table->string('va_validity_type', 10)->default('days')->after('va_valid_date');
            $table->unsignedSmallInteger('va_valid_days')->nullable()->after('va_validity_type');
        });

        DB::table('enrolment_discount_rules')
            ->whereNotNull('va_valid_date')
            ->update(['va_validity_type' => 'date']);
    }

    public function down()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->dropColumn(['va_validity_type', 'va_valid_days']);
        });
    }
};
