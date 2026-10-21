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
    private Collection $historicalRows;
    private array $historicalTotals;
    private ?string $historicalYearName;

    public function __construct(
        Collection $rows,
        array $totals,
        ?Collection $historicalRows = null,
        ?array $historicalTotals = null,
        ?string $historicalYearName = null
    ) {
        $this->rows = $rows;
        $this->totals = $totals;
        $this->historicalRows = $historicalRows ?? collect();
        $this->historicalTotals = $historicalTotals ?? [];
        $this->historicalYearName = $historicalYearName;
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

        if ($this->historicalYearName) {
            $data[] = array_fill(0, 14, '');
            $data[] = ['', 'Snapshot Historis: ' . $this->historicalYearName];
            $data[] = [
                'No',
                'Level / Grade',
                'Active',
                'Cancel',
                'Enrolment Expired',
                'Enrolment Pending',
                'Enrolment Paid',
                'Enrolment Total',
                'DP Expired',
                'DP Pending',
                'DP Paid',
                'DP Total',
                'Target Siswa',
                '% Target',
            ];

            foreach ($this->historicalRows as $index => $row) {
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

            if ($this->historicalRows->isNotEmpty()) {
                $data[] = [
                    '',
                    'Total Historis',
                    $this->historicalTotals['active_children'],
                    $this->historicalTotals['cancelled_children'],
                    $this->historicalTotals['expired'],
                    $this->historicalTotals['pending'],
                    $this->historicalTotals['paid'],
                    $this->historicalTotals['total_form'],
                    $this->historicalTotals['dp_expired'],
                    $this->historicalTotals['dp_pending'],
                    $this->historicalTotals['dp_paid'],
                    $this->historicalTotals['dp_total'],
                    $this->historicalTotals['enrolment_target'],
                    $this->historicalTotals['enrolment_target'] > 0
                        ? round(($this->historicalTotals['dp_paid'] / $this->historicalTotals['enrolment_target']) * 100, 1)
                        : 0,
                ];
            } else {
                $data[] = ['', 'Belum ada snapshot historis untuk filter ini.'];
            }
        }

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
        $lastRow = count($this->array()) + 2;
        $currentTotalRow = $this->rows->count() + 3;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($lastRow, $currentTotalRow) {
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
                if ($this->historicalRows->isNotEmpty()) {
                    $sheet->getStyle("A{$lastRow}:N{$lastRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => 'F3F6FB'],
                        ],
                    ]);
                }
                $sheet->getStyle("N3:N{$lastRow}")
                    ->getNumberFormat()->setFormatCode('0.0"%"');
                $sheet->getStyle("A{$currentTotalRow}:N{$currentTotalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F3F6FB'],
                    ],
                ]);

                if ($this->historicalYearName) {
                    $historicalTitleRow = $this->rows->count() + 5;
                    $historicalHeaderRow = $historicalTitleRow + 1;
                    $sheet->getStyle("A{$historicalTitleRow}:N{$historicalHeaderRow}")
                        ->applyFromArray([
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
                    $sheet->getStyle("A{$lastRow}:N{$lastRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => 'F3F6FB'],
                        ],
                    ]);
                }

                foreach (range('A', 'N') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
