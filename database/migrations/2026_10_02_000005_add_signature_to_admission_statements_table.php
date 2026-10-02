<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignatureToAdmissionStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admission_statements', function (Blueprint $table) {
            if (!Schema::hasColumn('admission_statements', 'signature_path')) {
                $table->string('signature_path')->nullable();
            }

            if (!Schema::hasColumn('admission_statements', 'signature_uploaded_at')) {
                $table->timestamp('signature_uploaded_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admission_statements', function (Blueprint $table) {
            if (Schema::hasColumn('admission_statements', 'signature_path')) {
                $table->dropColumn('signature_path');
            }

            if (Schema::hasColumn('admission_statements', 'signature_uploaded_at')) {
                $table->dropColumn('signature_uploaded_at');
            }
        });
    }
}
