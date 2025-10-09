<?php

namespace App\Helpers;

use App\Models\Observation;
use Carbon\Carbon;

function codeGenerator($prefix)
{
    $currDate = date("ymd");

    $last = Observation::where('code', 'like', "{$prefix}{$currDate}%")
        ->orderBy('code', 'desc')
        ->first();

    if ($last) {
        $n = (int) substr($last->code, -4);
        $n = str_pad($n + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $n = '0001';
    }

    return "{$prefix}{$currDate}{$n}";
}

function generateNoLetter($level)
{
    // 🧭 Pemetaan level ke divisi besar
    $divisionMap = [
        'Playgroup' => 'Preschool',
        'Kindergarten' => 'Preschool',
        'Preschool Development Class' => 'Preschool',
        'Primary' => 'Primary',
        'Primary Development Class' => 'Primary',
        'Lower Secondary' => 'Secondary',
        'Upper Secondary' => 'Secondary',
        'Junior High Development Class' => 'Secondary',
    ];

    // 🧭 Pemetaan level ke kode yang muncul di surat
    $levelCodeMap = [
        'Playgroup' => 'PGKG - MH',
        'Kindergarten' => 'PGKG - MH',
        'Preschool Development Class' => 'PGKG - MH',
        'Primary' => 'SD - MH',
        'Primary Development Class' => 'SD - MH',
        'Lower Secondary' => 'SMP - MH',
        'Upper Secondary' => 'SMA - MH',
        'Junior High Development Class' => 'SMP - MH',
    ];

    $division = $divisionMap[$level] ?? 'Other';
    $levelCode = $levelCodeMap[$level] ?? 'MH';

    // 📅 Waktu aktif
    $now = Carbon::now();
    $month = $now->month;
    $year = $now->year;

    // 🔢 Konversi bulan ke angka Romawi
    $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
    $romanMonth = $romanMonths[$month];

    // 🧮 Cari nomor terakhir untuk divisi & tahun yang sama
    $latest = Observation::whereYear('created_at', $year)
        ->where(function ($q) use ($division) {
            if ($division === 'Preschool') {
                $q->whereIn('level', ['Playgroup', 'Kindergarten', 'Preschool Development Class']);
            } elseif ($division === 'Primary') {
                $q->whereIn('level', ['Primary', 'Primary Development Class']);
            } elseif ($division === 'Secondary') {
                $q->whereIn('level', ['Lower Secondary', 'Upper Secondary', 'Junior High Development Class']);
            }
        })
        ->whereNotNull('no_letter')
        ->orderByDesc('id')
        ->first();

    // 🧩 Ambil nomor terakhir dari no_letter (format: "63/PSB/...")
    if ($latest && preg_match('/^(\d+)\//', $latest->no_letter, $match)) {
        $number = (int)$match[1] + 1;
    } else {
        $number = 1;
    }

    $noLetter = sprintf("%d/PSB/%s/B/%s/%d", $number, $levelCode, $romanMonth, $year);

    return $noLetter;
}
