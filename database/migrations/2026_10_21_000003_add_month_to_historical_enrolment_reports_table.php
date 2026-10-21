<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMonthToHistoricalEnrolmentReportsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('historical_enrolment_reports', 'snapshot_month')) {
            Schema::table('historical_enrolment_reports', function (Blueprint $table) {
                $table->unsignedTinyInteger('snapshot_month')->nullable()->after('academic_year_id');
            });
        }

        $indexes = collect(DB::select('SHOW INDEX FROM historical_enrolment_reports'))
            ->pluck('Key_name')
            ->unique();

        if (!$indexes->contains('historical_enrolment_report_month_scope_unique')) {
            Schema::table('historical_enrolment_reports', function (Blueprint $table) {
            $table->unique(
                ['academic_year_id', 'snapshot_month', 'branch_id', 'level_id', 'scope_key'],
                'historical_enrolment_report_month_scope_unique'
            );
            });
        }

        if ($indexes->contains('historical_enrolment_report_scope_unique')) {
            Schema::table('historical_enrolment_reports', function (Blueprint $table) {
                $table->dropUnique('historical_enrolment_report_scope_unique');
            });
        }
    }

    public function down()
    {
        if (DB::table('historical_enrolment_reports')->whereNotNull('snapshot_month')->exists()) {
            throw new RuntimeException('Cannot roll back month snapshots while monthly historical data exists.');
        }

        $indexes = collect(DB::select('SHOW INDEX FROM historical_enrolment_reports'))
            ->pluck('Key_name')
            ->unique();
        if (!$indexes->contains('historical_enrolment_report_scope_unique')) {
            Schema::table('historical_enrolment_reports', function (Blueprint $table) {
                $table->unique(
                    ['academic_year_id', 'branch_id', 'level_id', 'scope_key'],
                    'historical_enrolment_report_scope_unique'
                );
            });
        }

        if ($indexes->contains('historical_enrolment_report_month_scope_unique')) {
            Schema::table('historical_enrolment_reports', function (Blueprint $table) {
                $table->dropUnique('historical_enrolment_report_month_scope_unique');
            });
        }

        if (Schema::hasColumn('historical_enrolment_reports', 'snapshot_month')) {
            Schema::table('historical_enrolment_reports', function (Blueprint $table) {
                $table->dropColumn('snapshot_month');
            });
        }
    }
}
