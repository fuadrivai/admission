<?php

namespace App\Helpers;

use App\Models\Observation;

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
