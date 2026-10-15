<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDetailBankChargeAndAllowPartialProspects extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('enrolment_details', 'bank_charge')) {
            Schema::table('enrolment_details', function (Blueprint $table) {
                $table->dropColumn('bank_charge');
            });
        }

        Schema::table('prospects', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
            $table->string('place_of_birth')->nullable()->change();
            $table->string('current_school')->nullable()->change();
            $table->string('zipcode')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('relationship')->nullable()->change();
        });
    }

    public function down()
    {
        if (!Schema::hasColumn('enrolment_details', 'bank_charge')) {
            Schema::table('enrolment_details', function (Blueprint $table) {
                $table->decimal('bank_charge', 12, 2)->default(0);
            });
        }

        Schema::table('prospects', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('place_of_birth')->nullable(false)->change();
            $table->string('current_school')->nullable(false)->change();
            $table->string('zipcode')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('relationship')->nullable(false)->change();
        });
    }
}
