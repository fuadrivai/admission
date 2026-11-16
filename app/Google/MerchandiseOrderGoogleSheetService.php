<?php

namespace App\Google;

use App\Models\GoogleSheetMaster;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class MerchandiseOrderGoogleSheetService
{
    private const OWNER_EMAIL = 'admission@mutiaraharapan.sch.id';
    private const EDITOR_EMAILS = [
        'admission_bangka@mutiaraharapan.sch.id',
        'admission_semarang@mutiaraharapan.sch.id',
        'admission@phpgsheets-438702.iam.gserviceaccount.com',
    ];
    private const MASTER_KEY = 'merchandise_orders';
    private const SHEET_NAME = 'Orders';

    private $sheets;
    private $drive;

    public function sync(Collection $orders): string
    {
        $master = GoogleSheetMaster::firstOrCreate(
            ['integration_key' => self::MASTER_KEY],
            ['name' => 'Merchandise Orders']
        );
        $spreadsheetId = $this->spreadsheetId($master);

        $this->ensureSheet($spreadsheetId);
        $this->ensurePermissions($spreadsheetId);
        $this->writeOrders($spreadsheetId, $orders);

        return $master->google_sheet_url;
    }

    private function spreadsheetId(GoogleSheetMaster $master): string
    {
        if (empty($master->google_sheet_url)) {
            return $this->createSpreadsheet($master);
        }

        $id = $this->parseSpreadsheetId($master->google_sheet_url);
        try {
            $this->sheets()->spreadsheets->get($id, ['fields' => 'spreadsheetId']);
        } catch (\Google\Service\Exception $exception) {
            if ((int) $exception->getCode() !== 404) {
                throw $exception;
            }

            return $this->createSpreadsheet($master);
        }

        return $id;
    }

    private function createSpreadsheet(GoogleSheetMaster $master): string
    {
        $spreadsheet = $this->sheets()->spreadsheets->create(new Sheets\Spreadsheet([
            'properties' => ['title' => 'Merchandise Orders'],
            'sheets' => [[
                'properties' => ['title' => self::SHEET_NAME],
            ]],
        ]));

        $id = $spreadsheet->getSpreadsheetId();
        if (! $id) {
            throw new RuntimeException('Google Sheets did not return a spreadsheet ID.');
        }

        $master->google_sheet_url = $this->spreadsheetUrl($id);
        $master->save();

        return $id;
    }

    private function writeOrders(string $spreadsheetId, Collection $orders): void
    {
        $values = [array_values($this->headings())];
        foreach ($orders as $order) {
            $values[] = array_values(array_map(function ($value) {
                return $value === null ? '' : $value;
            }, $this->orderRow($order)));
        }

        $this->sheets()->spreadsheets_values->update(
            $spreadsheetId,
            self::SHEET_NAME . '!A1:R' . count($values),
            new Sheets\ValueRange(['values' => $values]),
            ['valueInputOption' => 'RAW']
        );

        $this->sheets()->spreadsheets_values->clear(
            $spreadsheetId,
            self::SHEET_NAME . '!A' . (count($values) + 1) . ':R',
            new Sheets\ClearValuesRequest()
        );
    }

    private function headings(): array
    {
        return [
            'Order Number',
            'Order Date',
            'Order Type',
            'Parent Name',
            'Email',
            'Phone',
            'Portal Username',
            'Branch',
            'Payment Status',
            'Fulfillment Status',
            'Items',
            'Item Count',
            'Subtotal (IDR)',
            'Discount (IDR)',
            'Admin Fee (IDR)',
            'Total (IDR)',
            'Paid At',
            'Invoice Expires At',
        ];
    }

    private function orderRow($order): array
    {
        $items = $order->items->map(function ($item) {
            $name = trim(($item->product_name ?: '') . ' - ' . ($item->variant_name ?: ''), ' -');

            return $name . ' x ' . $item->quantity;
        })->implode('; ');

        return [
            (string) $order->code,
            optional($order->created_at)->format('Y-m-d H:i:s'),
            (string) $order->order_type,
            (string) $order->parent_name,
            (string) $order->email,
            (string) $order->phone,
            (string) $order->portal_username,
            (string) $order->branch_name,
            (string) $order->display_payment_status,
            (string) $order->fulfillment_status,
            $items,
            (int) $order->total_items,
            (float) $order->subtotal,
            (float) $order->discount_amount,
            (float) $order->bank_charger,
            (float) $order->total_amount,
            optional($order->paid_at)->format('Y-m-d H:i:s') ?: '',
            optional($order->expires_at)->format('Y-m-d H:i:s') ?: '',
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

        if (isset($existing['anyone'])) {
            $this->drive()->permissions->delete($spreadsheetId, $existing['anyone']->getId());
        }

        foreach (self::EDITOR_EMAILS as $email) {
            $key = Str::lower($email);
            $permission = new Drive\Permission([
                'type' => 'user',
                'role' => 'writer',
                'emailAddress' => $email,
            ]);

            if (isset($existing[$key])) {
                if ($existing[$key]->getRole() !== 'writer') {
                    $this->drive()->permissions->update($spreadsheetId, $existing[$key]->getId(), $permission);
                }
            } else {
                $this->drive()->permissions->create($spreadsheetId, $permission, [
                    'sendNotificationEmail' => false,
                ]);
            }
        }

        $ownerKey = Str::lower(self::OWNER_EMAIL);
        $existing = $this->permissionsByEmail($spreadsheetId);
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
        foreach ($this->listPermissions($spreadsheetId) as $permission) {
            $email = Str::lower((string) $permission->getEmailAddress());
            if (in_array($permission->getRole(), ['commenter', 'writer'], true)
                && ! in_array($email, $allowedEditors, true)) {
                $permission->setRole('reader');
                $this->drive()->permissions->update($spreadsheetId, $permission->getId(), $permission);
            }
        }
    }

    private function permissionsByEmail(string $spreadsheetId): array
    {
        $permissions = [];
        foreach ($this->listPermissions($spreadsheetId) as $permission) {
            if ($permission->getType() === 'user') {
                $permissions[Str::lower((string) $permission->getEmailAddress())] = $permission;
            }
        }

        return $permissions;
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

    private function parseSpreadsheetId(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH);

        if ($host !== 'docs.google.com'
            || ! preg_match('#/spreadsheets/d/([A-Za-z0-9_-]+)#', (string) $path, $matches)) {
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
