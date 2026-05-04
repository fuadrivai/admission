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

class EnrolmentExport implements 
    FromCollection, 
    WithMapping, 
    WithHeadings, 
    WithEvents
{
    protected $enrolments;
    private $rowNumber = 1;

    public function __construct($data)
    {
        $this->enrolments = $data;
    }

    public function collection()
    {
        return $this->enrolments;
    }

    public function map($enrolment): array
    {
        if (isset($enrolment->prospect) && isset($enrolment->prospect->enrolment)) {
            $enrolment = $enrolment->prospect->enrolment;
        }

        $dateOfBirth = $enrolment->date_of_birth ? Carbon::parse($enrolment->date_of_birth)->format('d F Y') : '-';
        $paymentDate = $enrolment->payment_date ? Carbon::parse($enrolment->payment_date)->format('d F Y H:i') : '-';
        $createVaDate = $enrolment->create_va_date ? Carbon::parse($enrolment->create_va_date)->format('d F Y H:i') : '-';
        $expiryVaDate = $enrolment->expiry_va_date ? Carbon::parse($enrolment->expiry_va_date)->format('d F Y H:i') : '-';
        $createdAt = $enrolment->created_at ? Carbon::parse($enrolment->created_at)->format('d F Y H:i') : '-';
        $updatedAt = $enrolment->updated_at ? Carbon::parse($enrolment->updated_at)->format('d F Y H:i') : '-';

        return [
            $this->rowNumber++,
            $enrolment->code ?? '-',
            $enrolment->already_visit ?? '-',
            $enrolment->is_current_student ?? '-',
            $enrolment->mhis_portal_username ?? '-',
            $enrolment->branch->name ?? '-',
            $enrolment->level->name ?? '-',
            $enrolment->grade->name ?? '-',
            $enrolment->academic_year ?? '-',
            $enrolment->parent_name ?? '-',
            $enrolment->email ?? '-',
            $enrolment->phone_number ?? '-',
            $enrolment->relationship ?? '-',
            $enrolment->zipcode ?? '-',
            $enrolment->address ?? '-',
            $enrolment->child_name ?? '-',
            $dateOfBirth,
            $enrolment->place_of_birth ?? '-',
            $enrolment->current_school ?? '-',
            $enrolment->child_sosmed ?? '-',
            $enrolment->open_day_visited ?? '-',
            $enrolment->knowledge_about_program ?? '-',
            $enrolment->info_from ?? '-',
            $enrolment->info_from_message ?? '-',
            $enrolment->reason_for_enrolment ?? '-',
            $enrolment->preferred_program ?? '-',
            $enrolment->expectation_mhis_impact ?? '-',
            $enrolment->trust_reason ?? '-',
            $enrolment->recommender_name ?? '-',
            $enrolment->recommender_phone ?? '-',
            $enrolment->recommender_child_name ?? '-',
            $enrolment->recommender_child_class ?? '-',
            $enrolment->registration_fee ?? '-',
            $enrolment->custom_payment ?? '-',
            $enrolment->bank_charger ?? '-',
            $enrolment->discount ?? '-',
            $enrolment->amount_paid ?? '-',
            $enrolment->invoice_id ?? '-',
            $enrolment->payment_status ?? '-',
            $paymentDate,
            $enrolment->source_data ?? '-',
            $enrolment->noted ?? '-',
            $enrolment->regis_place ?? '-',
            $enrolment->data_from ?? '-',
            $createdAt,
        ];
    }

    public function headings(): array
    {
        return [
            ['Admission Enrolment Report'],
            [],
            [
                'No',
                'Code',
                'Already Visit',
                'Is Current Student',
                'MHIS Portal Username',
                'Branch',
                'Level',
                'Grade',
                'Academic Year',
                'Parent Name',
                'Email',
                'Phone Number',
                'Relationship',
                'Zipcode',
                'Address',
                'Child Name',
                'Date Of Birth',
                'Place Of Birth',
                'Current School',
                'Child Sosmed',
                'Open Day Visited',
                'Knowledge About Program',
                'Info From',
                'Info From Message',
                'Reason For Enrolment',
                'Preferred Program',
                'Expectation MHIS Impact',
                'Trust Reason',
                'Recommender Name',
                'Recommender Phone',
                'Recommender Child Name',
                'Recommender Child Class',
                'Registration Fee',
                'Custom Payment',
                'Bank Charger',
                'Discount',
                'Amount Paid',
                'Invoice ID',
                'Payment Status',
                'Payment Date',
                'Source Data',
                'Noted',
                'Regis Place',
                'Data From',
                'Created At',
            ],
        ];
    }

    public function registerEvents(): array
    {
        $enrolments = $this->enrolments;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($enrolments) {
                $sheet = $event->sheet->getDelegate();

                //  TITLE
                $sheet->mergeCells('A1:AS1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getRowDimension(1)->setRowHeight(25);

                //  HEADER
                $sheet->getStyle('A3:AS3')->getFont()->setBold(true);
                $sheet->getStyle('A3:AS3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3:AS3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Background header
                $sheet->getStyle('A3:AS3')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEFEFEF');

                // Border header
                $sheet->getStyle('A3:AS3')->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // DATA RANGE
                $endRow = ($enrolments ? $enrolments->count() : 0) + 4;

                // Border data
                $sheet->getStyle("A4:AS{$endRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // // Wrap text (kolom panjang)
                // $sheet->getStyle("P4:P{$endRow}")
                //     ->getAlignment()
                //     ->setWrapText(true);

                // $sheet->getStyle("K4:P{$endRow}")
                //     ->getAlignment()
                //     ->setWrapText(true);

                // Vertical align
                $sheet->getStyle("A4:AS{$endRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_TOP);

                // AUTO WIDTH
                foreach (range('A', 'AS') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
