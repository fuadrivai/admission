<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EnrolmentReportExport implements FromArray, WithHeadings
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
                $row->expired,
                $row->cancelled,
                $row->pending,
                $row->paid,
                $row->total_form,
                $row->seat_rsvp,
                $row->enrolment_target,
                $row->enrolment_target > 0
                    ? round(($row->seat_rsvp / $row->enrolment_target) * 100, 1)
                    : 0,
            ];
        }

        $data[] = [
            '',
            'Total',
            $this->totals['expired'],
            $this->totals['cancelled'],
            $this->totals['pending'],
            $this->totals['paid'],
            $this->totals['total_form'],
            $this->totals['seat_rsvp'],
            $this->totals['enrolment_target'],
            $this->totals['enrolment_target'] > 0
                ? round(($this->totals['seat_rsvp'] / $this->totals['enrolment_target']) * 100, 1)
                : 0,
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Level',
            'Expired',
            'Cancelled',
            'Pending',
            'Paid',
            'Total Form',
            'DP',
            'Target Siswa',
            '% Target',
        ];
    }
}
