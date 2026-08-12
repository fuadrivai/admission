<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupFieldsToUniformOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('uniform_orders', function (Blueprint $table) {
            $table->timestamp('picked_up_at')->nullable()->after('payment_date');
            $table->unsignedBigInteger('picked_up_by')->nullable()->after('picked_up_at');
        });
    }

    public function down()
    {
        Schema::table('uniform_orders', function (Blueprint $table) {
            $table->dropColumn(['picked_up_at', 'picked_up_by']);
        });
    }
}
