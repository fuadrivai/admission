<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Level;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            // Playgroup
            'Playgroup' => ['PG B', 'PG C'],

            // Kindergarten
            'Kindergarten' => ['KG A', 'KG B'],

            // Primary
            'Primary' => ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],

            // Lower Secondary
            'Lower Secondary' => ['Grade 7', 'Grade 8', 'Grade 9'],

            // Upper Secondary
            'Upper Secondary' => ['Grade 10', 'Grade 11', 'Grade 12'],

            // Preschool Development Class
            'Preschool Development Class' => ['PG B DC', 'PG C DC', 'KG A DC', 'KG B DC'],

            // Primary Development Class
            'Primary Development Class' => ['Grade 1 DC', 'Grade 2 DC', 'Grade 3 DC', 'Grade 4 DC', 'Grade 5 DC', 'Grade 6 DC'],

            // Junior High Development Class
            'Junior High Development Class' => ['Grade 7 DC', 'Grade 8 DC', 'Grade 9 DC'],
        ];

        foreach ($data as $levelName => $subLevels) {
            $level = Level::firstOrCreate(['name' => $levelName]);

            foreach ($subLevels as $subLevelName) {
                Grade::firstOrCreate([
                    'level_id' => $level->id,
                    'name' => $subLevelName,
                ]);
            }
        }
    }
}
