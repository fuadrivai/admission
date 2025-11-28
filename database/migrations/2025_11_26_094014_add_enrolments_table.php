<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnrolmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospects_id')->nullable()->constrained('prospects')->onDelete('set null');
            $table->boolean('already_visit')->default(false);
            $table->string('code')->unique();
            $table->string('is_current_student')->comment('Is your child currently studying at MHIS?');
            $table->string('student_branch')->nullable();
            $table->string('mhis_portal_username')->nullable();
            $table->foreignId('branch_id');
            $table->foreignId('level_id');
            $table->foreignId('grade_id');
            $table->string('academic_year');
            $table->string('parent_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('relationship');
            $table->string('zipcode');
            $table->string('address');
            $table->string('child_name');
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('current_school');
            $table->string('child_sosmed')->nullable(); 
            $table->boolean('open_day_visited')->default(false);
            $table->string('knowledge_about_program')->comment('Do you already know more or less our school program?');
            $table->string('info_from')->comment('How did you hear about us?');
            $table->string('info_from_message')->comment('if info_from is other, please specify')->nullable();
            $table->string('reason_for_enrolment')->comment('What is the specific thing that makes you want to enrol in MHIS?');
            $table->string('preferred_program')->comment('What MHIS program is best suited for your child?');
            $table->string('expectation_mhis_impact')->comment('To what extent do you believe MHIS educational standards will help your child succeed as a champion?');
            $table->string('trust_reason')->nullable()->comment('(internal) What is it that you like best about us that regains your trust?');
            $table->string('recommender_name')->nullable();
            $table->string('recommender_phone')->nullable();
            $table->string('recommender_child_name')->nullable();
            $table->string('recommender_child_class')->nullable();
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->decimal('bank_charger', 10, 2)->nullable()->default(0);    
            $table->decimal('amount_paid', 10, 2)->nullable()->default(0);
            $table->string('invoice_id')->unique()->comment('external ID for Xendit');
            $table->string('payment_status')->nullable()->default('unpaid'); 
            $table->date('payment_date')->nullable();
            $table->string('payment_url')->nullable();
            $table->enum('source_data', ['internal', 'external'])->comment('internal or external');
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
        Schema::dropIfExists('enrolments');
    }
}
