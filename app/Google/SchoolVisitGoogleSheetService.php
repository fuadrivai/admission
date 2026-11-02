<?php

namespace App\Google;

use App\Models\GoogleSheetMaster;
use App\Models\SchoolVisit;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Str;
use RuntimeException;

class SchoolVisitGoogleSheetService
{
    private const OWNER_EMAIL = 'admission@mutiaraharapan.sch.id';
    private const EDITOR_EMAILS = [
        'admission_bangka@mutiaraharapan.sch.id',
        'admission_semarang@mutiaraharapan.sch.id',
        'admission@phpgsheets-438702.iam.gserviceaccount.com',
    ];
    private const MASTER_KEY = 'school_visit';
    private const SHEET_NAME = 'School Visits';

    private $sheets;
    private $drive;

    public function sync(): string
    {
        $master = GoogleSheetMaster::where('integration_key', self::MASTER_KEY)->firstOrFail();
        $spreadsheetId = $this->getOrCreateSpreadsheet($master);

        $this->ensureSheet($spreadsheetId);
        $this->ensurePermissions($spreadsheetId);
        $this->writeVisits($spreadsheetId);

        return $master->google_sheet_url;
    }

    private function getOrCreateSpreadsheet(GoogleSheetMaster $master): string
    {
        if (empty($master->google_sheet_url)) {
            return $this->createSpreadsheet($master);
        }

        $spreadsheetId = $this->spreadsheetId($master->google_sheet_url);

        try {
            $this->sheets()->spreadsheets->get($spreadsheetId, [
                'fields' => 'spreadsheetId',
            ]);
        } catch (\Google\Service\Exception $exception) {
            if ((int) $exception->getCode() !== 404) {
                throw $exception;
            }

            return $this->createSpreadsheet($master);
        }

        return $spreadsheetId;
    }

    public function appendVisit(SchoolVisit $visit): void
    {
        $master = GoogleSheetMaster::where('integration_key', self::MASTER_KEY)->first();
        if (! $master || ! $master->google_sheet_url) {
            return;
        }

        $spreadsheetId = $this->spreadsheetId($master->google_sheet_url);
        $this->ensureSheet($spreadsheetId);
        $range = self::SHEET_NAME . '!A:Q';
        $values = $this->sheets()->spreadsheets_values->get($spreadsheetId, $range)->getValues() ?: [];

        if (empty($values)) {
            $this->sheets()->spreadsheets_values->update(
                $spreadsheetId,
                self::SHEET_NAME . '!A1:Q1',
                new Sheets\ValueRange(['values' => [$this->headings()]]),
                ['valueInputOption' => 'RAW']
            );
            $values = [$this->headings()];
        } elseif ($values[0] !== $this->headings()) {
            throw new RuntimeException('The Google Sheet header does not match the School Visit report.');
        }

        foreach (array_slice($values, 1) as $index => $existingRow) {
            if (isset($existingRow[1]) && (string) $existingRow[1] === (string) $visit->code) {
                $rowNumber = $index + 2;
                $row = $this->visitRow($visit->loadMissing('prospect.enrolment'), $existingRow[0] ?? $index + 1);
                $this->sheets()->spreadsheets_values->update(
                    $spreadsheetId,
                    self::SHEET_NAME . '!A' . $rowNumber . ':Q' . $rowNumber,
                    new Sheets\ValueRange(['values' => [$row]]),
                    ['valueInputOption' => 'RAW']
                );

                return;
            }
        }

        $row = $this->visitRow($visit->loadMissing('prospect.enrolment'), count($values));

        $this->sheets()->spreadsheets_values->append(
            $spreadsheetId,
            self::SHEET_NAME . '!A1',
            new Sheets\ValueRange(['values' => [$row]]),
            ['valueInputOption' => 'RAW']
        );
    }

    private function createSpreadsheet(GoogleSheetMaster $master): string
    {
        $spreadsheet = $this->sheets()->spreadsheets->create(new Sheets\Spreadsheet([
            'properties' => ['title' => 'School Visit Report'],
            'sheets' => [[
                'properties' => ['title' => self::SHEET_NAME],
            ]],
        ]));

        $spreadsheetId = $spreadsheet->getSpreadsheetId();
        if (! $spreadsheetId) {
            throw new RuntimeException('Google Sheets did not return a spreadsheet ID.');
        }

        $master->google_sheet_url = 'https://docs.google.com/spreadsheets/d/' . $spreadsheetId . '/edit';
        $master->save();

        return $spreadsheetId;
    }

    private function writeVisits(string $spreadsheetId): void
    {
        $values = [$this->headings()];

        $visits = SchoolVisit::with('prospect.enrolment')->orderBy('id')->get();
        foreach ($visits as $index => $visit) {
            $values[] = $this->visitRow($visit, $index + 1);
        }

        $this->sheets()->spreadsheets_values->update(
            $spreadsheetId,
            self::SHEET_NAME . '!A1:Q' . count($values),
            new Sheets\ValueRange(['values' => $values]),
            ['valueInputOption' => 'RAW']
        );

        $this->sheets()->spreadsheets_values->clear(
            $spreadsheetId,
            self::SHEET_NAME . '!A' . (count($values) + 1) . ':Q',
            new Sheets\ClearValuesRequest()
        );
    }

    private function headings(): array
    {
        return [
            'No',
            'Code',
            'Status',
            'Visit Date',
            'Admission Staff',
            'Parent Name',
            'Child Name',
            'Branch',
            'Level/Grade',
            'Previous School',
            'Enrolment Status',
            'Information Source',
            'Reason why Visit MH',
            'Enrolment Consideration',
            'Reason for Enrol',
            'Reason not Enrol',
            'Note',
        ];
    }

    private function visitRow(SchoolVisit $visit, int $number): array
    {
        $enrolment = optional(optional($visit->prospect)->enrolment);
        $date = $visit->date ? \Carbon\Carbon::parse($visit->date)->format('d F Y') : '-';
        $time = $visit->time ? \Carbon\Carbon::parse($visit->time)->format('H:i') : '';

        return [
            $number,
            $visit->code ?: '-',
            $visit->status ?: '-',
            trim($date . ' ' . $time),
            $visit->admission_staff ?: '-',
            $visit->parent_name ?: '-',
            $visit->child_name ?: '-',
            $visit->branch_name ?: '-',
            trim(($visit->level_name ?: '') . '/' . ($visit->grade_name ?: '')),
            $visit->current_school ?: '-',
            optional($enrolment)->payment_status ?: '-',
            $visit->info_from ?: '-',
            $visit->reason_for_visit ?: '-',
            $visit->enrolment_consideration ?: '-',
            $visit->reason_for_enrol ?: '-',
            $visit->reason_not_enrol ?: '-',
            $visit->note ?: '-',
        ];
    }

    private function ensureSheet(string $spreadsheetId): void
    {
        $spreadsheet = $this->sheets()->spreadsheets->get($spreadsheetId, [
            'fields' => 'sheets.properties.title',
        ]);

        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() === self::SHEET_NAME) {
                return;
            }
        }

        $this->sheets()->spreadsheets->batchUpdate(
            $spreadsheetId,
            new Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => [[
                    'addSheet' => ['properties' => ['title' => self::SHEET_NAME]],
                ]],
            ])
        );
    }

    private function ensurePermissions(string $spreadsheetId): void
    {
        $permissions = $this->listPermissions($spreadsheetId);
        $existing = [];

        foreach ($permissions as $permission) {
            $key = $permission->getType() === 'anyone'
                ? 'anyone'
                : Str::lower((string) $permission->getEmailAddress());
            $existing[$key] = $permission;
        }

        $this->setPermission($spreadsheetId, $existing, 'anyone', [
            'type' => 'anyone',
            'role' => 'reader',
        ]);

        foreach (self::EDITOR_EMAILS as $email) {
            $this->setPermission($spreadsheetId, $existing, Str::lower($email), [
                'type' => 'user',
                'role' => 'writer',
                'emailAddress' => $email,
            ]);
        }

        $ownerKey = Str::lower(self::OWNER_EMAIL);
        if (! isset($existing[$ownerKey]) || $existing[$ownerKey]->getRole() !== 'owner') {
            $permission = new Drive\Permission([
                'type' => 'user',
                'role' => 'owner',
                'emailAddress' => self::OWNER_EMAIL,
            ]);
            $options = ['transferOwnership' => true];

            if (isset($existing[$ownerKey])) {
                $this->drive()->permissions->update($spreadsheetId, $existing[$ownerKey]->getId(), $permission, $options);
            } else {
                $this->drive()->permissions->create($spreadsheetId, $permission, $options);
            }
        }

        $allowedEditors = array_map('strtolower', array_merge(self::EDITOR_EMAILS, [self::OWNER_EMAIL]));
        foreach ($this->listPermissions($spreadsheetId) as $permission) {
            $email = Str::lower((string) $permission->getEmailAddress());
            if (! in_array($permission->getRole(), ['commenter', 'writer'], true)
                || in_array($email, $allowedEditors, true)) {
                continue;
            }

            $permission->setRole('reader');
            $this->drive()->permissions->update($spreadsheetId, $permission->getId(), $permission);
        }
    }

    private function listPermissions(string $spreadsheetId): array
    {
        $permissions = [];
        $pageToken = null;

        do {
            $options = [
                'fields' => 'nextPageToken,permissions(id,type,emailAddress,role)',
                'pageSize' => 100,
            ];
            if ($pageToken) {
                $options['pageToken'] = $pageToken;
            }

            $response = $this->drive()->permissions->listPermissions($spreadsheetId, $options);
            $permissions = array_merge($permissions, $response->getPermissions() ?: []);
            $pageToken = $response->getNextPageToken();
        } while ($pageToken);

        return $permissions;
    }

    private function setPermission(string $spreadsheetId, array $existing, string $key, array $attributes): void
    {
        $permission = new Drive\Permission($attributes);

        if (isset($existing[$key])) {
            if ($existing[$key]->getRole() !== $attributes['role']) {
                $this->drive()->permissions->update($spreadsheetId, $existing[$key]->getId(), $permission);
            }

            return;
        }

        $this->drive()->permissions->create($spreadsheetId, $permission, [
            'sendNotificationEmail' => false,
        ]);
    }

    private function spreadsheetId(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH);

        if ($host !== 'docs.google.com' || ! preg_match('#/spreadsheets/d/([A-Za-z0-9_-]+)#', (string) $path, $matches)) {
            throw new RuntimeException('The Google Sheet URL is invalid.');
        }

        return $matches[1];
    }

    private function sheets(): Sheets
    {
        if (! $this->sheets) {
            $client = new Client();
            $client->setAuthConfig(storage_path('google/admission-service-account.json'));
            $client->setSubject(self::OWNER_EMAIL);
            $client->setScopes([Sheets::SPREADSHEETS, Drive::DRIVE]);
            $this->sheets = new Sheets($client);
            $this->drive = new Drive($client);
        }

        return $this->sheets;
    }

    private function drive(): Drive
    {
        $this->sheets();

        return $this->drive;
    }
}
