<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('admission_statement_documents', function (Blueprint $table) {
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_id')->nullable()->after('description_en');
        });

        DB::table('admission_statement_documents')
            ->whereNotNull('description')
            ->update(['description_en' => DB::raw('description')]);
    }

    public function down()
    {
        DB::table('admission_statement_documents')
            ->whereNotNull('description_en')
            ->update(['description' => DB::raw('description_en')]);

        Schema::table('admission_statement_documents', function (Blueprint $table) {
            $table->dropColumn(['description_en', 'description_id']);
        });
    }
};
