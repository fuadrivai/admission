<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToEnrolmentDiscountRules extends Migration
{
    public function up()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('registration_place_code');
        });
    }

    public function down()
    {
        Schema::table('enrolment_discount_rules', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
