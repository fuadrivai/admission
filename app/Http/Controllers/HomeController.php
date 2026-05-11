<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\Enrolment;
use App\Models\Level;
use App\Models\Observation;
use App\Models\ProspectActivity;
use App\Models\SchoolVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $branches  = Branch::orderBy('name')->get();
        $branchId  = $request->input('branch_id'); // null = All

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

        $enrolBase = Enrolment::when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $paidEnrolment  = (clone $enrolBase)->whereBetween('created_at', [$monthStart, $monthEnd])->where('payment_status', 'paid')->count();
        $enrolmentsMonth = (clone $enrolBase)->whereBetween('created_at', [$monthStart, $monthEnd])->count();

        $admissionBase = Admission::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$monthStart, $monthEnd]);
        $studentDocumentsMonth = (clone $admissionBase)->count();
        $documentComplete = (clone $admissionBase)
            ->where('is_complete', 1)
            ->whereHas('statement', fn($q) => $q->where('is_completed', 1))
            ->has('documents', '>=', 4)
            ->count();

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
        $thisWeekEnrolments = Enrolment::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$thisWeekStart->toDateString(), $thisWeekEnd->toDateString()])
            ->selectRaw('DAYOFWEEK(created_at) as dow, COUNT(*) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        $lastWeekEnrolments = Enrolment::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('payment_status', 'PAID')
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
            return Enrolment::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('level_id', $level->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
        })->values()->all();

        $levelPaidEnrolments = $levels->map(function ($level) use ($branchId, $monthStart, $monthEnd) {
            return Enrolment::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('payment_status', 'PAID')
                ->where('level_id', $level->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
        })->values()->all();

        return [
            'schoolVisitsToday' => $schoolVisitsToday,
            'schoolVisitsMonth' => $schoolVisitsMonth,
            'paidEnrolment' => $paidEnrolment,
            'enrolmentsMonth' => $enrolmentsMonth,
            'documentComplete' => $documentComplete,
            'studentDocumentsMonth' => $studentDocumentsMonth,
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
}
