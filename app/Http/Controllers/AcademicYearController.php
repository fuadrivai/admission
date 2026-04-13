<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class AcademicYearController extends Controller
{
    private AcademicYearService $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index()
    {
        return view('academic-year.index', ['title' => 'Academic Year']);
    }

    public function datatables(UtilitiesRequest $request)
    {
        $query = AcademicYear::query();

        if ($request->ajax()) {
            return datatables()->of($query)->make(true);
        }

        return response()->json([]);
    }

    public function create()
    {
        return view('academic-year.form', ['title' => 'Create Academic Year']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'name' => $validated['name'],
            'is_active' => isset($validated['is_active']) && $validated['is_active'] ? 'true' : 'false',
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ];

        $academicYear = $this->academicYearService->post($data);

        return response()->json($academicYear);
    }

    public function show($id)
    {
        $academicYear = $this->academicYearService->show($id);

        return response()->json($academicYear);
    }

    public function edit($id)
    {
        $academicYear = $this->academicYearService->show($id);

        return view('academic-year.form', ['title' => 'Edit Academic Year', 'academicYear' => $academicYear]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'id' => $validated['id'],
            'name' => $validated['name'],
            'is_active' => isset($validated['is_active']) && $validated['is_active'] ? 'true' : 'false',
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ];

        $academicYear = $this->academicYearService->put($data);

        return response()->json($academicYear);
    }

    public function destroy(AcademicYear $academicYear)
    {
        $this->academicYearService->delete($academicYear->id);

        return response()->json(['success' => true]);
    }
}
