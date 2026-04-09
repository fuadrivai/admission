<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToschoolVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_visits', function (Blueprint $table) {
            $table->text('reason_for_visit')->nullable()->after('updated_at')->comment('This column will store the reason for the school visit provided by the user during the visit scheduling process.');
            $table->text('enrolment_consideration')->nullable()->after('reason_for_visit')->comment('This column will store the enrolment consideration provided by the user during the visit scheduling process, indicating whether they are considering enrolling or not.');
            $table->string('reason_for_enrol')->nullable()->after('enrolment_consideration')->comment('This column will store the reason for enrolling provided by the user during the visit scheduling process.');
            $table->string('reason_not_enrol')->nullable()->after('reason_for_enrol')->comment('This column will store the reason for not enrolling provided by the user during the visit scheduling process.');
            $table->string('admission_staff')->nullable()->after('reason_not_enrol');
            $table->text('note')->nullable()->after('admission_staff');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_visits', function (Blueprint $table) {
            //
        });
    }
}
