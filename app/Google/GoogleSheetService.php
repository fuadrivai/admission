<?php

namespace App\Google;

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
}
