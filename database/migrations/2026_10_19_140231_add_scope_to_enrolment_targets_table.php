<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddScopeToEnrolmentTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrolment_targets', function (Blueprint $table) {
            $table->string('target_scope', 16)->default('grade')->after('level_id');
            $table->unsignedBigInteger('grade_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::table('enrolment_targets')->where('target_scope', 'level')->exists()) {
            throw new RuntimeException('Cannot roll back target scope migration while level-scope targets exist.');
        }

        Schema::table('enrolment_targets', function (Blueprint $table) {
            $table->dropColumn('target_scope');
            $table->unsignedBigInteger('grade_id')->nullable(false)->change();
        });
    }
}
