<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registration_places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_other')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        foreach ([
            ['School', 'school', false],
            ['Open Day', 'openday', false],
            ['Information Session', 'information_session', false],
            ['FE', 'FE', false],
            ['Other', 'other', true],
        ] as [$name, $code, $other]) {
            DB::table('registration_places')->insert([
                'name' => $name, 'code' => $code, 'is_other' => $other,
                'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('registration_places');
    }
};
