<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPicAndParentToUniformOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniform_orders', function (Blueprint $table) {
            $table->string('picked_up_name')->nullable()->after('picked_up_by')->comment('who picked up the uniform');
            $table->string('pic_name')->nullable()->after('picked_up_name')->comment('name of the person who give the uniform to parent');
            $table->text('note')->nullable()->after('pic_name')->comment('note of the person who give the uniform to parent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniform_orders', function (Blueprint $table) {
            //
        });
    }
}
