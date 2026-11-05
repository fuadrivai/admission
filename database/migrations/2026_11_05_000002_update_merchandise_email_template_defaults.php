<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateMerchandiseEmailTemplateDefaults extends Migration
{
    public function up()
    {
        $defaults = [
            'invoice' => [
                'old' => '<p>Dear {{ parent_name }},</p><p>Thank you for your merchandise order. Please complete your payment using the link below.</p><p><strong>Order:</strong> {{ order_code }}<br><strong>Order date:</strong> {{ order_date }}<br><strong>Branch:</strong> {{ branch_name }}</p>{{ order_items }}<p><strong>Subtotal:</strong> {{ subtotal }}<br><strong>Discount:</strong> {{ discount }}<br><strong>Admin fee:</strong> {{ admin_fee }}<br><strong>Total payment:</strong> {{ total }}</p><p><a href="{{ payment_url }}">Pay now</a></p><p>Please pay before {{ expires_at }}.</p>',
                'new' => '<p>Dear {{ parent_name }},</p><p>Thank you for your merchandise order. Please review the payment details below.</p><p><strong>Order:</strong> {{ order_code }}<br><strong>Order date:</strong> {{ order_date }}<br><strong>Branch:</strong> {{ branch_name }}</p>{{ payment_table }}<p style="text-align:center;margin:24px 0"><a href="{{ payment_url }}" style="display:inline-block;background-color:#800000;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold">Pay now</a></p><p>Please complete payment before {{ expires_at }}.</p>',
            ],
            'receipt' => [
                'old' => '<p>Dear {{ parent_name }},</p><p>We have received your payment. Thank you for your merchandise order.</p><p><strong>Order:</strong> {{ order_code }}<br><strong>Payment date:</strong> {{ payment_date }}<br><strong>Branch:</strong> {{ branch_name }}<br><strong>Total paid:</strong> {{ total }}</p>{{ order_items }}<p>Your receipt is attached to this email.</p>',
                'new' => '<p>Dear {{ parent_name }},</p><p>We have received your payment. Thank you for your merchandise order.</p><p><strong>Order:</strong> {{ order_code }}<br><strong>Payment date:</strong> {{ payment_date }}<br><strong>Branch:</strong> {{ branch_name }}</p>{{ payment_table }}<p>Your receipt is attached to this email.</p>',
            ],
        ];

        foreach ($defaults as $key => $body) {
            DB::table('merchandise_email_templates')
                ->where('key', $key)
                ->where('body', $body['old'])
                ->update([
                    'body' => $body['new'],
                    'updated_at' => now(),
                ]);
        }
    }

    public function down()
    {
        // Keep customized templates intact when rolling back.
    }
}
