<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankChargerToMerchandiseOrders extends Migration
{
    public function up()
    {
        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->decimal('bank_charger', 12, 2)->default(0)->after('subtotal');
        });
    }

    public function down()
    {
        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->dropColumn('bank_charger');
        });
    }
}
