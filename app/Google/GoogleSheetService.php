<?php

namespace App\Google;

use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;

class GoogleSheetService
{
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('google/admission-service-account.json'));
        $client->addScope(Sheets::SPREADSHEETS);

        $this->service = new Sheets($client);

        // Spreadsheet ID dari URL Google Sheet
        $this->spreadsheetId = env('GOOGLE_SHEET_ID');
    }

    public function appendRow(array $values, $sheetName = 'BINTARO')
    {
        $range = $sheetName . '!A1';
        $body = new Sheets\ValueRange([
            'values' => [$values]
        ]);

        $params = ['valueInputOption' => 'USER_ENTERED'];

        return $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }

    public function updateDateTimeByCode(string $code, string $date, string $time, string $sheetName = 'BINTARO')
    {
        $service = $this->service;
        $spreadsheetId = $this->spreadsheetId;
        $range = "{$sheetName}!A2:Z"; // Ambil data mulai baris ke-2 (skip header)

        // Ambil semua data dari sheet
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            throw new \Exception('Tidak ada data di Google Sheet.');
        }

        // Cari baris berdasarkan code (kolom B)
        $rowIndex = null;
        foreach ($values as $i => $row) {
            if (isset($row[1]) && trim($row[1]) === trim($code)) { // Kolom B = Observation Code
                $rowIndex = $i + 2; // +2 karena index dimulai dari 0 & header di baris 1
                break;
            }
        }

        if ($rowIndex === null) {
            throw new \Exception("Data dengan code {$code} tidak ditemukan di Google Sheet.");
        }

        // Format tanggal dan waktu
        $formattedDate = Carbon::parse($date)->format('d M Y'); // contoh: 11 Oct 2025
        $formattedTime = Carbon::parse($time)->format('H:i');    // contoh: 08:00

        // Tentukan sel yang akan diupdate
        $dateCell = "{$sheetName}!K{$rowIndex}"; // kolom K untuk tanggal
        $timeCell = "{$sheetName}!L{$rowIndex}"; // kolom L untuk jam

        // Update kolom tanggal (K)
        $dateBody = new \Google\Service\Sheets\ValueRange([
            'values' => [[$formattedDate]]
        ]);
        $service->spreadsheets_values->update(
            $spreadsheetId,
            $dateCell,
            $dateBody,
            ['valueInputOption' => 'USER_ENTERED']
        );

        // Update kolom waktu (L)
        $timeBody = new \Google\Service\Sheets\ValueRange([
            'values' => [[$formattedTime]]
        ]);
        $service->spreadsheets_values->update(
            $spreadsheetId,
            $timeCell,
            $timeBody,
            ['valueInputOption' => 'USER_ENTERED']
        );
    }
}
