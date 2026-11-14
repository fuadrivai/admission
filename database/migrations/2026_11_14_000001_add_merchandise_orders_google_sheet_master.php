<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMerchandiseOrdersGoogleSheetMaster extends Migration
{
    public function up()
    {
        DB::table('google_sheet_masters')->insertOrIgnore([
            'integration_key' => 'merchandise_orders',
            'name' => 'Merchandise Orders',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        DB::table('google_sheet_masters')
            ->where('integration_key', 'merchandise_orders')
            ->delete();
    }
}
