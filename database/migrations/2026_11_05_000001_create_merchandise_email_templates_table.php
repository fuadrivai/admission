<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseEmailTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('merchandise_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 30)->unique();
            $table->string('name', 100);
            $table->string('subject', 255);
            $table->longText('body');
            $table->timestamps();
        });

        $now = now();
        DB::table('merchandise_email_templates')->insert([
            [
                'key' => 'invoice',
                'name' => 'Payment Invoice',
                'subject' => 'Merchandise Invoice {{ order_code }} - Mutiara Harapan Islamic School',
                'body' => '<p>Dear {{ parent_name }},</p><p>Thank you for your merchandise order. Please review the payment details below.</p><p><strong>Order:</strong> {{ order_code }}<br><strong>Order date:</strong> {{ order_date }}<br><strong>Branch:</strong> {{ branch_name }}</p>{{ payment_table }}<p style="text-align:center;margin:24px 0"><a href="{{ payment_url }}" style="display:inline-block;background-color:#800000;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold">Pay now</a></p><p>Please complete payment before {{ expires_at }}.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'receipt',
                'name' => 'Payment Receipt',
                'subject' => 'Merchandise Payment Receipt {{ order_code }} - Mutiara Harapan Islamic School',
                'body' => '<p>Dear {{ parent_name }},</p><p>We have received your payment. Thank you for your merchandise order.</p><p><strong>Order:</strong> {{ order_code }}<br><strong>Payment date:</strong> {{ payment_date }}<br><strong>Branch:</strong> {{ branch_name }}</p>{{ payment_table }}<p>Your receipt is attached to this email.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->dateTime('invoice_email_sent_at')->nullable();
            $table->dateTime('receipt_email_sent_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_email_sent_at', 'receipt_email_sent_at']);
        });

        Schema::dropIfExists('merchandise_email_templates');
    }
}
