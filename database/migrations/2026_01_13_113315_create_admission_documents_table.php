<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->cascadeOnDelete();
            $table->string('type')->comment("KTP_FATHER | KTP_MOTHER | AKTA_KELAHIRAN | KK");
            $table->string('owner_role')->comment("father | mother | child | family")->nullable();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 50);
            $table->unsignedBigInteger('file_size');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique(['admission_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admission_documents');
    }
}
