<?php

namespace Tests\Feature;

use App\Exports\MerchandiseOrderExport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use ReflectionMethod;
use Google\Service\Sheets;
use Tests\TestCase;

class MerchandiseOrderExportTest extends TestCase
{
    public function test_order_export_includes_order_and_item_details(): void
    {
        $order = (object) [
            'code' => 'MERCH-TEST',
            'created_at' => Carbon::parse('2026-11-14 10:00:00'),
            'order_type' => 'PREORDER',
            'parent_name' => 'Parent Name',
            'email' => 'parent@example.com',
            'phone' => '08123456789',
            'portal_username' => 'P100',
            'branch_name' => 'Bintaro',
            'fulfillment_status' => 'WAITING_FOR_STOCK',
            'total_items' => 2,
            'subtotal' => 240000,
            'discount_amount' => 0,
            'bank_charger' => 1500,
            'total_amount' => 241500,
            'paid_at' => null,
            'expires_at' => null,
            'display_payment_status' => 'PAID',
            'items' => new Collection([
                (object) [
                    'product_name' => 'Shall MHIS',
                    'variant_name' => 'Kuning / 1.5 Meter',
                    'quantity' => 2,
                ],
            ]),
        ];

        $export = new MerchandiseOrderExport(collect([$order]));
        $row = $export->map($order);

        $this->assertCount(18, $export->headings());
        $this->assertCount(18, $row);
        $this->assertSame('MERCH-TEST', $row[0]);
        $this->assertSame('PAID', $row[8]);
        $this->assertSame('Shall MHIS - Kuning / 1.5 Meter x 2', $row[10]);
        $this->assertEquals(241500, $row[15]);

        $googleRowMethod = new ReflectionMethod(
            \App\Google\MerchandiseOrderGoogleSheetService::class,
            'orderRow'
        );
        $googleRowMethod->setAccessible(true);
        $googleRow = $googleRowMethod->invoke(new \App\Google\MerchandiseOrderGoogleSheetService(), $order);

        $this->assertSame('', $googleRow[16]);
        $this->assertSame('', $googleRow[17]);
        $this->assertSame(range(0, 17), array_keys($googleRow));

        $payload = json_decode(json_encode((new Sheets\ValueRange([
            'values' => [$export->headings(), $googleRow],
        ]))->toSimpleObject()), true);
        $this->assertSame(range(0, 17), array_keys($payload['values'][1]));
    }
}
