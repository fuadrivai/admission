<?php

namespace Tests\Feature;

use App\Models\MerchandiseEmailTemplate;
use App\Models\MerchandiseOrder;
use App\Models\MerchandiseOrderItem;
use App\Services\Implement\MerchandiseImplement;
use Illuminate\Database\Eloquent\Collection;
use ReflectionMethod;
use Tests\TestCase;

class MerchandiseEmailTemplateFeatureTest extends TestCase
{
    public function test_payment_details_and_totals_are_rendered_in_one_mobile_friendly_table(): void
    {
        $order = new MerchandiseOrder([
            'parent_name' => 'Parent',
            'code' => 'MERCH-TEST',
            'branch_name' => 'Bintaro',
            'email' => 'parent@example.com',
            'phone' => '08123456789',
            'portal_username' => 'parent',
            'subtotal' => 150000,
            'discount_amount' => 0,
            'bank_charger' => 3663,
            'total_amount' => 153663,
            'payment_status' => 'PENDING',
        ]);
        $order->setRelation('items', new Collection([
            new MerchandiseOrderItem([
                'product_name' => 'Shall MHIS',
                'variant_name' => 'Hijau / 1.5 Meter',
                'quantity' => 1,
                'subtotal' => 150000,
            ]),
        ]));
        $template = new MerchandiseEmailTemplate([
            'subject' => 'Invoice {{ order_code }}',
            'body' => '<p>{{ order_items }}</p>'
                . '<p><strong>Subtotal:</strong> {{ subtotal }}<br>'
                . '<strong>Discount:</strong> {{ discount }}<br>'
                . '<strong>Admin fee:</strong> {{ admin_fee }}<br>'
                . '<strong>Total payment:</strong> {{ total }}</p>'
                . '<p><a href="{{ payment_url }}">Pay now</a></p>',
        ]);
        $method = new ReflectionMethod(MerchandiseImplement::class, 'merchandiseEmailData');
        $method->setAccessible(true);

        $emailData = $method->invoke(new MerchandiseImplement(), $order, $template, []);
        $content = $emailData['content'];

        $this->assertStringContainsString('table-layout:fixed', $content);
        $this->assertStringContainsString('Shall MHIS', $content);
        $this->assertStringContainsString('Rp150.000', $content);
        $this->assertStringContainsString('Rp3.663', $content);
        $this->assertStringContainsString('Rp153.663', $content);
        $this->assertStringContainsString('Total payment', $content);
        $this->assertStringNotContainsString('<strong>Subtotal:</strong>', $content);
        $this->assertStringContainsString('line-height:48px', $content);
        $this->assertStringContainsString('width:260px', $content);
        $this->assertStringContainsString('href=""', $content);
    }

    public function test_merchandise_email_uses_branded_responsive_layout_and_renders_payment_table(): void
    {
        $paymentTable = '<table role="presentation" width="100%" style="width:100%;table-layout:fixed">'
            . '<tr><td>Product</td><td>Qty</td><td>Amount</td></tr>'
            . '<tr><td>Shall MHIS</td><td>1</td><td>Rp150.000</td></tr>'
            . '<tr><td colspan="2">Total payment</td><td>Rp153.663</td></tr></table>';

        $html = view('email-template.merchandise', [
            'data' => [
                'title' => 'Merchandise Invoice',
                'content' => '<p>Dear Parent,</p>' . $paymentTable,
            ],
        ])->render();

        $this->assertStringContainsString('name="viewport"', $html);
        $this->assertStringContainsString('LOGO-5-1536x864-1-1024x576.png', $html);
        $this->assertStringContainsString('table-layout:fixed', $html);
        $this->assertStringContainsString('Shall MHIS', $html);
        $this->assertStringContainsString('Rp153.663', $html);
        $this->assertStringContainsString('background-color:#f9f7f4', $html);
        $this->assertStringContainsString('Home of The Champions', $html);
        $this->assertStringContainsString('&copy; 2025 Mutiara Harapan Islamic School. All Rights Reserved.', $html);
        $this->assertStringContainsString('Tangerang Selatan – 15426', $html);
        $this->assertStringContainsString('padding:25px 20px;background-color:#f5f1ec;color:#777;font-size:12px;line-height:1.5', $html);
    }
}
