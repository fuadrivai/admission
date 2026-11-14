<?php

namespace Tests\Feature;

use App\Exports\MerchandiseOrderExport;
use App\Models\MerchandiseOrder;
use App\Models\MerchandiseOrderItem;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MerchandiseOrderExportTest extends TestCase
{
    public function test_order_export_includes_order_and_item_details(): void
    {
        $order = new MerchandiseOrder([
            'code' => 'MERCH-TEST',
            'created_at' => '2026-11-14 10:00:00',
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
            'expires_at' => '2026-11-15 10:00:00',
        ]);
        $order->setAttribute('display_payment_status', 'PAID');
        $order->setRelation('items', new Collection([
            new MerchandiseOrderItem([
                'product_name' => 'Shall MHIS',
                'variant_name' => 'Kuning / 1.5 Meter',
                'quantity' => 2,
            ]),
        ]));

        $export = new MerchandiseOrderExport(collect([$order]));
        $row = $export->map($order);

        $this->assertCount(18, $export->headings());
        $this->assertCount(18, $row);
        $this->assertSame('MERCH-TEST', $row[0]);
        $this->assertSame('PAID', $row[8]);
        $this->assertSame('Shall MHIS - Kuning / 1.5 Meter x 2', $row[10]);
        $this->assertSame(241500.0, $row[15]);
    }
}
