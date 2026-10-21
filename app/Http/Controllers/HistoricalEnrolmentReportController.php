<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Grade;
use App\Models\HistoricalEnrolmentReport;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HistoricalEnrolmentReportController extends Controller
{
    private const METRICS = [
        'active_children',
        'cancelled_children',
        'enrolment_expired',
        'enrolment_pending',
        'enrolment_paid',
        'dp_expired',
        'dp_pending',
        'dp_paid',
        'target',
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_if(auth()->user()->role === 'user', 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'academic_year_id' => 'nullable|integer|exists:academic_years,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);
        $hasFilters = $request->filled('academic_year_id') && $request->filled('branch_id');
        $snapshots = collect();

        if ($hasFilters) {
            $snapshots = HistoricalEnrolmentReport::with(['academicYear', 'branch', 'level', 'grade'])
                ->leftJoin('levels as report_levels', 'report_levels.id', '=', 'historical_enrolment_reports.level_id')
                ->leftJoin('divisions as report_divisions', 'report_divisions.id', '=', 'report_levels.division_id')
                ->leftJoin('grades as report_grades', 'report_grades.id', '=', 'historical_enrolment_reports.grade_id')
                ->where('historical_enrolment_reports.academic_year_id', $filters['academic_year_id'])
                ->where('historical_enrolment_reports.branch_id', $filters['branch_id'])
                ->select('historical_enrolment_reports.*')
                ->orderByRaw("CASE report_divisions.name
                    WHEN 'Preschool' THEN 1
                    WHEN 'Primary' THEN 2
                    WHEN 'Secondary' THEN 3
                    WHEN 'Development Class' THEN 4
                    ELSE 5
                END")
                ->orderBy('report_levels.name')
                ->orderBy('report_grades.name')
                ->get();
        }

        return view('enrolment.historical-report.index', [
            'title' => 'Data Historis Enrolment',
            'snapshots' => $snapshots,
            'academicYears' => AcademicYear::orderByDesc('name')->get(['id', 'name']),
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'selectedAcademicYearId' => $filters['academic_year_id'] ?? '',
            'selectedBranchId' => $filters['branch_id'] ?? '',
            'hasFilters' => $hasFilters,
        ]);
    }

    public function create()
    {
        return $this->form(new HistoricalEnrolmentReport());
    }

    public function store(Request $request)
    {
        $validated = $this->validateSnapshot($request);
        $scopeKey = $this->scopeKey($validated);
        $this->ensureScopeIsConsistent($validated);

        HistoricalEnrolmentReport::updateOrCreate(
            [
                'academic_year_id' => $validated['academic_year_id'],
                'branch_id' => $validated['branch_id'],
                'level_id' => $validated['level_id'],
                'scope_key' => $scopeKey,
            ],
            $this->snapshotValues($validated) + [
                'scope_type' => $validated['scope_type'],
                'grade_id' => $validated['scope_type'] === 'grade' ? $validated['grade_id'] : null,
                'created_by' => auth()->id(),
            ]
        );

        return redirect()->route('enrolment.historical-report.index', [
            'academic_year_id' => $validated['academic_year_id'],
            'branch_id' => $validated['branch_id'],
        ])->with('success', 'Snapshot historis berhasil disimpan.');
    }

    public function edit(HistoricalEnrolmentReport $historicalEnrolmentReport)
    {
        return $this->form($historicalEnrolmentReport);
    }

    public function update(Request $request, HistoricalEnrolmentReport $historicalEnrolmentReport)
    {
        $validated = $this->validateSnapshot($request);
        $scopeKey = $this->scopeKey($validated);
        $this->ensureScopeIsConsistent($validated, $historicalEnrolmentReport);

        $duplicate = HistoricalEnrolmentReport::where('academic_year_id', $validated['academic_year_id'])
            ->where('branch_id', $validated['branch_id'])
            ->where('level_id', $validated['level_id'])
            ->where('scope_key', $scopeKey)
            ->where('id', '!=', $historicalEnrolmentReport->id)
            ->exists();
        if ($duplicate) {
            throw ValidationException::withMessages([
                'grade_id' => 'Snapshot untuk cakupan ini sudah ada.',
            ]);
        }

        $historicalEnrolmentReport->update($this->snapshotValues($validated) + [
            'academic_year_id' => $validated['academic_year_id'],
            'branch_id' => $validated['branch_id'],
            'level_id' => $validated['level_id'],
            'scope_type' => $validated['scope_type'],
            'scope_key' => $scopeKey,
            'grade_id' => $validated['scope_type'] === 'grade' ? $validated['grade_id'] : null,
        ]);

        return redirect()->route('enrolment.historical-report.index', [
            'academic_year_id' => $validated['academic_year_id'],
            'branch_id' => $validated['branch_id'],
        ])->with('success', 'Snapshot historis berhasil diperbarui.');
    }

    public function destroy(HistoricalEnrolmentReport $historicalEnrolmentReport)
    {
        $filters = [
            'academic_year_id' => $historicalEnrolmentReport->academic_year_id,
            'branch_id' => $historicalEnrolmentReport->branch_id,
        ];
        $historicalEnrolmentReport->delete();

        return redirect()->route('enrolment.historical-report.index', $filters)
            ->with('success', 'Snapshot historis berhasil dihapus.');
    }

    private function form(HistoricalEnrolmentReport $snapshot)
    {
        return view('enrolment.historical-report.form', [
            'title' => $snapshot->exists ? 'Edit Data Historis' : 'Tambah Data Historis',
            'snapshot' => $snapshot,
            'academicYears' => AcademicYear::orderByDesc('name')->get(['id', 'name']),
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'levels' => Level::orderBy('name')->get(['id', 'name', 'branch_id']),
            'grades' => Grade::orderBy('name')->get(['id', 'name', 'level_id']),
        ]);
    }

    private function validateSnapshot(Request $request): array
    {
        $rules = [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'level_id' => [
                'required',
                'integer',
                Rule::exists('levels', 'id')->where('branch_id', $request->input('branch_id')),
            ],
            'scope_type' => 'required|in:level,grade',
            'grade_id' => [
                Rule::requiredIf($request->input('scope_type') === 'grade'),
                'nullable',
                'integer',
                Rule::exists('grades', 'id')->where('level_id', $request->input('level_id')),
            ],
        ];

        foreach (self::METRICS as $metric) {
            $rules[$metric] = 'required|integer|min:0|max:4294967295';
        }

        return $request->validate($rules);
    }

    private function ensureScopeIsConsistent(array $validated, ?HistoricalEnrolmentReport $current = null): void
    {
        $existingScope = HistoricalEnrolmentReport::where('academic_year_id', $validated['academic_year_id'])
            ->where('branch_id', $validated['branch_id'])
            ->where('level_id', $validated['level_id'])
            ->when($current, function ($query) use ($current) {
                $query->where('id', '!=', $current->id);
            })
            ->value('scope_type');

        if ($existingScope && $existingScope !== $validated['scope_type']) {
            throw ValidationException::withMessages([
                'scope_type' => 'Gunakan satu cakupan saja per level: pilih Level atau Grade, jangan dicampur.',
            ]);
        }
    }

    private function scopeKey(array $validated): string
    {
        return $validated['scope_type'] === 'level'
            ? 'level:' . $validated['level_id']
            : 'grade:' . $validated['grade_id'];
    }

    private function snapshotValues(array $validated): array
    {
        return collect(self::METRICS)->mapWithKeys(function ($metric) use ($validated) {
            return [$metric => $validated[$metric]];
        })->all();
    }
}
