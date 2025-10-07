<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Google\GoogleSheetService;

class GoogleSheetSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        $sheetService = new GoogleSheetService();

        // Data dummy untuk testing
        $data = [
            '3',                          // ID dummy
            'OBV202510070001',          // Observation Code
            'Bilal',                   // Nama anak
            'Male',                       // Gender
            'Primary',                    // Level
            'Grade 1',                    // Grade
            'Fuad',                    // Nama orang tua
            'Ayah',                       // Relationship
            '081234567890',               // Phone
            'parent@example.com',         // Email
            '2025-09-23',                 // Date
            '09:00',                      // Time
            'Register',                   // Status
            now()->toDateTimeString(),    // Timestamp insert
        ];

        $sheetService->appendRow($data);

        $this->command->info('Dummy data berhasil dikirim ke Google Spreadsheet!');
    }
}
