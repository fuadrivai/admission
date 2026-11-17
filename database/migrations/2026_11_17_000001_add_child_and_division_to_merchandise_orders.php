<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChildAndDivisionToMerchandiseOrders extends Migration
{
    public function up()
    {
        Schema::table('merchandise_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('merchandise_orders', 'child_name')) {
                $table->string('child_name', 150)->nullable()->after('parent_name');
            }
            if (!Schema::hasColumn('merchandise_orders', 'division_id')) {
                $table->unsignedBigInteger('division_id')->nullable()->after('portal_username');
            }
            if (!Schema::hasColumn('merchandise_orders', 'division_name')) {
                $table->string('division_name')->nullable()->after('division_id');
            }
        });
    }

    public function down()
    {
        $columns = array_filter(['division_id', 'division_name'], function ($column) {
            return Schema::hasColumn('merchandise_orders', $column);
        });

        if ($columns) {
            Schema::table('merchandise_orders', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
}
