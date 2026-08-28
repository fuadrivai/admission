<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AcademicYearService;
use App\Services\BranchService;
use App\Services\GradeService;
use App\Services\LevelService;
use Illuminate\Http\Request;

class AcademicStructureApiController extends Controller
{
    private AcademicYearService $academicYearService;
    private BranchService $branchService;
    private LevelService $levelService;
    private GradeService $gradeService;

    public function __construct(
        AcademicYearService $academicYearService,
        BranchService $branchService,
        LevelService $levelService,
        GradeService $gradeService
    ) {
        $this->academicYearService = $academicYearService;
        $this->branchService = $branchService;
        $this->levelService = $levelService;
        $this->gradeService = $gradeService;
    }

    public function academicYears()
    {
        return response()->json($this->academicYearService->get());
    }

    public function activeAcademicYears()
    {
        return response()->json($this->academicYearService->getActive());
    }

    public function branches()
    {
        return response()->json($this->branchService->get());
    }

    public function levels(Request $request)
    {
        $levels = $request->filled('branch_id')
            ? $this->levelService->getByBranch($request->input('branch_id'), $request->input('search'))
            : $this->levelService->get();

        return response()->json($levels);
    }

    public function levelByBranch($branchId, Request $request)
    {
        return response()->json(
            $this->levelService->getByBranch($branchId, $request->input('search'))
        );
    }

    public function grades(Request $request)
    {
        $grades = $request->filled('level_id')
            ? $this->gradeService->byLevelId($request->input('level_id'))
            : $this->gradeService->get();

        return response()->json($grades);
    }

    public function gradeByLevel($levelId)
    {
        return response()->json($this->gradeService->byLevelId($levelId));
    }
}
