<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSelectedToMerchandiseCartItems extends Migration
{
    public function up()
    {
        Schema::table('merchandise_cart_items', function (Blueprint $table) {
            $table->boolean('is_selected')->default(true)->after('quantity');
        });
    }

    public function down()
    {
        Schema::table('merchandise_cart_items', function (Blueprint $table) {
            $table->dropColumn('is_selected');
        });
    }
}
