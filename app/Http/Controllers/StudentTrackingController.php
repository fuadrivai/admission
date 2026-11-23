<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Enrolment;
use App\Models\Observation;
use App\Models\ProspectActivity;
use App\Models\Prospects;
use App\Models\SchoolVisit;
use Illuminate\Http\Request;

class StudentTrackingController extends Controller
{
    public function show(Request $request, $prospectId)
    {
        $user = $request->user();
        $branchRestricted = $user->role === 'user';
        $branchId = (int) ($user->branch_id ?: 0);

        $prospectQuery = Prospects::query()->whereKey($prospectId);

        if ($branchRestricted) {
            $prospectQuery->where(function ($query) use ($branchId) {
                $query->whereHas('schoolVisit', function ($visitQuery) use ($branchId) {
                    $visitQuery->where('branch_id', $branchId);
                })->orWhereHas('enrolment', function ($enrolmentQuery) use ($branchId) {
                    $enrolmentQuery->where('branch_id', $branchId);
                })->orWhereHas('obsservation', function ($observationQuery) use ($branchId) {
                    $observationQuery->where('branch_id', $branchId);
                });
            });
        }

        $prospect = $prospectQuery->firstOrFail();

        $schoolVisits = SchoolVisit::query()
            ->where('prospects_id', $prospect->id)
            ->when($branchRestricted, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->with('branch:id,name')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        $enrolments = Enrolment::query()
            ->where('prospects_id', $prospect->id)
            ->when($branchRestricted, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->with([
                'branch:id,name',
                'level:id,name',
                'grade:id,name',
                'initialPaymentTransaction',
                'transactions.details',
                'admission.applicant.parents',
                'admission.documents',
                'admission.statement',
            ])
            ->orderByDesc('created_at')
            ->get();

        $requiredDocuments = [
            'KTP_FATHER' => 'KTP Ayah',
            'KTP_MOTHER' => 'KTP Ibu',
            'AKTA_KELAHIRAN' => 'Akta Kelahiran',
            'KK' => 'Kartu Keluarga',
        ];

        $enrolmentProgress = $enrolments->map(function ($enrolment) use ($requiredDocuments) {
            $admission = $enrolment->admission;
            $documents = $admission ? $admission->documents : collect();
            $statement = $admission ? $admission->statement : null;
            $uploadedDocumentTypes = $documents->pluck('type')->unique();
            $uploadedRequiredDocumentTypes = $uploadedDocumentTypes
                ->intersect(array_keys($requiredDocuments));
            $missingDocumentTypes = array_diff(
                array_keys($requiredDocuments),
                $uploadedRequiredDocumentTypes->all()
            );
            $documentCount = count($requiredDocuments) - count($missingDocumentTypes);

            return [
                'enrolment' => $enrolment,
                'admission' => $admission,
                'documents' => $documents,
                'document_count' => $documentCount,
                'missing_document_types' => $missingDocumentTypes,
                'data_state' => !$admission
                    ? 'not_started'
                    : ($admission->is_complete ? 'complete' : 'in_progress'),
                'documents_state' => !$admission
                    ? 'not_started'
                    : (empty($missingDocumentTypes) ? 'complete' : 'in_progress'),
                'statement_state' => !$statement
                    ? 'not_started'
                    : ($statement->is_completed ? 'complete' : 'in_progress'),
                'statement' => $statement,
            ];
        });

        $observations = Observation::query()
            ->where('prospects_id', $prospect->id)
            ->when($branchRestricted, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->with('branch:id,name')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        $activitiesQuery = ProspectActivity::query()
            ->where('prospects_id', $prospect->id);

        if ($branchRestricted) {
            $activitiesQuery->whereHasMorph(
                'activityable',
                [SchoolVisit::class, Enrolment::class, Observation::class, Admission::class],
                function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                }
            );
        }

        $activities = $activitiesQuery
            ->with('activityable')
            ->latest()
            ->get();

        $latestVisit = $schoolVisits->first();
        $latestEnrolment = $enrolmentProgress->first();
        $latestObservation = $observations->first();
        $currentAction = $this->currentAction($latestVisit, $latestEnrolment, $latestObservation);

        return view('student-tracking.show', [
            'title' => 'Student Tracking',
            'prospect' => $prospect,
            'schoolVisits' => $schoolVisits,
            'enrolmentProgress' => $enrolmentProgress,
            'observations' => $observations,
            'activities' => $activities,
            'requiredDocuments' => $requiredDocuments,
            'currentAction' => $currentAction,
        ]);
    }

    private function currentAction($latestVisit, ?array $latestEnrolment, $latestObservation): string
    {
        if ($latestEnrolment) {
            $enrolmentStatus = strtoupper((string) $latestEnrolment['enrolment']->status);
            if ($enrolmentStatus === 'CANCELLED') {
                return 'Enrolment dibatalkan, perlu ditinjau';
            }
            if ($enrolmentStatus === 'INACTIVE') {
                return 'Enrolment tidak aktif, periksa pembayaran';
            }

            $admission = $latestEnrolment['admission'];

            if (!$admission || !$admission->is_complete) {
                return 'Lengkapi data diri siswa';
            }

            if ($latestEnrolment['missing_document_types']) {
                return 'Lengkapi berkas siswa (' . $latestEnrolment['document_count'] . '/4)';
            }

            if (!$latestEnrolment['statement'] || !$latestEnrolment['statement']->is_completed) {
                return 'Lengkapi surat pernyataan orang tua';
            }

            if (!$latestObservation) {
                return 'Menunggu pendaftaran Observation';
            }

            $observationStatus = strtolower((string) $latestObservation->status);
            if ($observationStatus === 'registered') {
                return 'Observation terdaftar, menunggu kehadiran';
            }
            if (in_array($observationStatus, ['absent', 'expired'], true)) {
                return 'Observation perlu ditindaklanjuti';
            }

            return 'Tahap utama pendaftaran sudah selesai';
        }

        if ($latestObservation) {
            $observationStatus = strtolower((string) $latestObservation->status);

            if ($observationStatus === 'registered') {
                return 'Observation terdaftar, menunggu kehadiran';
            }
            if (in_array($observationStatus, ['absent', 'expired'], true)) {
                return 'Observation perlu ditindaklanjuti';
            }

            return 'Observation sudah dihadiri; proses Enrolment belum dimulai';
        }

        if ($latestVisit) {
            return strtolower((string) $latestVisit->status) === 'registered'
                ? 'School Visit terdaftar, menunggu kehadiran'
                : 'School Visit tercatat, menunggu Enrolment';
        }

        return 'Prospect baru, belum ada School Visit atau Enrolment';
    }
}
