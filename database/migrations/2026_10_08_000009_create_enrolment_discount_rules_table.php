<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrolment_discount_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_place_code');
            $table->string('applies_to_type')->default('enrolment');
            $table->decimal('percentage', 5, 2);
            $table->boolean('requires_dp')->default(true);
            $table->unsignedInteger('quota')->nullable();
            $table->date('valid_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_rule_id')->nullable()->after('discount');
        });
    }

    public function down()
    {
        Schema::table('enrolment_transactions', function (Blueprint $table) {
            $table->dropColumn('discount_rule_id');
        });
        Schema::dropIfExists('enrolment_discount_rules');
    }
};
