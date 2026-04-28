<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegisplaceAndDaraFromToEnrolmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->decimal('custom_payment', 10, 2)->default(0)->after('registration_fee');
            $table->decimal('discount', 10, 2)->default(0)->after('bank_charger');
             $table->string('regis_place')->nullable()->after('source_data');
             $table->string('data_from')->nullable()->after('regis_place');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            //
        });
    }
}
