<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentInFinancialAgreements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_agreements', function (Blueprint $table) {
            $table->decimal('ittihada_fee', 20, 2)->default(0)->nullable();
            $table->decimal('mhsu_fee', 20, 2)->default(0)->nullable();
            $table->decimal('uniform_fee', 20, 2)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_agreements', function (Blueprint $table) {
            //
        });
    }
}
