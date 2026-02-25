<?php

namespace App\Helpers;

use App\Models\EmailSetting;
use App\Models\Observation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

function codeGenerator($table,$column,$prefix)
{
    $currDate = date("ymd");

    $last = DB::table($table)
        ->where($column, 'like', "{$prefix}{$currDate}%")
        ->orderBy($column, 'desc')
        ->lockForUpdate()
        ->first();

    if ($last) {
        $n = (int) substr($last->$column, -4);
        $n = str_pad($n + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $n = '0001';
    }

    return "{$prefix}{$currDate}{$n}";
}

function generate($prefix, $table = 'prospects', $column = 'code')
{
    $lastCode = DB::table($table)
        ->where($column, 'like', $prefix . '%')
        ->orderBy($column, 'desc')
        ->value($column);
    if (!$lastCode) {
        return $prefix . '20';
    }
    $number = (int) str_replace($prefix, '', $lastCode);
    $nextNumber = $number + 1;
    return $prefix . $nextNumber;
}

function generateUniqueCode()
{
    do {
        $code = strtoupper(Str::random(5));
    } while (DB::table('school_visits')->where('code', $code)->exists());

    return $code;
}


function generateNoLetter($level)
{
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

    $now = Carbon::now();
    $month = $now->month;
    $year = $now->year;

    $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
    $romanMonth = $romanMonths[$month];

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

    if ($latest && preg_match('/^(\d+)\//', $latest->no_letter, $match)) {
        $number = (int)$match[1] + 1;
    } else {
        $number = 1;
    }

    $noLetter = sprintf("%d/PSB/%s/B/%s/%d", $number, $levelCode, $romanMonth, $year);

    return $noLetter;
}

function sendWhatsapp($phonenumber,$sender, $message)
{

    $phone = normalizePhoneNumber($phonenumber);
    return Http::withHeaders(['Content-Type' => 'application/json'])
        ->post('https://whatsapp.mhis.link/apiv2/send-message.php', [
            "api_key" => env('WHATSAPP_KEY'),
            "sender" => $sender,
            "number" => $phone,
            "message" => $message,
        ])
        ->json();
}

function normalizePhoneNumber($phone)
{
    $phone = preg_replace('/\D+/', '', $phone);
    if (strpos($phone, '62') === 0) {
        return $phone;
    }
    if (strpos($phone, '0') === 0) {
        return '62' . substr($phone, 1);
    }
    if (strpos($phone, '8') === 0) {
        return '62' . $phone;
    }
    return $phone;
}

function createXenditInvoice(array $payload)
{
    $apiKey = env('XENDIT_API_KEY');
    $response = Http::withBasicAuth($apiKey, '')
        ->withHeaders([
            'Content-Type' => 'application/json'
        ])
        ->post('https://api.xendit.co/v2/invoices', $payload);
    if ($response->failed()) {
        return [
            'success' => false,
            'status' => $response->status(),
            'message' => $response->json()['message'] ?? 'Failed creating invoice',
        ];
    }
    return $response->json();
}

function imageToBase64($path)
{
    if (!file_exists($path)) {
        return null;
    }

    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);

    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}


function setupMail($branchId)
{
    $setting = EmailSetting::where('branch_id', $branchId)->first();
    Config::set('mail.default', 'smtp');
    Config::set('mail.mailers.smtp', [
        'transport' => $setting->mailer,
        'host'      => $setting->host,
        'port'      => $setting->port,
        'encryption'=> $setting->encryption,
        'username'  => $setting->username,
        'password'  => $setting->app_password,
    ]);
    Config::set('mail.from', [
        'address' => $setting->from_address,
        'name'    => $setting->from_name,
    ]);
}

function formatDate($date, $format = 'd F Y', $locale = 'en')
{
    if (empty($date)) {
        return '-';
    }
    try {
        $carbon = Carbon::parse($date)->locale($locale);
        return $locale === 'en'
            ? $carbon->format($format)
            : $carbon->translatedFormat($format);
    } catch (\Exception $e) {
        return $date;
    }
}

function avatarName($child_name)
{
    if (!$child_name || trim($child_name) === '') {
        return 'NA';
    }
    $words = explode(' ', trim($child_name));
    $initials = collect($words)
        ->filter()            // hapus spasi kosong
        ->take(2)             // ambil max 2 kata
        ->map(function ($word) {
            return strtoupper(substr($word, 0, 1));
        })
        ->implode('');
    return $initials ?: 'NA';
}
