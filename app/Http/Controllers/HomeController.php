<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Enrolment;
use App\Models\Level;
use App\Models\Observation;
use App\Models\ProspectActivity;
use App\Models\Prospects;
use App\Models\SchoolVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
        ]);
        $branches  = Branch::orderBy('name')->get();
        $branchId  = $request->input('branch_id') ?: null; // null = All

        $studentSearch = trim($validated['search'] ?? '');
        $studentSearchResults = collect();
        $studentSearchCount = 0;
        $branchRestricted = $request->user()->role === 'user' || (bool) $branchId;
        $searchBranchIds = $request->user()->role === 'user'
            ? [(int) ($request->user()->branch_id ?: 0)]
            : ($branchId ? [(int) $branchId] : []);

        if ($studentSearch !== '' && (!$branchRestricted || $searchBranchIds)) {
            $studentSearchQuery = Prospects::query()
                ->where(function ($query) use ($studentSearch) {
                    $like = '%' . $studentSearch . '%';
                    $query->where('code', 'like', $like)
                        ->orWhere('child_name', 'like', $like)
                        ->orWhere('parent_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone_number', 'like', $like);
                });

            if ($branchRestricted) {
                $studentSearchQuery->where(function ($query) use ($searchBranchIds) {
                    $query->whereHas('schoolVisit', function ($visitQuery) use ($searchBranchIds) {
                        $visitQuery->whereIn('branch_id', $searchBranchIds);
                    })->orWhereHas('enrolment', function ($enrolmentQuery) use ($searchBranchIds) {
                        $enrolmentQuery->whereIn('branch_id', $searchBranchIds);
                    })->orWhereHas('obsservation', function ($observationQuery) use ($searchBranchIds) {
                        $observationQuery->whereIn('branch_id', $searchBranchIds);
                    });
                });
            }

            $studentSearchCount = (clone $studentSearchQuery)->count();
            $studentSearchResults = $studentSearchQuery
                ->with([
                    'schoolVisit' => function ($query) use ($searchBranchIds) {
                        $query->when($searchBranchIds, function ($query) use ($searchBranchIds) {
                            $query->whereIn('branch_id', $searchBranchIds);
                        })->orderByDesc('created_at')
                            ->select('id', 'prospects_id', 'status', 'branch_id', 'academic_year_id');
                    },
                    'enrolment' => function ($query) use ($searchBranchIds) {
                        $query->when($searchBranchIds, function ($query) use ($searchBranchIds) {
                            $query->whereIn('branch_id', $searchBranchIds);
                        })->orderByDesc('created_at')
                            ->select('id', 'prospects_id', 'status', 'branch_id', 'academic_year_id');
                    },
                    'obsservation' => function ($query) use ($searchBranchIds) {
                        $query->when($searchBranchIds, function ($query) use ($searchBranchIds) {
                            $query->whereIn('branch_id', $searchBranchIds);
                        })->orderByDesc('created_at')
                            ->select('id', 'prospects_id', 'status', 'branch_id');
                    },
                ])
                ->orderByDesc('created_at')
                ->take(8)
                ->get([
                    'id', 'code', 'child_name', 'parent_name', 'email',
                    'phone_number', 'source_module', 'created_at',
                ]);
        }

        $metrics = $this->dashboardMetrics($branchId);

        $recentActivities = ProspectActivity::with('prospect')
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHasMorph('activityable', [
                    \App\Models\SchoolVisit::class,
                    \App\Models\Enrolment::class,
                    \App\Models\Observation::class,
                ], fn($m) => $m->where('branch_id', $branchId));
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', array_merge([
            'title'    => 'Dashboard',
            'branches' => $branches,
            'selectedBranch' => $branchId,
            'studentSearch' => $studentSearch,
            'studentSearchResults' => $studentSearchResults,
            'studentSearchCount' => $studentSearchCount,
            'recentActivities' => $recentActivities,
        ], $metrics));
    }

    private function dashboardMetrics(?int $branchId): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $visitBase = SchoolVisit::when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $schoolVisitsToday = (clone $visitBase)->whereDate('date', $today)->count();
        $schoolVisitsMonth = (clone $visitBase)->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count();

        $enrolBase = Enrolment::whereNull('merged_into_enrolment_id')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $paidEnrolment = $this->paidMasterEnrolments($branchId, 'enrolment')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();
        $enrolmentsMonth = (clone $enrolBase)->whereBetween('created_at', [$monthStart, $monthEnd])->count();

        $paidDpThisMonth = $this->paidMasterEnrolments($branchId, 'dp')
            ->whereHas('transactions', function ($query) use ($monthStart, $monthEnd) {
                $query->whereRaw('UPPER(payment_status) = ?', ['PAID'])
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->whereHas('details', fn($details) => $details->where('type', 'dp'));
            })
            ->count();

        $comparisonMonthStart = Carbon::now()->startOfMonth();
        $comparisonMonths = collect([
            $comparisonMonthStart->copy()->subMonth(),
            $comparisonMonthStart->copy()->subMonths(2),
        ]);
        $paymentComparisons = [];

        foreach (['dp', 'enrolment'] as $paymentType) {
            $months = $comparisonMonths->map(function (Carbon $month) use ($branchId, $paymentType) {
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                $count = $this->paidMasterEnrolments($branchId, $paymentType)
                    ->whereHas('transactions', function ($query) use ($monthStart, $monthEnd, $paymentType) {
                        $query->whereRaw('UPPER(payment_status) = ?', ['PAID'])
                            ->whereBetween('payment_date', [$monthStart, $monthEnd])
                            ->whereHas('details', fn($details) => $details->where('type', $paymentType));
                    })
                    ->count();

                return [
                    'label' => $month->format('F Y'),
                    'count' => $count,
                ];
            })->values();

            $currentCount = $months[0]['count'];
            $previousCount = $months[1]['count'];
            $paymentComparisons[$paymentType] = [
                'months' => $months,
                'change_percentage' => $previousCount > 0
                    ? (($currentCount - $previousCount) / $previousCount) * 100
                    : null,
                'direction' => $currentCount <=> $previousCount,
                'previous_month' => $months[1]['label'],
                'previous_count' => $previousCount,
            ];
        }

        $obsBase = Observation::when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $observationsToday = (clone $obsBase)->whereDate('date', $today)->count();
        $observationsMonth = (clone $obsBase)->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count();

        // Weekly trend: this week vs last week
        $thisWeekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $thisWeekEnd   = $thisWeekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $lastWeekStart = $thisWeekStart->copy()->subWeek();
        $lastWeekEnd   = $lastWeekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $thisWeekVisits = SchoolVisit::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('date', [$thisWeekStart->toDateString(), $thisWeekEnd->toDateString()])
            ->selectRaw('DAYOFWEEK(date) as dow, COUNT(*) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        $lastWeekVisits = SchoolVisit::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('date', [$lastWeekStart->toDateString(), $lastWeekEnd->toDateString()])
            ->selectRaw('DAYOFWEEK(date) as dow, COUNT(*) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        // DAYOFWEEK: 1=Sun,2=Mon,...,7=Sat — map to Mon(2)–Sun(1) order
        $dowOrder = [2, 3, 4, 5, 6, 7, 1];
        $thisWeekData = collect($dowOrder)->map(fn($d) => $thisWeekVisits[$d] ?? 0)->values()->all();
        $lastWeekData = collect($dowOrder)->map(fn($d) => $lastWeekVisits[$d] ?? 0)->values()->all();

        // Enrolment weekly trend (grouped by DAYOFWEEK on created_at, paid only)
        $thisWeekEnrolments = $this->paidMasterEnrolments($branchId, 'enrolment')
            ->whereBetween('created_at', [$thisWeekStart->toDateString(), $thisWeekEnd->toDateString()])
            ->selectRaw('DAYOFWEEK(created_at) as dow, COUNT(*) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        $lastWeekEnrolments = $this->paidMasterEnrolments($branchId, 'enrolment')
            ->whereBetween('created_at', [$lastWeekStart->toDateString(), $lastWeekEnd->toDateString()])
            ->selectRaw('DAYOFWEEK(created_at) as dow, COUNT(*) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        $thisWeekEnrolData = collect($dowOrder)->map(fn($d) => $thisWeekEnrolments[$d] ?? 0)->values()->all();
        $lastWeekEnrolData = collect($dowOrder)->map(fn($d) => $lastWeekEnrolments[$d] ?? 0)->values()->all();

        $levels = Level::with('division:id,name')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('division_id', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'principal', 'email', 'division_id']);

        $levelLabels = $levels->pluck('name')->values()->all();
        $levelVisits = $levels->map(function ($level) use ($branchId, $monthStart, $monthEnd) {
            return SchoolVisit::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('level_id', $level->id)
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->count();
        })->values()->all();

        $levelEnrolments = $levels->map(function ($level) use ($branchId, $monthStart, $monthEnd) {
            return Enrolment::whereNull('merged_into_enrolment_id')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('level_id', $level->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
        })->values()->all();

        $levelPaidEnrolments = $levels->map(function ($level) use ($branchId, $monthStart, $monthEnd) {
            return $this->paidMasterEnrolments($branchId, 'enrolment')
                ->where('level_id', $level->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
        })->values()->all();

        return [
            'schoolVisitsToday' => $schoolVisitsToday,
            'schoolVisitsMonth' => $schoolVisitsMonth,
            'paidEnrolment' => $paidEnrolment,
            'enrolmentsMonth' => $enrolmentsMonth,
            'paidDpThisMonth' => $paidDpThisMonth,
            'dpPaymentComparison' => $paymentComparisons['dp'],
            'enrolmentPaymentComparison' => $paymentComparisons['enrolment'],
            'observationsToday' => $observationsToday,
            'observationsMonth' => $observationsMonth,
            'thisWeekVisits' => $thisWeekData,
            'lastWeekVisits' => $lastWeekData,
            'thisWeekEnrolments' => $thisWeekEnrolData,
            'lastWeekEnrolments' => $lastWeekEnrolData,
            'levels' => $levels,
            'levelLabels' => $levelLabels,
            'levelVisits' => $levelVisits,
            'levelEnrolments' => $levelEnrolments,
            'levelPaidEnrolments' => $levelPaidEnrolments,
        ];
    }

    private function paidMasterEnrolments(?int $branchId, string $detailType)
    {
        return Enrolment::query()
            ->whereNull('merged_into_enrolment_id')
            ->whereRaw("UPPER(COALESCE(status, 'ACTIVE')) = ?", ['ACTIVE'])
            ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
            ->whereHas('transactions', function ($query) use ($detailType) {
                $query->whereRaw('UPPER(payment_status) = ?', ['PAID'])
                    ->whereHas('details', fn($details) => $details->where('type', $detailType));
            });
    }
}
