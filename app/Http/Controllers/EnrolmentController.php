<?php

namespace App\Http\Controllers;

use App\Exports\EnrolmentExport;
use App\Exports\EnrolmentReportExport;
use App\Models\Branch;
use App\Models\Enrolment;
use App\Models\EnrolmentTransaction;
use App\Models\HistoricalEnrolmentReport;
use App\Services\BranchService;
use App\Services\EnrolmentService;
use App\Services\ProspectService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use function App\Helpers\expireXenditInvoice;

class EnrolmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private BranchService $branchService;
    private EnrolmentService $enrolmentService;
    private ProspectService $prospectService;
    public function __construct(BranchService $branchService, EnrolmentService $enrolmentService, ProspectService $prospectService)
    {
        $this->branchService = $branchService;
        $this->enrolmentService = $enrolmentService;
        $this->prospectService = $prospectService;
    }
    public function index(Request $request)
    {
        $query = $this->enrolmentService->search($request);
        $summaryQuery = $this->enrolmentService->search($request, 'active');
        $summary = $this->enrolmentService->summary($summaryQuery);
        $enrolments = $query->paginate(request('perpage')??10)->withQueryString();
        if ($request->ajax()) {
            return view('enrolment._list', compact('enrolments', 'summary'))->render();
        }

        return view('enrolment.index', ["title" => "Enrolment", "enrolments" => $enrolments, "summary" => $summary]);
    }

   public function datatables(UtilitiesRequest $request)
    {

        $enrolment = Enrolment::query()->whereNull('merged_into_enrolment_id');
        if ($request->ajax()) {
            return datatables()->of($enrolment->with(['branch','grade', 'level']))
                ->addColumn('branch_name', function ($row) {
                    return $row->branch ? $row->branch->name : '-';
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level ? $row->level->name : '-';
                })
                ->addColumn('grade_name', function ($row) {
                    return $row->grade ? $row->grade->name : '-';
                })
                ->make(true);
        }

        return view('enrolment.index', ["title" => "Enrolment"]);
    }

    public function report(Request $request)
    {
        $hasSubmittedFilters = $request->hasAny(['branch_id', 'level_id', 'academic_year_id']);
        $branchRules = $hasSubmittedFilters ? 'required|integer|exists:branches,id' : 'nullable|integer|exists:branches,id';
        $academicYears = DB::table('academic_years')
            ->orderByDesc('name')
            ->get(['id', 'name'            ]);
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $validated = $request->validate([
            'level_id' => 'nullable|integer|exists:levels,id',
            'branch_id' => $branchRules,
            'academic_year_id' => [
                $hasSubmittedFilters ? 'required' : 'nullable',
                Rule::in($academicYears->pluck('id')->map(function ($id) {
                    return (string) $id;
                })->all()),
            ],
            'comparison_academic_year_id' => [
                'nullable',
                'integer',
                'exists:academic_years,id',
                'different:academic_year_id',
            ],
            'report_month' => [
                Rule::requiredIf($request->filled('comparison_academic_year_id')),
                'nullable',
                'integer',
                'between:1,12',
            ],
        ]);

        $selectedLevelId = $validated['level_id'] ?? '';
        $selectedAcademicYearId = $validated['academic_year_id'] ?? '';
        $selectedBranchId = auth()->user()->role === 'user'
            ? auth()->user()->branch_id
            : ($validated['branch_id'] ?? '');
        $selectedComparisonAcademicYearId = $validated['comparison_academic_year_id'] ?? '';
        $selectedReportMonth = $validated['report_month'] ?? '';
        $hasReportFilter = $hasSubmittedFilters
            && $request->filled('branch_id')
            && $request->filled('academic_year_id');
        $branches = Branch::query()
            ->when(auth()->user()->role === 'user', function ($query) {
                $query->where('id', auth()->user()->branch_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);
        $levels = DB::table('levels')
            ->leftJoin('divisions', 'divisions.id', '=', 'levels.division_id')
            ->select('levels.id', 'levels.name', 'levels.branch_id', 'divisions.name as division_name')
            ->orderByRaw("
                CASE divisions.name
                    WHEN 'Preschool' THEN 1
                    WHEN 'Primary' THEN 2
                    WHEN 'Secondary' THEN 3
                    WHEN 'Development Class' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('levels.name')
            ->get();
        $rows = collect();
        $historicalRows = collect();
        $historicalTotals = [
            'enrolment_target' => 0,
            'total_form' => 0,
            'active_children' => 0,
            'cancelled_children' => 0,
            'paid' => 0,
            'pending' => 0,
            'expired' => 0,
            'dp_paid' => 0,
            'dp_pending' => 0,
            'dp_expired' => 0,
            'dp_total' => 0,
        ];

        if ($hasReportFilter) {
            $enrolmentJoin = function ($join) use ($selectedBranchId, $selectedAcademicYearId, $selectedReportMonth) {
                $join->on('enrolments.grade_id', '=', 'grades.id')
                    ->whereNull('enrolments.merged_into_enrolment_id');

                $join->where('enrolments.academic_year_id', $selectedAcademicYearId);
                if ($selectedReportMonth !== '') {
                    $join->whereMonth('enrolments.created_at', $selectedReportMonth);
                }

                if ($selectedBranchId) {
                    $join->where('enrolments.branch_id', $selectedBranchId);
                }
            };

            $targetRows = DB::table('enrolment_targets')
                ->where('branch_id', $selectedBranchId)
                ->whereIn('target_scope', ['level', 'grade'])
                ->where('academic_year_id', $selectedAcademicYearId)
                ->when($selectedLevelId, function ($query) use ($selectedLevelId) {
                    $query->where('level_id', $selectedLevelId);
                })
                ->get(['academic_year_id', 'level_id', 'grade_id', 'target_scope', 'target']);

            $statsRows = DB::table('grades')
                ->leftJoin('levels', 'levels.id', '=', 'grades.level_id')
                ->leftJoin('divisions', 'divisions.id', '=', 'levels.division_id')
                ->when($selectedBranchId, function ($query) use ($selectedBranchId) {
                    $query->where('levels.branch_id', $selectedBranchId);
                })
                ->leftJoin('enrolments', $enrolmentJoin)
                ->when($selectedLevelId, function ($query) use ($selectedLevelId) {
                    $query->where('levels.id', $selectedLevelId);
                })
                ->select(
                    'levels.id as level_id',
                    'levels.name as level_name',
                    'grades.id as grade_id',
                    'grades.name as grade_name',
                    'enrolments.academic_year_id as academic_year_id'
                )
                ->selectRaw("
                    MIN(CASE divisions.name
                        WHEN 'Preschool' THEN 1
                        WHEN 'Primary' THEN 2
                        WHEN 'Secondary' THEN 3
                        WHEN 'Development Class' THEN 4
                        ELSE 5
                    END) as division_order
                ")
                ->selectRaw("COUNT(DISTINCT CASE
                    WHEN UPPER(COALESCE(enrolments.status, 'ACTIVE')) = 'ACTIVE'
                    THEN enrolments.id
                END) as active_children")
                ->selectRaw("COUNT(DISTINCT CASE
                    WHEN UPPER(enrolments.status) = 'CANCELLED'
                    THEN enrolments.id
                END) as cancelled_children")
                ->selectRaw("COUNT(DISTINCT CASE WHEN EXISTS (
                    SELECT 1
                    FROM enrolment_transactions status_transaction
                    INNER JOIN enrolment_details status_detail
                        ON status_detail.enrolment_transaction_id = status_transaction.id
                    WHERE status_transaction.enrolment_id = enrolments.id
                        AND UPPER(COALESCE(enrolments.status, 'ACTIVE')) = 'ACTIVE'
                        AND UPPER(status_transaction.payment_status) = 'PAID'
                        AND status_detail.type = 'enrolment'
                ) THEN enrolments.id END) as paid")
                ->selectRaw("COUNT(DISTINCT CASE WHEN EXISTS (
                    SELECT 1
                    FROM enrolment_transactions status_transaction
                    INNER JOIN enrolment_details status_detail
                        ON status_detail.enrolment_transaction_id = status_transaction.id
                    WHERE status_transaction.enrolment_id = enrolments.id
                        AND UPPER(COALESCE(enrolments.status, 'ACTIVE')) = 'ACTIVE'
                        AND UPPER(status_transaction.payment_status) = 'PENDING'
                        AND status_detail.type = 'enrolment'
                ) THEN enrolments.id END) as pending")
                ->selectRaw("COUNT(DISTINCT CASE WHEN EXISTS (
                    SELECT 1
                    FROM enrolment_transactions status_transaction
                    INNER JOIN enrolment_details status_detail
                        ON status_detail.enrolment_transaction_id = status_transaction.id
                    WHERE status_transaction.enrolment_id = enrolments.id
                        AND UPPER(COALESCE(enrolments.status, 'ACTIVE')) <> 'CANCELLED'
                        AND UPPER(status_transaction.payment_status) = 'EXPIRED'
                        AND status_detail.type = 'enrolment'
                ) THEN enrolments.id END) as expired")
                ->selectRaw("
                    COUNT(DISTINCT CASE WHEN EXISTS (
                        SELECT 1
                        FROM enrolment_transactions
                        INNER JOIN enrolment_details
                            ON enrolment_details.enrolment_transaction_id = enrolment_transactions.id
                        WHERE enrolment_transactions.enrolment_id = enrolments.id
                            AND UPPER(COALESCE(enrolments.status, 'ACTIVE')) = 'ACTIVE'
                            AND UPPER(enrolment_transactions.payment_status) = 'PAID'
                            AND enrolment_details.type = 'dp'
                    ) THEN enrolments.id END) as dp_paid
                ")
                ->selectRaw("
                    COUNT(DISTINCT CASE WHEN EXISTS (
                        SELECT 1
                        FROM enrolment_transactions
                        INNER JOIN enrolment_details
                            ON enrolment_details.enrolment_transaction_id = enrolment_transactions.id
                        WHERE enrolment_transactions.enrolment_id = enrolments.id
                            AND UPPER(COALESCE(enrolments.status, 'ACTIVE')) = 'ACTIVE'
                            AND UPPER(enrolment_transactions.payment_status) = 'PENDING'
                            AND enrolment_details.type = 'dp'
                    ) THEN enrolments.id END) as dp_pending
                ")
                ->selectRaw("
                    COUNT(DISTINCT CASE WHEN EXISTS (
                        SELECT 1
                        FROM enrolment_transactions
                        INNER JOIN enrolment_details
                            ON enrolment_details.enrolment_transaction_id = enrolment_transactions.id
                        WHERE enrolment_transactions.enrolment_id = enrolments.id
                            AND UPPER(enrolments.status) <> 'CANCELLED'
                            AND UPPER(enrolment_transactions.payment_status) = 'EXPIRED'
                            AND enrolment_details.type = 'dp'
                    ) THEN enrolments.id END) as dp_expired
                ")
                ->groupBy('levels.id', 'levels.name', 'grades.id', 'grades.name', 'enrolments.academic_year_id')
                ->orderBy('division_order')
                ->orderBy('grades.name')
                ->get();

            $gradeInfo = $statsRows->unique('grade_id')->keyBy('grade_id');
            $years = collect([(int) $selectedAcademicYearId]);
            $outputRows = [];
            $metricFields = [
                'active_children',
                'cancelled_children',
                'paid',
                'pending',
                'expired',
                'dp_expired',
                'dp_paid',
                'dp_pending',
            ];

            $appendRow = function ($key, array $row) use (&$outputRows, $metricFields) {
                if (!isset($outputRows[$key])) {
                    $outputRows[$key] = (object) $row;

                    return;
                }

                foreach (array_merge(['enrolment_target'], $metricFields) as $field) {
                    $outputRows[$key]->{$field} += $row[$field];
                }
            };

            foreach ($years as $yearId) {
                $yearStats = $statsRows->filter(function ($row) use ($yearId) {
                    return (string) $row->academic_year_id === (string) $yearId;
                });
                $yearTargets = $targetRows->where('academic_year_id', $yearId);
                $levelTargets = $yearTargets->where('target_scope', 'level')->keyBy('level_id');
                $gradeTargets = $yearTargets->where('target_scope', 'grade')->keyBy('grade_id');
                $coveredGrades = [];

                foreach ($levelTargets as $levelId => $target) {
                    $levelGrades = $gradeInfo->where('level_id', $levelId);
                    $levelGradeIds = $levelGrades->pluck('grade_id')->all();
                    $coveredGrades = array_merge($coveredGrades, $levelGradeIds);
                    $levelStats = $yearStats->whereIn('grade_id', $levelGradeIds);
                    $firstGrade = $levelGrades->first();

                    if (!$firstGrade) {
                        continue;
                    }

                    $row = [
                        'level' => $firstGrade->level_name,
                        'division_order' => $firstGrade->division_order,
                        'enrolment_target' => (int) $target->target,
                    ];
                    foreach ($metricFields as $field) {
                        $row[$field] = (int) $levelStats->sum($field);
                    }
                    $appendRow('level:' . $levelId, $row);
                }

                foreach ($gradeTargets as $gradeId => $target) {
                    $coveredGrades[] = $gradeId;
                    $grade = $gradeInfo->get($gradeId);
                    if (!$grade) {
                        continue;
                    }

                    $gradeStats = $yearStats->where('grade_id', $gradeId);
                    $row = [
                        'level' => $grade->grade_name,
                        'division_order' => $grade->division_order,
                        'enrolment_target' => (int) $target->target,
                    ];
                    foreach ($metricFields as $field) {
                        $row[$field] = (int) $gradeStats->sum($field);
                    }
                    $appendRow('grade:' . $gradeId, $row);
                }

                foreach ($yearStats as $stat) {
                    if (in_array($stat->grade_id, $coveredGrades)) {
                        continue;
                    }

                    $row = [
                        'level' => $stat->grade_name,
                        'division_order' => $stat->division_order,
                        'enrolment_target' => 0,
                    ];
                    foreach ($metricFields as $field) {
                        $row[$field] = (int) $stat->{$field};
                    }
                    $appendRow('grade:' . $stat->grade_id, $row);
                }
            }

            foreach ($gradeInfo as $gradeId => $grade) {
                if (isset($outputRows['level:' . $grade->level_id]) || isset($outputRows['grade:' . $gradeId])) {
                    continue;
                }

                $row = [
                    'level' => $grade->grade_name,
                    'division_order' => $grade->division_order,
                    'enrolment_target' => 0,
                ];
                foreach ($metricFields as $field) {
                    $row[$field] = 0;
                }
                $appendRow('grade:' . $gradeId, $row);
            }

            $rows = collect($outputRows)->each(function ($row) {
                $row->total_form = $row->paid + $row->pending;
                $row->dp_total = $row->dp_paid + $row->dp_pending;
            })->sortBy([
                ['division_order', 'asc'],
                ['level', 'asc'],
            ])->values();

            if ($selectedComparisonAcademicYearId !== '') {
                $historicalRows = HistoricalEnrolmentReport::query()
                    ->join('levels', 'levels.id', '=', 'historical_enrolment_reports.level_id')
                    ->leftJoin('divisions', 'divisions.id', '=', 'levels.division_id')
                    ->leftJoin('grades', 'grades.id', '=', 'historical_enrolment_reports.grade_id')
                    ->where('historical_enrolment_reports.academic_year_id', $selectedComparisonAcademicYearId)
                    ->where('historical_enrolment_reports.snapshot_month', $selectedReportMonth)
                    ->where('historical_enrolment_reports.branch_id', $selectedBranchId)
                    ->when($selectedLevelId, function ($query) use ($selectedLevelId) {
                        $query->where('historical_enrolment_reports.level_id', $selectedLevelId);
                    })
                    ->select(
                        'historical_enrolment_reports.*',
                        'levels.name as level_name',
                        'grades.name as grade_name'
                    )
                    ->selectRaw("CASE divisions.name
                        WHEN 'Preschool' THEN 1
                        WHEN 'Primary' THEN 2
                        WHEN 'Secondary' THEN 3
                        WHEN 'Development Class' THEN 4
                        ELSE 5
                    END as division_order")
                    ->get()
                    ->map(function ($snapshot) {
                        $row = (object) [
                            'level' => $snapshot->scope_type === 'level'
                                ? $snapshot->level_name
                                : ($snapshot->grade_name ?: $snapshot->level_name),
                            'division_order' => (int) $snapshot->division_order,
                            'active_children' => (int) $snapshot->active_children,
                            'cancelled_children' => (int) $snapshot->cancelled_children,
                            'expired' => (int) $snapshot->enrolment_expired,
                            'pending' => (int) $snapshot->enrolment_pending,
                            'paid' => (int) $snapshot->enrolment_paid,
                            'dp_expired' => (int) $snapshot->dp_expired,
                            'dp_pending' => (int) $snapshot->dp_pending,
                            'dp_paid' => (int) $snapshot->dp_paid,
                            'enrolment_target' => (int) $snapshot->target,
                        ];
                        $row->total_form = $row->paid + $row->pending;
                        $row->dp_total = $row->dp_paid + $row->dp_pending;

                        return $row;
                    })
                    ->sortBy([
                        ['division_order', 'asc'],
                        ['level', 'asc'],
                    ])
                    ->values();

                foreach ($historicalTotals as $field => $value) {
                    $historicalTotals[$field] = (int) $historicalRows->sum($field);
                }
            }
        }

        $totals = [
            'enrolment_target' => $rows->sum('enrolment_target'),
            'total_form' => $rows->sum('total_form'),
            'active_children' => $rows->sum('active_children'),
            'cancelled_children' => $rows->sum('cancelled_children'),
            'paid' => $rows->sum('paid'),
            'pending' => $rows->sum('pending'),
            'expired' => $rows->sum('expired'),
            'dp_paid' => $rows->sum('dp_paid'),
            'dp_pending' => $rows->sum('dp_pending'),
            'dp_expired' => $rows->sum('dp_expired'),
            'dp_total' => $rows->sum('dp_total'),
        ];

        if ($request->input('export') === 'excel') {
            abort_unless($hasReportFilter, 422, 'Pilih cabang dan tahun ajaran sebelum mengekspor laporan.');

            return Excel::download(
                new EnrolmentReportExport(
                    $rows,
                    $totals,
                    $historicalRows,
                    $historicalTotals,
                    $selectedComparisonAcademicYearId !== ''
                        ? optional($academicYears->firstWhere('id', (int) $selectedComparisonAcademicYearId))->name
                            . ' - ' . ($months[$selectedReportMonth] ?? '')
                        : null
                ),
                'Enrolment_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx'
            );
        }

        return view('enrolment.report', [
            'title' => 'Laporan Enrolment',
            'rows' => $rows,
            'totals' => $totals,
            'historicalRows' => $historicalRows,
            'historicalTotals' => $historicalTotals,
            'levels' => $levels,
            'academicYears' => $academicYears,
            'months' => $months,
            'branches' => $branches,
            'selectedLevelId' => $selectedLevelId,
            'selectedAcademicYearId' => $selectedAcademicYearId,
            'selectedBranchId' => $selectedBranchId,
            'selectedComparisonAcademicYearId' => $selectedComparisonAcademicYearId,
            'selectedReportMonth' => $selectedReportMonth,
            'hasReportFilter' => $hasReportFilter,
        ]);
    }

    public function setting()
    {
        $branches = $this->branchService->getShowInForm();

        return view('enrolment.setting', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function showByCode($code)
    {
        try {
            $enrolment = $this->enrolmentService->showByCode($code,['branch', 'grade', 'level','prospect']);
            return response()->json($enrolment);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enrollment code not found'
            ], $e->getCode() ?: 404);
        }
    }

    public function show(Enrolment $enrolment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function edit(Enrolment $enrolment)
    {
        $enrolment = $this->enrolmentService->show($enrolment->id)->load([
            'transactions.details',
            'initialPaymentTransaction',
        ]);
        return view('enrolment.detail', ["title" => "Enrolment Detail", "enrolment" => $enrolment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Enrolment $enrolment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrolment $enrolment)
    {
        if ($enrolment->isMerged()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merged enrolments are archived and cannot be deleted.',
            ], 422);
        }

        $expireResponse = $this->expirePendingInvoice($enrolment);
        if ($expireResponse !== null) {
            return $expireResponse;
        }

        $enrolment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Enrolment deleted successfully.',
        ]);
    }
    

    public function form()
    {
        $branches = $this->branchService->getShowInForm();
        return view('enrolment.form.external', compact('branches'));
    }

    public function post(Request $request)
    {
        
        $rules = [
            'alreadyVisit'          => 'required',
            'code'                  => 'nullable|string',
            'prospectsId'           => 'nullable|string',
            'isCurrentStudent'      =>'required',
            'studentBranch'         => 'nullable|string',
            'mhisPortalUsername'    => 'nullable|string',
            'branch'                => 'required',
            'level'                 => 'required',
            'grade'                 => 'required',
            'academicYearId'        => 'required|integer',
            'academicYear'          => 'required|string',
            'parentName'            => 'required|string|max:255',
            'email'                 => 'required',
            'phone'                 => 'required|string|max:20',
            'relationship'          => 'required|string',
            'zipCode'               => 'nullable|string|max:20',
            'address'               => 'required|string',
            'childName'             => 'required|string|max:255',
            'childNickname'         => 'required|string|max:255',
            'placeOfBirth'          => 'required|string|max:120',
            'dateOfBirth'           => 'required',
            'currentSchool'         => 'nullable|string|max:255',
            'childSosmed'           => 'nullable|string|max:255',
            'opendayVisited'        => 'required',
            'knowledgeAboutProgram' => 'required|in:yes,no,maybe',
            'infoFrom'              => 'required|string',
            'infoFromMessage'       => 'nullable|string',
            'reasonForEnrolment'    => 'required|string',
            'prefferedProgram'      => 'required|string',
            'expectationMhisImpact' => 'required|string',
            'recommenderName'       => 'nullable|string|max:255',
            'recommenderPhone'      => 'nullable|string|max:20',
            'recommenderChildName'  => 'nullable|string|max:255',
            'recommenderChildClass' => 'nullable|string|max:120',
        ];

        if ($request->alreadyVisit === "true") {
            $rules['code'] = 'required|string';
        }

        $validated = $request->validate($rules);
        $enrolment = $this->enrolmentService->post((object)$validated);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Enrolment form submitted successfully.',
            'data'      => $enrolment,
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->enrolmentService->search($request);
        $timestamps = Carbon::now()->format('Ymd_His');
        return Excel::download(
            new EnrolmentExport($query->get()),
            'Enrolment_Report_' . $timestamps . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function history($id)
    {
        $prospect = $this->prospectService->show($id);
        return view('schoolvisit._history', compact('prospect'))->render();
    }

    public function cancel(Request $request, Enrolment $enrolment)
    {
        if ($enrolment->isMerged()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merged enrolments are archived and cannot be cancelled.',
            ], 422);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if (strtoupper((string) $enrolment->status) === 'CANCELLED') {
            return response()->json(['status' => 'error', 'message' => 'Enrolment is already cancelled.'], 422);
        }

        $enrolment->status = 'Cancelled';
        $enrolment->cancel_reason = $validated['reason'];
        $enrolment->cancelled_at = now();
        $enrolment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Enrolment cancelled successfully.',
        ]);
    }

    private function expirePendingInvoice(Enrolment $enrolment): ?\Illuminate\Http\JsonResponse
    {
        $branchName = $enrolment->branch->name ?? 'bintaro';

        $transactions = EnrolmentTransaction::where('code', $enrolment->code)
            ->whereRaw('UPPER(payment_status) = ?', ['PENDING'])
            ->get();

        if ($transactions->isEmpty()) {
            return null;
        }
        $paymentUrls = $transactions->pluck('payment_url')->filter()->unique();

        foreach ($paymentUrls as $paymentUrl) {
            $result = expireXenditInvoice((string) $paymentUrl, $branchName);

            if (!($result['success'] ?? false)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Xendit invoice could not be expired. The enrolment was not changed.',
                    'details' => $result['message'] ?? 'Unknown Xendit error.',
                ], 502);
            }
        }

        return null;
    }

    public function updateSourceData(Request $request, Enrolment $enrolment)
    {
        $validated = $request->validate([
            'source_data' => 'required|in:internal,external',
        ]);

        $enrolment->source_data = $validated['source_data'];
        $enrolment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Source data updated successfully.',
            'data' => [
                'id' => $enrolment->id,
                'source_data' => $enrolment->source_data,
            ],
        ]);
    }

}
