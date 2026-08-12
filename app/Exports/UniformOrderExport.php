<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;

class UniformOrderExport implements 
    FromCollection, 
    WithMapping, 
    WithHeadings, 
    WithEvents
{
    protected $orders;
    private $rowNumber = 1;

    public function __construct($orders)
    {
        $rows = collect();
        foreach ($orders as $order) {
            if ($order->details && $order->details->count() > 0) {
                foreach ($order->details as $detail) {
                    $rows->push([
                        'order' => $order,
                        'detail' => $detail,
                    ]);
                }
            } else {
                $rows->push([
                    'order' => $order,
                    'detail' => null,
                ]);
            }
        }
        $this->orders = $rows;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function map($item): array
    {
        $order = $item['order'];
        $detail = $item['detail'];

        $orderDate   = $order->created_at ? Carbon::parse($order->created_at)->format('d F Y H:i') : '-';
        $paymentDate = $order->payment_date ? Carbon::parse($order->payment_date)->format('d F Y H:i') : '-';
        $pickedUpDate = $order->picked_up_at ? Carbon::parse($order->picked_up_at)->format('d F Y H:i') : '-';

        return [
            $this->rowNumber++,
            $order->code ?? '-',
            $orderDate,
            $order->student_name ?? '-',
            $order->branch_name ?? $order->branch->name ?? '-',
            $order->level_name ?? $order->level->name ?? '-',
            $order->grade_name ?? $order->grade->name ?? '-',
            $order->parent_name ?? '-',
            $order->parent_email ?? '-',
            $order->parent_phone ?? '-',
            $detail->product_code ?? '-',
            $detail->product_name ?? '-',
            strtoupper($detail->unit_type ?? '-'),
            $detail->size ?? '-',
            $detail->qty ?? 0,
            $detail->price ?? 0,
            $detail->subtotal ?? 0,
            $order->subtotal ?? 0,
            $order->bank_charger ?? 0,
            $order->total_amount ?? 0,
            strtoupper($order->payment_status ?? 'UNPAID'),
            $paymentDate,
            $pickedUpDate,
        ];
    }

    public function headings(): array
    {
        return [
            ['Uniform Orders & Items Detail Report'],
            ['Export Date: ' . Carbon::now()->format('d F Y H:i:s')],
            [],
            [
                'No',
                'Order Code',
                'Order Date',
                'Student Name',
                'Branch',
                'Level',
                'Grade',
                'Parent Name',
                'Parent Email',
                'Parent Phone',
                'Product Code',
                'Product Name',
                'Unit Type',
                'Size',
                'Qty',
                'Unit Price (IDR)',
                'Item Subtotal (IDR)',
                'Order Subtotal (IDR)',
                'Bank Charge (IDR)',
                'Grand Total (IDR)',
                'Payment Status',
                'Payment Date',
                'Picked Up Date'
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:W1');
                $sheet->mergeCells('A2:W2');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '1E3A8A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['rgb' => '64748B'],
                    ],
                ]);

                $sheet->getStyle('A4:W4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E3A8A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $lastRow = $sheet->getHighestRow();

                if ($lastRow >= 5) {
                    $sheet->getStyle('A5:W' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E2E8F0'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B5:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('C5:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K5:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('M5:O' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('P5:T' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('U5:W' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                foreach (range('A', 'W') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
