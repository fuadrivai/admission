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

class SchoolVisitExport implements 
    FromCollection, 
    WithMapping, 
    WithHeadings, 
    WithEvents
{
    protected $visits;
    private $rowNumber = 1;

    public function __construct($data)
    {
        $this->visits = $data;
    }

    public function collection()
    {
        return $this->visits;
    }

    public function map($visit): array
    {
        $prospect = $visit->prospect;
        $enrolment = optional($prospect)->enrolment;

        $date = $visit->date 
            ? Carbon::parse($visit->date)->format('d F Y') 
            : '-';

        $time = $visit->time 
            ? Carbon::parse($visit->time)->format('H:i') 
            : '';

        $dateTime = trim($date . ' ' . $time);

        return [
            $this->rowNumber++,
            $dateTime,
            "Admission Staff",
            $visit->parent_name ?? '-',
            $visit->child_name ?? '-',
            $visit->branch_name ?? '-',
            trim(($visit->level_name ?? '') . '/' . ($visit->grade_name ?? '')),
            $visit->current_school ?? '-',
            optional($enrolment)->payment_status ?? '-',
            $visit->info_from ?? '-',
            $visit->reason_for_visit ?? '-',
            $visit->enrolment_consideration ?? '-',
            $visit->reason_for_enrol ?? '-',
            $visit->reason_not_enrol ?? '-',
            $visit->note ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            ['Admission School Visit Report'],
            [],
            [
                "No",
                "Visit Date",
                "Admission Staff",
                "Parent Name",
                "Child Name",
                "Branch",
                "Level/Grade",
                "Previous School",
                "Enrolment Status",
                "Information Source",
                "Reason why Visit MH",
                "Enrolment Consideration",
                "Reason for Enrol",
                "Reason not Enrol",
                "Note",
            ],
        ];
    }

    public function registerEvents(): array
    {
        $visits = $this->visits;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($visits) {
                $sheet = $event->sheet->getDelegate();

                //  TITLE
                $sheet->mergeCells('A1:O1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getRowDimension(1)->setRowHeight(25);

                //  HEADER
                $sheet->getStyle('A3:O3')->getFont()->setBold(true);
                $sheet->getStyle('A3:O3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3:O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Background header
                $sheet->getStyle('A3:O3')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEFEFEF');

                // Border header
                $sheet->getStyle('A3:O3')->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // DATA RANGE
                $endRow = ($visits ? $visits->count() : 0) + 4;

                // Border data
                $sheet->getStyle("A4:O{$endRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Wrap text (kolom panjang)
                $sheet->getStyle("O4:O{$endRow}")
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle("K4:O{$endRow}")
                    ->getAlignment()
                    ->setWrapText(true);

                // Vertical align
                $sheet->getStyle("A4:O{$endRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_TOP);

                // AUTO WIDTH
                foreach (range('A', 'O') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
