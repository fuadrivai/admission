<?php

namespace App\Services\Implement;

use App\Models\SiswaEreport;
use App\Services\SiswaEreportService;

class SiswaEreportImplement implements SiswaEreportService
{
    public function getByNis($request)
    {
        $siswa = SiswaEreport::where('nis', $request['nis'])
            ->first();
        return $siswa;
    }
}
