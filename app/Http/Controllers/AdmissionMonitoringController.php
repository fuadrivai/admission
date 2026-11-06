<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\Enrolment;
use App\Models\EnrolmentTransaction;
use App\Models\SchoolVisit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdmissionMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where('show_in_form', true),
            ],
        ]);

        $user = $request->user();
        $branchRestricted = $user->role === 'user';
        $canViewDocuments = !$branchRestricted;

        $branches = Branch::query()
            ->where('show_in_form', true)
            ->when($branchRestricted, function ($query) use ($user) {
                $query->whereKey($user->branch_id ?: 0);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $visibleBranchIds = $branches->pluck('id')->map(function ($id) {
            return (int) $id;
        })->all();
        $requestedBranchId = $branchRestricted
            ? (int) ($user->branch_id ?: 0)
            : (int) ($validated['branch_id'] ?? 0);
        $branchId = $requestedBranchId && in_array($requestedBranchId, $visibleBranchIds, true)
            ? $requestedBranchId
            : null;
        $scopeBranchIds = $branchId
            ? [$branchId]
            : ($branchRestricted ? [] : $visibleBranchIds);
        $branchUnavailable = $branchRestricted && !$branchId;
        $branchName = $branchId
            ? optional($branches->firstWhere('id', $branchId))->name
            : ($branchUnavailable ? 'Cabang tidak tersedia' : 'Semua cabang');

        $upcomingVisits = $this->upcomingVisitsQuery($scopeBranchIds);
        $schoolVisits = (clone $upcomingVisits)
            ->with('branch:id,name')
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get(['id', 'code', 'child_name', 'branch_id', 'date', 'time']);

        $activeEnrolments = Enrolment::query()
            ->whereNull('merged_into_enrolment_id')
            ->whereRaw("UPPER(COALESCE(status, 'ACTIVE')) = ?", ['ACTIVE'])
            ->whereIn('branch_id', $scopeBranchIds);

        $hasPaidDp = function ($query) {
            $query->whereHas('details', function ($detailsQuery) {
                $detailsQuery->where('type', 'dp');
            })->whereRaw('UPPER(payment_status) IN (?, ?, ?)', ['PAID', 'SETTLED', 'COMPLETED']);
        };
        $dpPaid = (clone $activeEnrolments)->whereHas('transactions', $hasPaidDp);
        $dpUnpaid = (clone $activeEnrolments)->whereDoesntHave('transactions', $hasPaidDp);

        $paymentTypes = $canViewDocuments ? ['enrolment', 'dp'] : ['enrolment'];
        $pendingPayments = EnrolmentTransaction::query()
            ->whereRaw('UPPER(payment_status) IN (?, ?)', ['PENDING', 'UNPAID'])
            ->whereHas('details', function ($query) use ($paymentTypes) {
                $query->whereIn('type', $paymentTypes);
            })
            ->whereHas('enrolment', function ($query) use ($scopeBranchIds) {
                $query->whereNull('merged_into_enrolment_id')
                    ->whereRaw("UPPER(COALESCE(status, 'ACTIVE')) = ?", ['ACTIVE'])
                    ->whereIn('branch_id', $scopeBranchIds);
            })
            ->with([
                'enrolment:id,code,child_name',
                'details:id,enrolment_transaction_id,type',
            ])
            ->orderByDesc('created_at');

        $incompleteAdmissionsCount = null;
        $admissionsNeedingAttention = collect();

        if ($canViewDocuments) {
            $incompleteAdmissions = Admission::query()
                ->where(function ($query) {
                    $query->where('is_complete', false)
                        ->orWhereDoesntHave('statement', function ($statementQuery) {
                            $statementQuery->where('is_completed', true);
                        })
                        ->orWhereRaw(
                            '(SELECT COUNT(*) FROM admission_documents WHERE admission_documents.admission_id = admissions.id) <> ?',
                            [4]
                        );
                })
                ->whereIn('branch_id', $scopeBranchIds);

            $incompleteAdmissionsCount = (clone $incompleteAdmissions)->count();
            $admissionsNeedingAttention = (clone $incompleteAdmissions)
                ->with([
                    'enrolment:id,code,child_name',
                    'statement:id,admission_id,is_completed',
                ])
                ->withCount('documents')
                ->latest()
                ->take(5)
                ->get(['id', 'code', 'branch_id', 'enrolment_id', 'is_complete', 'created_at']);
        }

        return view('admission-monitoring.index', [
            'title' => 'Admission Monitoring',
            'branches' => $branches,
            'selectedBranch' => $branchId,
            'branchName' => $branchName ?: 'Cabang belum ditetapkan',
            'branchRestricted' => $branchRestricted,
            'branchUnavailable' => $branchUnavailable,
            'canViewDocuments' => $canViewDocuments,
            'schoolVisitsCount' => (clone $upcomingVisits)->count(),
            'schoolVisits' => $schoolVisits,
            'activeEnrolmentsCount' => $activeEnrolments->count(),
            'dpPaidCount' => (clone $dpPaid)->count(),
            'dpUnpaidCount' => (clone $dpUnpaid)->count(),
            'dpPaidEnrolments' => (clone $dpPaid)->latest()->take(5)
                ->get(['id', 'code', 'child_name', 'branch_id']),
            'dpUnpaidEnrolments' => (clone $dpUnpaid)->latest()->take(5)
                ->get(['id', 'code', 'child_name', 'branch_id']),
            'pendingPaymentsCount' => (clone $pendingPayments)->count(),
            'paymentLabel' => $canViewDocuments ? 'Pembayaran menunggu' : 'Pembayaran Enrolment menunggu',
            'pendingPayments' => $pendingPayments->take(5)->get([
                'id', 'enrolment_id', 'code', 'payment_status', 'expiry_va_date', 'created_at',
            ]),
            'incompleteAdmissionsCount' => $incompleteAdmissionsCount,
            'admissionsNeedingAttention' => $admissionsNeedingAttention,
        ]);
    }

    private function upcomingVisitsQuery(array $branchIds)
    {
        return SchoolVisit::query()
            ->where('status', 'registered')
            ->whereDate('date', '>=', today())
            ->whereIn('branch_id', $branchIds);
    }
}
