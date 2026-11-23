@extends('main-layout.index')

@section('content-style')
    <style>
        .tracking-page {
            --tracking-ink: #172b4d;
            --tracking-muted: #738096;
            --tracking-line: #e8edf3;
            color: var(--tracking-ink);
        }

        .tracking-hero,
        .tracking-panel {
            border: 1px solid var(--tracking-line);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 20px rgba(27, 47, 76, .045);
        }

        .tracking-hero {
            padding: 1.35rem 1.5rem;
            background: linear-gradient(120deg, #f3faf7 0%, #fff 68%);
        }

        .tracking-avatar {
            display: grid;
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
            place-items: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #167c65, #0f766e);
            color: #fff;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .tracking-title {
            margin: 0;
            color: var(--tracking-ink);
            font-size: clamp(1.25rem, 2.4vw, 1.7rem);
            font-weight: 800;
        }

        .tracking-subtitle {
            margin: .25rem 0 0;
            color: var(--tracking-muted);
            font-size: .88rem;
        }

        .tracking-current-action {
            min-width: 220px;
            padding: .8rem 1rem;
            border: 1px solid #d5e9df;
            border-radius: 12px;
            background: #f5fbf7;
        }

        .tracking-current-action small {
            display: block;
            margin-bottom: .2rem;
            color: #648072;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .tracking-current-action strong {
            color: #17684f;
            font-size: .9rem;
        }

        .tracking-contact {
            display: flex;
            flex-wrap: wrap;
            gap: .55rem 1.1rem;
            margin-top: 1rem;
            color: #536178;
            font-size: .82rem;
        }

        .tracking-contact i {
            margin-right: .25rem;
            color: #198266;
        }

        .tracking-journey {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: start;
            gap: 1rem;
            margin-top: 1rem;
        }

        .tracking-panel {
            min-width: 0;
            overflow: hidden;
        }

        .tracking-panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid #edf1f5;
        }

        .tracking-panel-header h2 {
            margin: 0;
            color: var(--tracking-ink);
            font-size: 1rem;
            font-weight: 800;
        }

        .tracking-panel-header p {
            margin: .2rem 0 0;
            color: var(--tracking-muted);
            font-size: .78rem;
        }

        .tracking-panel-body {
            padding: 1rem 1.1rem;
        }

        .tracking-record + .tracking-record {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #edf1f5;
        }

        .tracking-record-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .6rem;
            margin-bottom: .55rem;
        }

        .tracking-record-title strong {
            font-size: .88rem;
        }

        .tracking-record-meta {
            color: var(--tracking-muted);
            font-size: .78rem;
            line-height: 1.55;
        }

        .tracking-checklist {
            display: grid;
            gap: .5rem;
            margin-top: .75rem;
        }

        .tracking-check-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .7rem;
            padding: .55rem .65rem;
            border: 1px solid #edf1f5;
            border-radius: 9px;
            background: #fbfcfe;
            font-size: .8rem;
        }

        .tracking-document-list {
            display: grid;
            gap: .35rem;
            margin: .35rem 0 0;
            padding: 0;
            list-style: none;
        }

        .tracking-document-list li {
            display: flex;
            align-items: center;
            gap: .45rem;
            color: #536178;
            font-size: .76rem;
        }

        .tracking-transaction {
            display: flex;
            justify-content: space-between;
            gap: .6rem;
            padding: .45rem 0;
            border-bottom: 1px dashed #e8edf3;
            font-size: .76rem;
        }

        .tracking-transaction:last-child {
            border-bottom: 0;
        }

        .tracking-history {
            margin: 0;
            padding: 0 0 0 1.45rem;
            list-style: none;
        }

        .tracking-history-item {
            position: relative;
            padding: .1rem 0 1rem 1rem;
            border-left: 2px solid #d7eee4;
        }

        .tracking-history-item:last-child {
            padding-bottom: 0;
            border-left-color: transparent;
        }

        .tracking-history-item::before {
            position: absolute;
            top: .18rem;
            left: -.42rem;
            width: .72rem;
            height: .72rem;
            border: 2px solid #fff;
            border-radius: 50%;
            background: #16936f;
            box-shadow: 0 0 0 1px #bde5d3;
            content: '';
        }

        .tracking-history-date {
            color: #148166;
            font-size: .75rem;
            font-weight: 750;
        }

        .tracking-history-note {
            margin: .2rem 0 0;
            color: #344054;
            font-size: .85rem;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .tracking-empty {
            padding: .9rem;
            border: 1px dashed #d7dee8;
            border-radius: 10px;
            color: var(--tracking-muted);
            font-size: .82rem;
            text-align: center;
        }

        @media (max-width: 991.98px) {
            .tracking-journey {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .tracking-hero {
                padding: 1rem;
            }

            .tracking-hero-main {
                align-items: flex-start !important;
            }

            .tracking-current-action {
                min-width: 0;
                width: 100%;
            }

            .tracking-panel-header,
            .tracking-panel-body {
                padding-right: .85rem;
                padding-left: .85rem;
            }
        }
    </style>
@endsection

@section('content-child')
    @php
        $stateLabels = [
            'complete' => 'Selesai',
            'in_progress' => 'Berjalan',
            'attention' => 'Perlu tindak lanjut',
            'not_started' => 'Belum dimulai',
        ];
        $stateClasses = [
            'complete' => 'text-bg-success',
            'in_progress' => 'text-bg-warning',
            'attention' => 'text-bg-danger',
            'not_started' => 'text-bg-light border text-muted',
        ];
        $initials = collect(explode(' ', trim($prospect->child_name ?? '')))
            ->filter()
            ->take(2)
            ->map(function ($word) {
                return strtoupper(substr($word, 0, 1));
            })
            ->implode('');
        $latestVisit = $schoolVisits->first();
        $latestEnrolment = $enrolmentProgress->first();
        $latestObservation = $observations->first();
    @endphp

    <main class="section tracking-page">
        <div class="mb-3">
            <a href="{{ route('home', ['search' => $prospect->code]) }}" class="btn btn-link text-decoration-none p-0">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke hasil pencarian
            </a>
        </div>

        <section class="tracking-hero" aria-labelledby="tracking-title">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div class="tracking-hero-main d-flex align-items-center gap-3">
                    <div class="tracking-avatar">{{ $initials ?: 'NA' }}</div>
                    <div>
                        <h1 class="tracking-title" id="tracking-title">{{ $prospect->child_name }}</h1>
                        <p class="tracking-subtitle">
                            Prospect: <strong>{{ $prospect->code }}</strong>
                            <span class="mx-1">·</span>
                            Sumber: {{ ucfirst($prospect->source_module ?: '-') }}
                        </p>
                    </div>
                </div>
                <div class="tracking-current-action">
                    <small>Tahap / tindakan berikutnya</small>
                    <strong>{{ $currentAction }}</strong>
                </div>
            </div>
            <div class="tracking-contact">
                <span><i class="bi bi-person"></i>{{ $prospect->parent_name ?: '-' }}</span>
                <span><i class="bi bi-telephone"></i>{{ $prospect->phone_number ?: '-' }}</span>
                <span><i class="bi bi-envelope"></i>{{ $prospect->email ?: '-' }}</span>
                <span><i class="bi bi-people"></i>{{ ucfirst($prospect->relationship ?: 'Wali') }}</span>
                <span><i class="bi bi-calendar3"></i>
                    {{ $prospect->date_of_birth ? \Carbon\Carbon::parse($prospect->date_of_birth)->format('d M Y') : '-' }}
                </span>
                <span><i class="bi bi-mortarboard"></i>{{ $prospect->current_school ?: 'Sekolah belum diisi' }}</span>
            </div>
            @if ($prospect->address || $prospect->zipcode)
                <div class="tracking-subtitle mt-2">
                    <i class="bi bi-geo-alt me-1"></i>
                    {{ $prospect->address ?: '' }}{{ $prospect->address && $prospect->zipcode ? ', ' : '' }}{{ $prospect->zipcode ?: '' }}
                </div>
            @endif
        </section>

        <section class="tracking-journey" aria-label="Tahapan pendaftaran siswa">
            <article class="tracking-panel">
                <header class="tracking-panel-header">
                    <div>
                        <h2><i class="bi bi-1-circle me-1 text-primary"></i>School Visit</h2>
                        <p>{{ $schoolVisits->count() }} riwayat kunjungan</p>
                    </div>
                    @php
                        $visitStatus = strtolower((string) optional($latestVisit)->status);
                        $visitState = !$latestVisit
                            ? 'not_started'
                            : ($visitStatus === 'present'
                                ? 'complete'
                                : (in_array($visitStatus, ['registered'], true) ? 'in_progress' : 'attention'));
                    @endphp
                    <span class="badge rounded-pill {{ $stateClasses[$visitState] }}">
                        {{ $stateLabels[$visitState] }}
                    </span>
                </header>
                <div class="tracking-panel-body">
                    @forelse ($schoolVisits as $visit)
                        @php
                            $visitStatus = strtolower((string) $visit->status);
                            $visitRecordState = $visitStatus === 'present'
                                ? 'complete'
                                : (in_array($visitStatus, ['registered'], true) ? 'in_progress' : 'attention');
                        @endphp
                        <div class="tracking-record">
                            <div class="tracking-record-title">
                                <strong>{{ $visit->code ?: 'School Visit' }}</strong>
                                <span class="badge rounded-pill {{ $stateClasses[$visitRecordState] }}">
                                    {{ ucfirst($visit->status ?: '-') }}
                                </span>
                            </div>
                            <div class="tracking-record-meta">
                                {{ $visit->date ? \Carbon\Carbon::parse($visit->date)->format('d M Y') : '-' }}
                                @if ($visit->time)
                                    · {{ substr((string) $visit->time, 0, 5) }}
                                @endif
                                <br>
                                {{ $visit->branch->name ?? 'Cabang tidak tersedia' }}
                                @if ($visit->level_name || $visit->grade_name)
                                    · {{ $visit->level_name }} {{ $visit->grade_name }}
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="tracking-empty">Belum ada data School Visit yang tertaut.</div>
                    @endforelse
                </div>
            </article>

            <article class="tracking-panel">
                <header class="tracking-panel-header">
                    <div>
                        <h2><i class="bi bi-2-circle me-1 text-success"></i>Enrolment</h2>
                        <p>{{ $enrolmentProgress->count() }} riwayat pendaftaran</p>
                    </div>
                    @php
                        $latestEnrolmentStatus = strtoupper((string) optional($latestEnrolment['enrolment'] ?? null)->status);
                        $enrolmentStageClass = !$latestEnrolment
                            ? 'text-bg-light border text-muted'
                            : ($latestEnrolmentStatus === 'ACTIVE' ? 'text-bg-success' : 'text-bg-danger');
                    @endphp
                    <span class="badge rounded-pill {{ $enrolmentStageClass }}">
                        {{ $latestEnrolment ? ucfirst($latestEnrolment['enrolment']->status ?: 'Terdaftar') : 'Belum ada' }}
                    </span>
                </header>
                <div class="tracking-panel-body">
                    @forelse ($enrolmentProgress as $progress)
                        @php
                            $enrolment = $progress['enrolment'];
                            $admission = $progress['admission'];
                            $paymentStatus = strtoupper((string) $enrolment->payment_status);
                            $paymentClass = in_array($paymentStatus, ['PAID', 'SETTLED', 'COMPLETED'], true)
                                ? 'text-bg-success'
                                : (in_array($paymentStatus, ['PENDING', 'UNPAID'], true)
                                    ? 'text-bg-warning'
                                    : ($paymentStatus === '-' ? 'text-bg-light border text-muted' : 'text-bg-secondary'));
                        @endphp
                        <div class="tracking-record">
                            <div class="tracking-record-title">
                                <strong>{{ $enrolment->code ?: 'Enrolment' }}</strong>
                                <span class="badge rounded-pill {{ $paymentStatus !== '' ? $paymentClass : 'text-bg-light border text-muted' }}">
                                    {{ $paymentStatus !== '' ? $paymentStatus : 'Belum ada pembayaran' }}
                                </span>
                            </div>
                            <div class="tracking-record-meta">
                                {{ $enrolment->branch->name ?? 'Cabang tidak tersedia' }}
                                · {{ $enrolment->level->name ?? '-' }} / {{ $enrolment->grade->name ?? '-' }}
                                · Tahun ajaran {{ $enrolment->academic_year ?: '-' }}
                            </div>

                            <div class="tracking-checklist">
                                <div class="tracking-check-item">
                                    <span><i class="bi bi-person-vcard me-1"></i>Data diri siswa</span>
                                    <span class="badge rounded-pill {{ $stateClasses[$progress['data_state']] }}">
                                        {{ $stateLabels[$progress['data_state']] }}
                                    </span>
                                </div>
                                @if ($admission && $admission->applicant)
                                    <div class="tracking-record-meta">
                                        Nama pada formulir: {{ $admission->applicant->fullname ?: '-' }}
                                    </div>
                                @endif
                                <div class="tracking-check-item">
                                    <span><i class="bi bi-folder2-open me-1"></i>Berkas
                                        ({{ $progress['document_count'] }}/{{ count($requiredDocuments) }})</span>
                                    <span class="badge rounded-pill {{ $stateClasses[$progress['documents_state']] }}">
                                        {{ $stateLabels[$progress['documents_state']] }}
                                    </span>
                                </div>
                                @if ($admission)
                                    <ul class="tracking-document-list">
                                        @foreach ($requiredDocuments as $type => $label)
                                            @php
                                                $document = $progress['documents']->firstWhere('type', $type);
                                            @endphp
                                            <li>
                                                <i class="bi {{ $document ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                                                <span>{{ $label }}</span>
                                                @if ($document && $document->verified_at)
                                                    <span class="ms-auto text-success">Terverifikasi</span>
                                                @elseif ($document)
                                                    <span class="ms-auto text-muted">Terkirim</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="tracking-check-item">
                                    <span><i class="bi bi-pen me-1"></i>Surat pernyataan orang tua</span>
                                    <span class="badge rounded-pill {{ $stateClasses[$progress['statement_state']] }}">
                                        {{ $stateLabels[$progress['statement_state']] }}
                                    </span>
                                </div>
                            </div>
                            @if ($progress['statement'] && $progress['statement']->completed_at)
                                <div class="tracking-record-meta mt-2">
                                    Diselesaikan {{ $progress['statement']->completed_at->format('d M Y H:i') }}
                                </div>
                            @elseif ($progress['statement'] && $progress['statement']->signature_uploaded_at)
                                <div class="tracking-record-meta mt-2">
                                    Tanda tangan diunggah
                                    {{ $progress['statement']->signature_uploaded_at->format('d M Y H:i') }}
                                </div>
                            @endif

                            @if ($enrolment->transactions->isNotEmpty())
                                <div class="mt-3">
                                    <div class="small fw-bold text-muted mb-1">Riwayat pembayaran</div>
                                    @foreach ($enrolment->transactions->sortByDesc('created_at') as $transaction)
                                        <div class="tracking-transaction">
                                            <span>
                                                {{ $transaction->invoice_id ?: $transaction->code }}
                                                · {{ $transaction->details->pluck('type')->unique()->implode(', ') ?: 'Pembayaran' }}
                                            </span>
                                            <span class="text-nowrap">
                                                {{ strtoupper($transaction->payment_status ?: '-') }}
                                                · Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="tracking-empty">Belum ada data Enrolment yang tertaut.</div>
                    @endforelse
                </div>
            </article>

            <article class="tracking-panel">
                <header class="tracking-panel-header">
                    <div>
                        <h2><i class="bi bi-3-circle me-1 text-warning"></i>Observation</h2>
                        <p>{{ $observations->count() }} riwayat observation</p>
                    </div>
                    @php
                        $observationStatus = strtolower((string) optional($latestObservation)->status);
                        $observationState = !$latestObservation
                            ? 'not_started'
                            : ($observationStatus === 'present'
                                ? 'complete'
                                : (in_array($observationStatus, ['registered'], true) ? 'in_progress' : 'attention'));
                    @endphp
                    <span class="badge rounded-pill {{ $stateClasses[$observationState] }}">
                        {{ $latestObservation ? ucfirst($latestObservation->status) : $stateLabels[$observationState] }}
                    </span>
                </header>
                <div class="tracking-panel-body">
                    @forelse ($observations as $observation)
                        @php
                            $observationStatus = strtolower((string) $observation->status);
                            $observationRecordState = $observationStatus === 'present'
                                ? 'complete'
                                : (in_array($observationStatus, ['registered'], true) ? 'in_progress' : 'attention');
                        @endphp
                        <div class="tracking-record">
                            <div class="tracking-record-title">
                                <strong>{{ $observation->date ? \Carbon\Carbon::parse($observation->date)->format('d M Y') : '-' }}
                                    {{ $observation->time ? substr((string) $observation->time, 0, 5) : '' }}</strong>
                                <span class="badge rounded-pill {{ $stateClasses[$observationRecordState] }}">
                                    {{ ucfirst($observation->status ?: '-') }}
                                </span>
                            </div>
                            <div class="tracking-record-meta">
                                {{ $observation->level ?: '-' }} / {{ $observation->grade ?: '-' }}
                                · {{ $observation->branch->name ?? 'Cabang tidak tersedia' }}
                            </div>
                        </div>
                    @empty
                        <div class="tracking-empty">Belum ada data Observation yang tertaut.</div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="tracking-panel mt-4">
            <header class="tracking-panel-header">
                <div>
                    <h2><i class="bi bi-clock-history me-1 text-success"></i>Riwayat aktivitas</h2>
                    <p>Aktivitas yang tercatat pada Prospect dan proses terkait.</p>
                </div>
                <span class="badge rounded-pill text-bg-light border text-dark">{{ $activities->count() }}</span>
            </header>
            <div class="tracking-panel-body">
                @if ($activities->isNotEmpty())
                    <ul class="tracking-history">
                        @foreach ($activities as $activity)
                            @php
                                $activityType = $activity->activityable
                                    ? class_basename($activity->activityable)
                                    : class_basename($activity->activityable_type);
                            @endphp
                            <li class="tracking-history-item">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="tracking-history-date">
                                        {{ $activity->created_at ? $activity->created_at->format('d M Y H:i') : '-' }}
                                    </span>
                                    <span class="badge rounded-pill text-bg-light border text-muted">{{ $activityType }}</span>
                                </div>
                                <p class="tracking-history-note">{{ $activity->note ?: 'Aktivitas tercatat.' }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="tracking-empty">Belum ada catatan aktivitas pada Prospect ini.</div>
                @endif
            </div>
        </section>
    </main>
@endsection
