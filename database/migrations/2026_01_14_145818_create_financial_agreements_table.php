<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_statement_id');
            $table->boolean('agree_full_payment_terms')->default(false);
            $table->decimal('development_fee', 20, 2)->default(0)->nullable();
            $table->decimal('annual_fee', 20, 2)->default(0)->nullable();
            $table->decimal('school_fee', 20, 2)->default(0)->nullable();
            $table->boolean('agree_development_fee_policy')->default(false);
            $table->boolean('agree_annual_and_school_fee_policy')->default(false);
            $table->boolean('agree_exam_fee')->default(false);
            $table->boolean('agree_learning_material_fee')->default(false);
            $table->boolean('agree_additional_activity_fee')->default(false);
            $table->boolean('agree_monthly_school_fee_payment')->default(false);
            $table->boolean('agree_ittihada_fee')->default(false);
            $table->boolean('agree_full_financial_obligation')->default(false);
            $table->boolean('agree_financial_terms_and_consequences')->default(false);
            $table->boolean('agree_truth_and_consent')->default(false);
            $table->timestamp('agreed_at');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_agreements');
    }
}
