<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Division::firstOrCreate(['name' => "Preschool"]);
        Division::firstOrCreate(['name' => "Primary"]);
        Division::firstOrCreate(['name' => "Secondary"]);
        Division::firstOrCreate(['name' => "Development Class"]);
    }
}
