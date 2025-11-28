<?php

namespace App\Http\Controllers;

use App\Services\SiswaEreportService;
use Illuminate\Http\Request;

class SiswaEreportController extends Controller
{
    private SiswaEreportService $siswaService;

    public function __construct(SiswaEreportService $siswaService)
    {
        $this->siswaService = $siswaService;
    }

    public function getSiswaByNis(Request $request){

        $validated = $request->validate([
            'level'=>'required',
            'branch'=>'required',
            'nis'=>'required',
        ]);

        session(['school_branch' => $request->branch]);
        session(['level' => $request->level]);

        $siswa = $this->siswaService->getByNis($validated);
        return response()->json($siswa);
    }
}
