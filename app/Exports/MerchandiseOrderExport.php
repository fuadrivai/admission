<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MerchandiseOrderExport implements FromCollection, WithHeadings, WithMapping
{
    private $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Order Date',
            'Order Type',
            'Parent Name',
            'Child Name',
            'Email',
            'Phone',
            'Portal Username',
            'Branch',
            'Division',
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

    public function map($order): array
    {
        $items = $order->items->map(function ($item) {
            $name = trim(($item->product_name ?: '') . ' - ' . ($item->variant_name ?: ''), ' -');

            return $name . ' x ' . $item->quantity;
        })->implode('; ');

        return [
            $order->code,
            optional($order->created_at)->format('Y-m-d H:i:s'),
            $order->order_type,
            $order->parent_name,
            $order->child_name,
            $order->email,
            $order->phone,
            $order->portal_username,
            $order->branch_name,
            $order->division_name,
            $order->display_payment_status,
            $order->fulfillment_status,
            $items,
            $order->total_items,
            $order->subtotal,
            $order->discount_amount,
            $order->bank_charger,
            $order->total_amount,
            optional($order->paid_at)->format('Y-m-d H:i:s'),
            optional($order->expires_at)->format('Y-m-d H:i:s'),
        ];
    }
}
