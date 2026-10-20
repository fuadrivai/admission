<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRequiredToAdmissionFinancialSectionsTable extends Migration
{
    public function up()
    {
        Schema::table('admission_financial_sections', function (Blueprint $table) {
            $table->boolean('is_required')->default(true)->after('sort_order');
        });
    }

    public function down()
    {
        Schema::table('admission_financial_sections', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
    }
}
