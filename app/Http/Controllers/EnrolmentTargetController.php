<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\EnrolmentTarget;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EnrolmentTargetController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_if(auth()->user()->role === 'user', 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('name')->get(['id', 'name']);
        $filters = $request->validate([
            'academic_year_id' => [
                'nullable',
                Rule::in(array_merge(['all'], $academicYears->pluck('id')->map(function ($id) {
                    return (string) $id;
                })->all())),
            ],
            'branch_id' => 'nullable|integer|exists:branches,id',
            'level_id' => 'nullable|integer|exists:levels,id',
        ]);

        $selectedAcademicYearId = $filters['academic_year_id'] ?? '';
        $selectedBranchId = $filters['branch_id'] ?? '';
        $selectedLevelId = $filters['level_id'] ?? '';
        $hasTargetFilters = $request->filled('academic_year_id') && $request->filled('branch_id');

        $targets = collect();
        if ($hasTargetFilters) {
            $targets = EnrolmentTarget::with(['academicYear', 'branch', 'level', 'grade'])
                ->when($selectedAcademicYearId !== 'all', function ($query) use ($selectedAcademicYearId) {
                    $query->where('academic_year_id', $selectedAcademicYearId);
                })
                ->where('branch_id', $selectedBranchId)
                ->when($selectedLevelId, function ($query) use ($selectedLevelId) {
                    $query->where('level_id', $selectedLevelId);
                })
                ->get()
                ->sortBy([
                    ['academicYear.name', 'desc'],
                    ['branch.name', 'asc'],
                    ['level.name', 'asc'],
                    ['grade.name', 'asc'],
                ]);
        }

        return view('enrolment.target.index', [
            'title' => 'Enrolment Target',
            'targets' => $targets,
            'academicYears' => $academicYears,
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'levels' => Level::orderBy('name')->get(['id', 'name', 'branch_id']),
            'selectedAcademicYearId' => $selectedAcademicYearId,
            'selectedBranchId' => $selectedBranchId,
            'selectedLevelId' => $selectedLevelId,
            'hasTargetFilters' => $hasTargetFilters,
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|integer|exists:academic_years,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'level_id' => 'nullable|integer|exists:levels,id',
            'target_scope' => 'nullable|in:level,grade',
        ]);

        $academicYearId = old('academic_year_id', $validated['academic_year_id'] ?? '');
        $branchId = old('branch_id', $validated['branch_id'] ?? '');
        $levelId = old('level_id', $validated['level_id'] ?? '');
        $targetScope = old('target_scope', $validated['target_scope'] ?? 'grade');
        $grades = collect();
        $existingTargets = collect();
        $scopeError = null;
        $levelName = '';

        if ($academicYearId && $branchId && $levelId) {
            $level = Level::where('branch_id', $branchId)->findOrFail($levelId);
            $levelName = $level->name;
            $existing = EnrolmentTarget::where('academic_year_id', $academicYearId)
                ->where('branch_id', $branchId)
                ->where('level_id', $levelId)
                ->get();
            $existingScope = $existing->first()->target_scope ?? null;

            if ($existingScope && $existingScope !== $targetScope) {
                $scopeError = 'Level ini sudah menggunakan cakupan target ' . ($existingScope === 'level' ? 'Level' : 'Grade') . '. Hapus target yang ada sebelum mengganti cakupan.';
            } elseif ($targetScope === 'grade') {
                $grades = $level->grades()->orderBy('name')->get();
                $existingTargets = $existing->pluck('target', 'grade_id');
            } else {
                $existingTargets = $existing->pluck('target', 'grade_id');
            }
        }

        return view('enrolment.target.form', [
            'title' => 'Tambah Enrolment Target',
            'academicYears' => AcademicYear::orderByDesc('name')->get(['id', 'name']),
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'levels' => Level::orderBy('name')->get(['id', 'name', 'branch_id']),
            'selectedAcademicYearId' => $academicYearId,
            'selectedBranchId' => $branchId,
            'selectedLevelId' => $levelId,
            'selectedLevelName' => $levelName,
            'selectedTargetScope' => $targetScope,
            'grades' => $grades,
            'existingTargets' => $existingTargets,
            'scopeError' => $scopeError,
            'editingTarget' => null,
        ]);
    }

    public function edit(EnrolmentTarget $enrolmentTarget)
    {
        return view('enrolment.target.form', [
            'title' => 'Edit Enrolment Target',
            'academicYears' => collect(),
            'branches' => collect(),
            'levels' => collect(),
            'selectedAcademicYearId' => $enrolmentTarget->academic_year_id,
            'selectedBranchId' => $enrolmentTarget->branch_id,
            'selectedLevelId' => $enrolmentTarget->level_id,
            'grades' => $enrolmentTarget->target_scope === 'grade' ? collect([$enrolmentTarget->grade]) : collect(),
            'existingTargets' => collect([$enrolmentTarget->grade_id => $enrolmentTarget->target]),
            'scopeError' => null,
            'editingTarget' => $enrolmentTarget->load(['academicYear', 'branch', 'level']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBatch($request);
        $this->saveTargets($validated);

        return redirect()->route('enrolment.target.index')
            ->with('success', 'Enrolment target berhasil disimpan.');
    }

    public function update(Request $request, EnrolmentTarget $enrolmentTarget)
    {
        $validated = $request->validate([
            'target' => 'required|integer|min:0',
        ]);
        $enrolmentTarget->update(['target' => $validated['target']]);

        return redirect()->route('enrolment.target.index')
            ->with('success', 'Enrolment target berhasil diperbarui.');
    }

    public function destroy(EnrolmentTarget $enrolmentTarget)
    {
        $enrolmentTarget->delete();

        return redirect()->route('enrolment.target.index')
            ->with('success', 'Enrolment target berhasil dihapus.');
    }

    private function validateBatch(Request $request): array
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'level_id' => 'required|integer',
            'target_scope' => 'required|in:level,grade',
            'target' => 'required_if:target_scope,level|nullable|integer|min:0',
            'grade_targets' => 'required_if:target_scope,grade|array|min:1',
            'grade_targets.*' => 'nullable|integer|min:0',
        ]);

        $level = Level::where('branch_id', $validated['branch_id'])
            ->find($validated['level_id']);
        if (!$level) {
            throw ValidationException::withMessages([
                'level_id' => 'Level yang dipilih tidak tersedia pada cabang tersebut.',
            ]);
        }

        if ($validated['target_scope'] === 'grade') {
            $gradeIds = array_map('intval', array_keys($validated['grade_targets']));
            $validGradeIds = $level->grades()->whereIn('id', $gradeIds)->pluck('id')->map(function ($id) {
                return (int) $id;
            })->all();

            if (count($validGradeIds) !== count(array_unique($gradeIds))) {
                throw ValidationException::withMessages([
                    'grade_targets' => 'Grade yang dipilih tidak termasuk dalam level tersebut.',
                ]);
            }

            $hasTargetValue = collect($validated['grade_targets'])->contains(function ($value) {
                return $value !== null && $value !== '';
            });

            if (!$hasTargetValue) {
                throw ValidationException::withMessages([
                    'grade_targets' => 'Masukkan target minimal untuk satu grade.',
                ]);
            }
        }

        return $validated;
    }

    private function saveTargets(array $data): void
    {
        DB::transaction(function () use ($data) {
            Level::whereKey($data['level_id'])->lockForUpdate()->firstOrFail();

            $otherScopeExists = EnrolmentTarget::where('academic_year_id', $data['academic_year_id'])
                ->where('branch_id', $data['branch_id'])
                ->where('level_id', $data['level_id'])
                ->where('target_scope', '!=', $data['target_scope'])
                ->exists();

            if ($otherScopeExists) {
                throw ValidationException::withMessages([
                    'target_scope' => 'Target Level dan target Grade tidak dapat dicampur untuk kombinasi tahun ajaran, cabang, dan level yang sama. Hapus target yang ada terlebih dahulu.',
                ]);
            }

            if ($data['target_scope'] === 'level') {
                $target = EnrolmentTarget::firstOrNew([
                    'academic_year_id' => $data['academic_year_id'],
                    'branch_id' => $data['branch_id'],
                    'level_id' => $data['level_id'],
                    'target_scope' => 'level',
                    'grade_id' => null,
                ]);
                $target->target = $data['target'];
                $target->save();

                return;
            }

            foreach ($data['grade_targets'] as $gradeId => $targetValue) {
                if ($targetValue === null || $targetValue === '') {
                    continue;
                }

                EnrolmentTarget::updateOrCreate(
                    [
                        'academic_year_id' => $data['academic_year_id'],
                        'branch_id' => $data['branch_id'],
                        'level_id' => $data['level_id'],
                        'target_scope' => 'grade',
                        'grade_id' => $gradeId,
                    ],
                    ['target' => $targetValue]
                );
            }
        });
    }
}
