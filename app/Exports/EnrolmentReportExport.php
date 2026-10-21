<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EnrolmentReportExport implements FromArray, WithEvents, WithHeadings
{
    private Collection $rows;
    private array $totals;

    public function __construct(Collection $rows, array $totals)
    {
        $this->rows = $rows;
        $this->totals = $totals;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->rows as $index => $row) {
            $data[] = [
                $index + 1,
                $row->level,
                $row->active_children,
                $row->cancelled_children,
                $row->expired,
                $row->pending,
                $row->paid,
                $row->total_form,
                $row->dp_expired,
                $row->dp_pending,
                $row->dp_paid,
                $row->dp_total,
                $row->enrolment_target,
                $row->enrolment_target > 0
                    ? round(($row->dp_paid / $row->enrolment_target) * 100, 1)
                    : 0,
            ];
        }

        $data[] = [
            '',
            'Total',
            $this->totals['active_children'],
            $this->totals['cancelled_children'],
            $this->totals['expired'],
            $this->totals['pending'],
            $this->totals['paid'],
            $this->totals['total_form'],
            $this->totals['dp_expired'],
            $this->totals['dp_pending'],
            $this->totals['dp_paid'],
            $this->totals['dp_total'],
            $this->totals['enrolment_target'],
            $this->totals['enrolment_target'] > 0
                ? round(($this->totals['dp_paid'] / $this->totals['enrolment_target']) * 100, 1)
                : 0,
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            ['No', 'Level', 'Status Anak', '', 'Enrolment Form', '', '', '', 'DP', '', '', '', 'Target', ''],
            ['', '', 'Active', 'Cancel', 'Expired', 'Pending', 'Paid', 'Total', 'Expired', 'Pending', 'Paid', 'Total', 'Siswa', '% Target'],
        ];
    }

    public function registerEvents(): array
    {
        $lastRow = $this->rows->count() + 3;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($lastRow) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:D1');
                $sheet->mergeCells('E1:H1');
                $sheet->mergeCells('I1:L1');
                $sheet->mergeCells('M1:N1');

                $sheet->getStyle('A1:N2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '475467']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F3F6FB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E8EDF3'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A3:N{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E8EDF3'],
                        ],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("E1:E{$lastRow}")
                    ->getBorders()->getLeft()
                    ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('98A2B3');
                $sheet->getStyle("I1:I{$lastRow}")
                    ->getBorders()->getLeft()
                    ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('98A2B3');
                $sheet->getStyle("M1:M{$lastRow}")
                    ->getBorders()->getLeft()
                    ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('98A2B3');
                $sheet->getStyle("A{$lastRow}:N{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F3F6FB'],
                    ],
                ]);
                $sheet->getStyle("N3:N{$lastRow}")
                    ->getNumberFormat()->setFormatCode('0.0"%"');

                foreach (range('A', 'N') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
