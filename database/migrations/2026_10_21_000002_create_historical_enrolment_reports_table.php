<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalEnrolmentReportsTable extends Migration
{
    public function up()
    {
        Schema::create('historical_enrolment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete();
            $table->string('scope_type', 16);
            $table->string('scope_key', 32);
            $table->foreignId('grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->unsignedInteger('active_children')->default(0);
            $table->unsignedInteger('cancelled_children')->default(0);
            $table->unsignedInteger('enrolment_expired')->default(0);
            $table->unsignedInteger('enrolment_pending')->default(0);
            $table->unsignedInteger('enrolment_paid')->default(0);
            $table->unsignedInteger('dp_expired')->default(0);
            $table->unsignedInteger('dp_pending')->default(0);
            $table->unsignedInteger('dp_paid')->default(0);
            $table->unsignedInteger('target')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['academic_year_id', 'branch_id', 'level_id', 'scope_key'],
                'historical_enrolment_report_scope_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('historical_enrolment_reports');
    }
}
