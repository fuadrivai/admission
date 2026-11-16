<?php

namespace App\Google;

use App\Models\GoogleSheetMaster;
use App\Models\MerchandiseOrder;
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
        $this->writeOrders($spreadsheetId, $orders);
        $this->ensurePermissions($spreadsheetId);

        return $master->google_sheet_url;
    }

    public function syncOrder(int $orderId): void
    {
        $master = GoogleSheetMaster::where('integration_key', self::MASTER_KEY)->first();
        if (! $master || empty($master->google_sheet_url)) {
            return;
        }

        $spreadsheetId = $this->parseSpreadsheetId($master->google_sheet_url);
        $this->ensureSheet($spreadsheetId);

        $order = MerchandiseOrder::with('items')->findOrFail($orderId);
        $order->setAttribute(
            'display_payment_status',
            $order->payment_status === 'PENDING'
                && $order->expires_at
                && $order->expires_at->isPast()
                    ? 'EXPIRED'
                    : strtoupper($order->payment_status)
        );
        $row = $this->normalizedRow($this->orderRow($order));

        $header = $this->sheets()->spreadsheets_values
            ->get($spreadsheetId, self::SHEET_NAME . '!A1:R1')
            ->getValues() ?: [];
        if (empty($header)) {
            $this->sheets()->spreadsheets_values->update(
                $spreadsheetId,
                self::SHEET_NAME . '!A1:R1',
                new Sheets\ValueRange(['values' => [$this->headings()]]),
                ['valueInputOption' => 'RAW']
            );
        } elseif ($header[0] !== $this->headings()) {
            throw new RuntimeException('The Google Sheet header does not match the Merchandise Orders report.');
        }

        $existingCodes = $this->sheets()->spreadsheets_values
            ->get($spreadsheetId, self::SHEET_NAME . '!A2:A')
            ->getValues() ?: [];
        $rowNumber = null;
        foreach ($existingCodes as $index => $existingCode) {
            if (isset($existingCode[0]) && (string) $existingCode[0] === (string) $order->code) {
                $rowNumber = $index + 2;
                break;
            }
        }

        if ($rowNumber === null) {
            $this->sheets()->spreadsheets_values->append(
                $spreadsheetId,
                self::SHEET_NAME . '!A1',
                new Sheets\ValueRange(['values' => [$row]]),
                ['valueInputOption' => 'RAW']
            );

            return;
        }

        $this->sheets()->spreadsheets_values->update(
            $spreadsheetId,
            self::SHEET_NAME . '!A' . $rowNumber . ':R' . $rowNumber,
            new Sheets\ValueRange(['values' => [$row]]),
            ['valueInputOption' => 'RAW']
        );
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
            $values[] = $this->normalizedRow($this->orderRow($order));
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

    private function normalizedRow(array $row): array
    {
        return array_values(array_map(function ($value) {
            return $value === null ? '' : $value;
        }, $row));
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

            if (!isset($existing[$key])) {
                $this->drive()->permissions->create($spreadsheetId, $permission, [
                    'sendNotificationEmail' => false,
                ]);
            }
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
