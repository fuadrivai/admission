<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('from_address');
            $table->string('from_name');
            $table->string('app_password');
            $table->string('mailer')->default('smtp');
            $table->string('host')->default('smtp.gmail.com');
            $table->integer('port')->default(587);
            $table->string('encryption')->default('tls');
            $table->foreignId('branch_id');
            $table->string('branch_name')->nullable();
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
        Schema::dropIfExists('email_settings');
    }
}
