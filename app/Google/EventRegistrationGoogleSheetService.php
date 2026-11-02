<?php

namespace App\Google;

use App\Models\Event;
use App\Models\EventRegistration;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class EventRegistrationGoogleSheetService
{
    private const OWNER_EMAIL = 'admission@mutiaraharapan.sch.id';
    private const EDITOR_EMAILS = [
        'admission_bangka@mutiaraharapan.sch.id',
        'admission_semarang@mutiaraharapan.sch.id',
        'admission@phpgsheets-438702.iam.gserviceaccount.com',
    ];
    private const SHEET_NAME = 'Registrations';

    private $sheets;
    private $drive;

    public function syncEvent(Event $event): string
    {
        $sheets = $this->sheets();

        if (empty($event->google_sheet_url)) {
            $spreadsheet = $sheets->spreadsheets->create(new Sheets\Spreadsheet([
                'properties' => [
                    'title' => 'Registrations - ' . $event->title,
                ],
                'sheets' => [[
                    'properties' => ['title' => self::SHEET_NAME],
                ]],
            ]));

            $spreadsheetId = $spreadsheet->getSpreadsheetId();
            if (! $spreadsheetId) {
                throw new RuntimeException('Google Sheets did not return a spreadsheet ID.');
            }

            $event->google_sheet_url = $this->spreadsheetUrl($spreadsheetId);
            $event->save();
        } else {
            $spreadsheetId = $this->spreadsheetId($event->google_sheet_url);
        }

        $this->ensureSheet($spreadsheetId);
        $this->ensurePermissions($spreadsheetId);
        $this->syncRegistrations($event, $spreadsheetId);

        return $event->google_sheet_url;
    }

    public function appendRegistration(EventRegistration $registration): void
    {
        $event = $registration->event;

        if (! $event || empty($event->google_sheet_url)) {
            return;
        }

        $spreadsheetId = $this->spreadsheetId($event->google_sheet_url);
        $this->ensureSheet($spreadsheetId);

        $fields = $this->fields($event);
        $header = $this->header($fields);
        $range = self::SHEET_NAME . '!A:ZZ';
        $values = $this->sheets()->spreadsheets_values->get($spreadsheetId, $range)->getValues() ?: [];

        if (empty($values)) {
            $this->appendValues($spreadsheetId, [$header]);
        } elseif ($values[0] !== $header) {
            throw new RuntimeException('The Google Sheet header does not match this event form. Run a full sync after updating the form.');
        }

        foreach (array_slice($values, 1) as $row) {
            if (isset($row[0]) && (string) $row[0] === (string) $registration->code) {
                return;
            }
        }

        $this->appendValues($spreadsheetId, [
            $this->registrationRow($registration->loadMissing('fieldAnswers'), $fields),
        ]);
    }

    private function syncRegistrations(Event $event, string $spreadsheetId): void
    {
        $fields = $this->fields($event);
        $header = $this->header($fields);
        $range = self::SHEET_NAME . '!A:ZZ';
        $values = $this->sheets()->spreadsheets_values->get($spreadsheetId, $range)->getValues() ?: [];

        if (empty($values)) {
            $this->appendValues($spreadsheetId, [$header]);
        } elseif ($values[0] !== $header) {
            throw new RuntimeException('The Google Sheet header does not match this event form. Use an empty sheet or one created for this event.');
        }

        $existingCodes = [];
        foreach (array_slice($values, 1) as $row) {
            if (isset($row[0])) {
                $existingCodes[(string) $row[0]] = true;
            }
        }

        $registrations = $event->registrations()
            ->with('fieldAnswers')
            ->orderBy('id')
            ->get();
        $missingRows = [];

        foreach ($registrations as $registration) {
            if (! isset($existingCodes[(string) $registration->code])) {
                $missingRows[] = $this->registrationRow($registration, $fields);
            }
        }

        if ($missingRows) {
            $this->appendValues($spreadsheetId, $missingRows);
        }
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

            if (isset($existing[$ownerKey])) {
                $this->drive()->permissions->update(
                    $spreadsheetId,
                    $existing[$ownerKey]->getId(),
                    $permission,
                    ['transferOwnership' => true]
                );
            } else {
                $this->drive()->permissions->create(
                    $spreadsheetId,
                    $permission,
                    ['transferOwnership' => true]
                );
            }
        }

        $allowedEditors = array_map('strtolower', array_merge(self::EDITOR_EMAILS, [self::OWNER_EMAIL]));
        $permissions = $this->listPermissions($spreadsheetId);

        foreach ($permissions as $permission) {
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
                $this->drive()->permissions->update(
                    $spreadsheetId,
                    $existing[$key]->getId(),
                    $permission
                );
            }

            return;
        }

        $this->drive()->permissions->create($spreadsheetId, $permission, [
            'sendNotificationEmail' => false,
        ]);
    }

    private function appendValues(string $spreadsheetId, array $rows): void
    {
        $this->sheets()->spreadsheets_values->append(
            $spreadsheetId,
            self::SHEET_NAME . '!A1',
            new Sheets\ValueRange(['values' => $rows]),
            ['valueInputOption' => 'RAW']
        );
    }

    private function fields(Event $event): Collection
    {
        return $event->fields()->orderBy('order_index')->orderBy('id')->get();
    }

    private function header(Collection $fields): array
    {
        return array_merge(
            ['Code'],
            $fields->map(function ($field) {
                return $field->label ?: $field->field_key;
            })->all(),
            ['Amount', 'Registered At', 'Status']
        );
    }

    private function registrationRow(EventRegistration $registration, Collection $fields): array
    {
        $answers = $registration->fieldAnswers->keyBy('event_field_id');
        $row = [(string) $registration->code];

        foreach ($fields as $field) {
            $answer = $answers->get($field->id);
            $row[] = $answer ? (string) $answer->value : '';
        }

        $row[] = (string) $registration->amount;
        $row[] = $registration->registered_at
            ? $registration->registered_at->format('Y-m-d H:i:s')
            : '';
        $row[] = (string) $registration->status;

        return $row;
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

    private function spreadsheetUrl(string $spreadsheetId): string
    {
        return 'https://docs.google.com/spreadsheets/d/' . $spreadsheetId . '/edit';
    }

    private function sheets(): Sheets
    {
        if (! $this->sheets) {
            $client = $this->client();
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

    private function client(): Client
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('google/admission-service-account.json'));
        $client->setSubject(self::OWNER_EMAIL);
        $client->setScopes([
            Sheets::SPREADSHEETS,
            Drive::DRIVE,
        ]);

        return $client;
    }
}
