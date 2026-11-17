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
            'child_name' => 'Child Name',
            'division_name' => 'Primary',
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

        $this->assertCount(20, $export->headings());
        $this->assertCount(20, $row);
        $this->assertSame('MERCH-TEST', $row[0]);
        $this->assertSame('Child Name', $row[4]);
        $this->assertSame('Bintaro', $row[8]);
        $this->assertSame('Primary', $row[9]);
        $this->assertSame('PAID', $row[10]);
        $this->assertSame('Shall MHIS - Kuning / 1.5 Meter x 2', $row[12]);
        $this->assertEquals(241500, $row[17]);

        $googleRowMethod = new ReflectionMethod(
            \App\Google\MerchandiseOrderGoogleSheetService::class,
            'orderRow'
        );
        $googleRowMethod->setAccessible(true);
        $googleRow = $googleRowMethod->invoke(new \App\Google\MerchandiseOrderGoogleSheetService(), $order);

        $googleHeadingsMethod = new ReflectionMethod(
            \App\Google\MerchandiseOrderGoogleSheetService::class,
            'headings'
        );
        $googleHeadingsMethod->setAccessible(true);
        $this->assertSame(
            $export->headings(),
            $googleHeadingsMethod->invoke(new \App\Google\MerchandiseOrderGoogleSheetService())
        );
        $this->assertSame('Child Name', $googleRow[4]);
        $this->assertSame('Bintaro', $googleRow[8]);
        $this->assertSame('Primary', $googleRow[9]);
        $this->assertSame('', $googleRow[18]);
        $this->assertSame('', $googleRow[19]);
        $this->assertSame(range(0, 19), array_keys($googleRow));

        $payload = json_decode(json_encode((new Sheets\ValueRange([
            'values' => [$export->headings(), $googleRow],
        ]))->toSimpleObject()), true);
        $this->assertSame(range(0, 19), array_keys($payload['values'][1]));
    }
}
