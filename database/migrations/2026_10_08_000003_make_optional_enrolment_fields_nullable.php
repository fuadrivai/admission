<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeOptionalEnrolmentFieldsNullable extends Migration
{
    public function up()
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->string('invoice_id')->nullable()->change();
            $table->string('relationship')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('place_of_birth')->nullable()->change();
            $table->string('current_school')->nullable()->change();
            $table->string('knowledge_about_program')->nullable()->change();
            $table->string('info_from')->nullable()->change();
            $table->string('reason_for_enrolment')->nullable()->change();
            $table->string('preferred_program')->nullable()->change();
            $table->string('expectation_mhis_impact')->nullable()->change();
        });
    }

    public function down()
    {
        // Keep fields nullable: DP enrolments legitimately omit legacy form-only information.
    }
}
