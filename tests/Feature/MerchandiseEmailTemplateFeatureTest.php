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

    public function test_payment_receipt_email_uses_uniform_payment_style(): void
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
            'payment_status' => 'PAID',
        ]);
        $order->setRelation('items', new Collection());
        $template = new MerchandiseEmailTemplate([
            'key' => 'receipt',
            'subject' => 'Receipt {{ order_code }}',
            'body' => '<p>{{ payment_table }}</p>',
        ]);
        $method = new ReflectionMethod(MerchandiseImplement::class, 'merchandiseEmailData');
        $method->setAccessible(true);

        $emailData = $method->invoke(new MerchandiseImplement(), $order, $template, [
            'email_title' => 'Merchandise Payment Confirmation',
        ]);
        $html = view($emailData['template'], ['data' => $emailData])->render();

        $this->assertSame('email-template.merchandise-payment', $emailData['template']);
        $this->assertStringContainsString('Uniform Payment', file_get_contents(resource_path('views/email-template/uniform-payment.blade.php')));
        $this->assertStringContainsString('Merchandise Payment Confirmation', $html);
        $this->assertStringContainsString('We have received your merchandise payment', $html);
        $this->assertStringContainsString('With warm regards', $html);
        $this->assertStringContainsString('Home of The Champions', $html);
        $this->assertStringContainsString('wa.me/6281291823247', $html);
    }

    public function test_merchandise_receipt_pdf_uses_uniform_receipt_layout_with_order_totals(): void
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
            'payment_status' => 'PAID',
        ]);
        $order->setRelation('items', new Collection([
            new MerchandiseOrderItem([
                'product_name' => 'Shall MHIS',
                'variant_name' => 'Hijau / 1.5 Meter',
                'quantity' => 1,
                'unit_price' => 150000,
                'subtotal' => 150000,
            ]),
        ]));

        $html = view('pdf.merchandise-receipt', [
            'order' => $order,
            'description' => 'Merchandise order payment',
            'logo' => 'data:image/png;base64,logo',
        ])->render();

        $this->assertStringContainsString('Merchandise Receipt', $html);
        $this->assertStringContainsString('Customer Information', $html);
        $this->assertStringContainsString('Payment Details', $html);
        $this->assertStringContainsString('Transaction No:', $html);
        $this->assertStringContainsString('Shall MHIS', $html);
        $this->assertStringContainsString('Discount', $html);
        $this->assertStringContainsString('TOTAL PAID', $html);
        $this->assertStringContainsString('Rp 153.663', $html);
    }
}
