<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateMerchandiseInvoiceEmailBody extends Migration
{
    public function up()
    {
        DB::table('merchandise_email_templates')
            ->where('key', 'invoice')
            ->update([
                'body' => '<p><strong>Dear MHIS Family,</strong></p>'
                    . '<p>Thank you for your interest in MHIS Merchandise!</p>'
                    . '<p>We have received your order. To proceed with your purchase, kindly complete your payment using the details provided below.</p>'
                    . '<p><strong>Payment Details</strong></p>'
                    . '<ul>'
                    . '<li>Order Number: {{ order_code }}</li>'
                    . '<li>Account Name: Mr/Ms {{ parent_name }}</li>'
                    . '<li>Account Number: {{ phone }}</li>'
                    . '</ul>'
                    . '{{ payment_table }}'
                    . '<p style="text-align:center;margin:24px 0"><a href="{{ payment_url }}" style="display:inline-block;background-color:#800000;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold">Pay now</a></p>'
                    . '<p>Once the payment has been completed, kindly keep your payment receipt for reference.</p>'
                    . '<p>Your order will be processed once the payment has been verified. A confirmation email will be sent to you with further information regarding your merchandise collection.</p>'
                    . '<p>Thank you for your support and for being part of the MHIS community.</p>',
                'updated_at' => now(),
            ]);
    }

    public function down()
    {
        // Keep customized templates intact when rolling back.
    }
}
