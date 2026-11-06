@extends('main-layout.index')

@section('content-style')
    <style>
        .am-page {
            --am-ink: #172b4d;
            --am-muted: #738096;
            --am-line: #e8edf3;
            --am-green: #167c65;
            color: var(--am-ink);
        }

        .am-hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
            padding: 1.45rem 1.6rem;
            border: 1px solid #e8eee9;
            border-radius: 18px;
            background: linear-gradient(115deg, #f3faf7 0%, #fff 68%);
        }

        .am-eyebrow {
            margin: 0 0 .4rem;
            color: var(--am-green);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .am-heading {
            margin: 0;
            color: var(--am-ink);
            font-size: clamp(1.3rem, 2.5vw, 1.8rem);
            font-weight: 750;
        }

        .am-intro {
            max-width: 700px;
            margin: .45rem 0 0;
            color: var(--am-muted);
            font-size: .94rem;
            line-height: 1.55;
        }

        .am-filter {
            display: flex;
            align-items: center;
            gap: .55rem;
            min-width: 220px;
        }

        .am-filter label {
            margin: 0;
            color: var(--am-muted);
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .am-filter .form-select {
            min-width: 155px;
            border-color: #dce5e2;
            border-radius: 10px;
            font-size: .86rem;
        }

        .am-note {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            margin-bottom: 1.2rem;
            padding: .8rem 1rem;
            border: 1px solid #e4ecff;
            border-radius: 12px;
            background: #f5f8ff;
            color: #53647d;
            font-size: .86rem;
            line-height: 1.5;
        }

        .am-note i {
            margin-top: .1rem;
            color: #4a70bd;
        }

        .am-stat {
            height: 100%;
            padding: 1.2rem;
            border: 1px solid var(--am-line);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(27, 47, 76, .035);
            transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
        }

        .am-stat-link {
            display: block;
            height: 100%;
            color: inherit;
        }

        .am-stat-link:hover .am-stat {
            transform: translateY(-2px);
            border-color: #cfe2d9;
            box-shadow: 0 9px 24px rgba(27, 77, 61, .09);
        }

        .am-stat-link:focus-visible {
            border-radius: 16px;
            outline: 3px solid rgba(22, 124, 101, .35);
            outline-offset: 3px;
        }

        .am-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .7rem;
            margin-bottom: .85rem;
        }

        .am-stat-icon {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border-radius: 12px;
            font-size: 1.05rem;
        }

        .am-stat-icon.visit { background: #eaf3ff; color: #4375b9; }
        .am-stat-icon.enrol { background: #e8f7f1; color: #198266; }
        .am-stat-icon.payment { background: #fff4df; color: #b17813; }
        .am-stat-icon.document { background: #f1edff; color: #765bb7; }

        .am-stat-label {
            margin: 0;
            color: #536178;
            font-size: .88rem;
            font-weight: 700;
        }

        .am-stat-number {
            margin: 0;
            color: var(--am-ink);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1.1;
        }

        .am-stat-hint {
            min-height: 2.5em;
            margin: .55rem 0 0;
            color: var(--am-muted);
            font-size: .78rem;
            line-height: 1.5;
        }

        .am-section-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin: 1.8rem 0 .85rem;
        }

        .am-section-heading h2 {
            margin: 0;
            color: var(--am-ink);
            font-size: 1.08rem;
            font-weight: 750;
        }

        .am-section-heading p {
            margin: .25rem 0 0;
            color: var(--am-muted);
            font-size: .82rem;
        }

        .am-panel {
            height: 100%;
            overflow: hidden;
            border: 1px solid var(--am-line);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(27, 47, 76, .035);
        }

        .am-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .8rem;
            padding: 1.05rem 1.15rem;
            border-bottom: 1px solid #eef1f5;
        }

        .am-panel-head h3 {
            margin: 0;
            color: var(--am-ink);
            font-size: .94rem;
            font-weight: 750;
        }

        .am-panel-head p {
            margin: .25rem 0 0;
            color: var(--am-muted);
            font-size: .76rem;
        }

        .am-count {
            display: inline-flex;
            min-width: 28px;
            height: 28px;
            align-items: center;
            justify-content: center;
            padding: 0 .45rem;
            border-radius: 999px;
            background: #f0f3f7;
            color: #536178;
            font-size: .78rem;
            font-weight: 800;
        }

        .am-list {
            padding: .25rem 1.15rem;
        }

        .am-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            padding: .85rem 0;
            border-bottom: 1px solid #f0f2f5;
        }

        .am-row:last-child { border-bottom: 0; }

        .am-row-title {
            display: block;
            color: var(--am-ink);
            font-size: .84rem;
            font-weight: 700;
            text-decoration: none;
        }

        .am-row-title:hover { color: var(--am-green); }

        .am-row .flex-grow-1 { min-width: 0; }

        .am-row-meta {
            margin-top: .2rem;
            color: var(--am-muted);
            font-size: .74rem;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .am-tag {
            display: inline-flex;
            align-items: center;
            padding: .28rem .52rem;
            border-radius: 7px;
            background: #f2f5f8;
            color: #59677b;
            font-size: .68rem;
            font-weight: 750;
            white-space: nowrap;
        }

        .am-tag.warning { background: #fff4df; color: #99640c; }
        .am-tag.success { background: #e8f7f1; color: #16765e; }

        .am-empty {
            padding: 1.6rem 1rem;
            color: var(--am-muted);
            font-size: .82rem;
            text-align: center;
        }

        .am-empty i {
            display: block;
            margin-bottom: .45rem;
            color: #4d9b7e;
            font-size: 1.25rem;
        }

        .am-panel-foot {
            padding: .8rem 1.15rem;
            border-top: 1px solid #eef1f5;
            text-align: right;
        }

        .am-panel-foot a {
            color: var(--am-green);
            font-size: .78rem;
            font-weight: 750;
            text-decoration: none;
        }

        .am-dp-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .am-dp-summary {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
            padding: 1rem 1.15rem .8rem;
        }

        .am-dp-summary strong {
            display: block;
            color: var(--am-ink);
            font-size: 1.45rem;
            line-height: 1.2;
        }

        .am-dp-summary span {
            display: block;
            margin-top: .2rem;
            color: var(--am-muted);
            font-size: .76rem;
        }

        .am-dp-summary a {
            color: var(--am-green);
            font-size: .74rem;
            font-weight: 750;
            text-decoration: none;
            white-space: nowrap;
        }

        .am-date {
            flex: 0 0 auto;
            min-width: 53px;
            padding: .42rem .45rem;
            border-radius: 10px;
            background: #f2f7ff;
            color: #426caa;
            font-size: .7rem;
            font-weight: 800;
            line-height: 1.25;
            text-align: center;
        }

        .am-date strong {
            display: block;
            font-size: 1rem;
        }

        @media (max-width: 767.98px) {
            .am-hero {
                align-items: stretch;
                flex-direction: column;
                padding: 1.15rem;
            }

            .am-filter {
                width: 100%;
            }

            .am-filter .form-select {
                flex: 1;
            }

            .am-stat {
                padding: 1rem;
            }

            .am-stat-hint {
                min-height: 0;
            }

            .am-section-heading {
                align-items: flex-start;
                flex-direction: column;
                gap: .25rem;
            }
        }

        @media (max-width: 420px) {
            .am-dp-grid {
                grid-template-columns: 1fr;
            }

            .am-row {
                align-items: flex-start;
            }

            .am-tag {
                white-space: normal;
                text-align: center;
            }
        }
    </style>
@endsection

@section('content-child')
    <main class="am-page">
        <section class="am-hero" aria-labelledby="am-title">
            <div>
                <p class="am-eyebrow">ADMISSION · {{ $branchName }}</p>
                <h1 class="am-heading" id="am-title">Apa yang perlu dipantau hari ini?</h1>
                <p class="am-intro">
                    @if ($canViewDocuments)
                        Ringkasan School Visit, Enrolment, pembayaran, dan dokumen siswa dalam satu halaman.
                    @else
                        Ringkasan School Visit, Enrolment, dan pembayaran dalam satu halaman.
                    @endif
                    Pilih angka atau daftar untuk membuka data di menu terkait.
                </p>
            </div>
            @if (!$branchRestricted)
                <form class="am-filter" method="GET" action="{{ route('admission-monitoring.index') }}">
                    <label for="am-branch">Cabang</label>
                    <select class="form-select" id="am-branch" name="branch_id" onchange="this.form.submit()">
                        <option value="">Semua cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ (string) $selectedBranch === (string) $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </section>

        <div class="am-note">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <span>
                @if ($canViewDocuments)
                    Setiap proses berjalan sendiri. Satu calon siswa bisa melakukan School Visit, Enrolment,
                    atau pengumpulan dokumen pada waktu yang berbeda—jadi angka di bawah menunjukkan jumlah
                    di tiap proses dan tidak untuk dijumlahkan.
                @else
                    School Visit dan Enrolment adalah proses terpisah. Satu calon siswa bisa melakukan keduanya
                    pada waktu berbeda—jadi angka di bawah menunjukkan jumlah di tiap proses dan tidak untuk dijumlahkan.
                @endif
            </span>
        </div>

        @if ($branchUnavailable)
            <div class="alert alert-warning" role="alert">
                Cabang akun ini belum tersedia untuk monitoring. Hanya cabang dengan <code>show_in_form = 1</code> yang ditampilkan.
            </div>
        @endif

        <section class="row g-3" aria-label="Ringkasan proses admission">
            @php($statColumn = $canViewDocuments ? 'col-12 col-sm-6 col-xl-3' : 'col-12 col-sm-6 col-xl-4')
            <div class="{{ $statColumn }}">
                <a class="am-stat-link text-decoration-none" href="{{ route('schoolvisit.index') }}">
                    <article class="am-stat">
                        <div class="am-stat-top">
                            <p class="am-stat-label">School Visit terjadwal</p>
                            <span class="am-stat-icon visit"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
                        </div>
                        <p class="am-stat-number">{{ number_format($schoolVisitsCount) }}</p>
                        <p class="am-stat-hint">Kunjungan berstatus terdaftar mulai hari ini dan seterusnya.</p>
                    </article>
                </a>
            </div>
            <div class="{{ $statColumn }}">
                <a class="am-stat-link text-decoration-none" href="{{ route('enrolment.index') }}">
                    <article class="am-stat">
                        <div class="am-stat-top">
                            <p class="am-stat-label">Pendaftar berstatus aktif</p>
                            <span class="am-stat-icon enrol"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                        </div>
                        <p class="am-stat-number">{{ number_format($activeEnrolmentsCount) }}</p>
                        <p class="am-stat-hint">
                            Jumlah Enrolment yang belum dibatalkan, bukan jumlah pembayaran lunas. Contoh: jika 2 data pendaftaran digabung, yang dihitung hanya 1 data utama.
                        </p>
                    </article>
                </a>
            </div>
            <div class="{{ $statColumn }}">
                <a class="am-stat-link text-decoration-none" href="{{ route('enrolment.index') }}">
                    <article class="am-stat">
                        <div class="am-stat-top">
                            <p class="am-stat-label">{{ $paymentLabel }}</p>
                            <span class="am-stat-icon payment"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                        </div>
                        <p class="am-stat-number">{{ number_format($pendingPaymentsCount) }}</p>
                        <p class="am-stat-hint">
                            @if ($canViewDocuments)
                                Transaksi Enrolment atau DP berstatus menunggu pembayaran.
                            @else
                                Transaksi pendaftaran berstatus menunggu pembayaran.
                            @endif
                        </p>
                    </article>
                </a>
            </div>
            @if ($canViewDocuments)
                <div class="{{ $statColumn }}">
                    <a class="am-stat-link text-decoration-none" href="{{ route('applicant.index') }}">
                        <article class="am-stat">
                            <div class="am-stat-top">
                                <p class="am-stat-label">Admission perlu dilengkapi</p>
                                <span class="am-stat-icon document"><i class="bi bi-folder2-open" aria-hidden="true"></i></span>
                            </div>
                            <p class="am-stat-number">{{ number_format($incompleteAdmissionsCount) }}</p>
                            <p class="am-stat-hint">Form, pernyataan, atau empat dokumen wajib belum lengkap.</p>
                        </article>
                    </a>
                </div>
            @endif
        </section>

        <div class="am-section-heading">
            <div>
                <h2>Status pembayaran DP</h2>
                <p>Untuk Enrolment aktif. “Belum lunas” berarti belum ada transaksi DP berstatus lunas—DP bisa belum dibuat, masih menunggu, atau kedaluwarsa.</p>
            </div>
        </div>
        <section class="am-dp-grid" aria-label="Status pembayaran DP">
            <article class="am-panel">
                <div class="am-dp-summary">
                    <div>
                        <strong>{{ number_format($dpPaidCount) }}</strong>
                        <span>Enrolment sudah membayar DP</span>
                    </div>
                    <a href="{{ route('enrolment.index', ['dp_status' => 'paid', 'branch' => $selectedBranch]) }}">
                        Lihat daftar <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="am-list">
                    @forelse ($dpPaidEnrolments as $enrolment)
                        <div class="am-row">
                            <div>
                                <a class="am-row-title" href="{{ route('enrolment.edit', $enrolment->id) }}">
                                    {{ $enrolment->child_name }}
                                </a>
                                <div class="am-row-meta">
                                    {{ $enrolment->code }}
                                    @if ($enrolment->branch)
                                        · {{ $enrolment->branch->name }}
                                    @endif
                                </div>
                            </div>
                            <span class="am-tag success">DP lunas</span>
                        </div>
                    @empty
                        <div class="am-empty">Belum ada Enrolment aktif dengan DP lunas.</div>
                    @endforelse
                </div>
            </article>
            <article class="am-panel">
                <div class="am-dp-summary">
                    <div>
                        <strong>{{ number_format($dpUnpaidCount) }}</strong>
                        <span>Enrolment belum lunas DP</span>
                    </div>
                    <a href="{{ route('enrolment.index', ['dp_status' => 'unpaid', 'branch' => $selectedBranch]) }}">
                        Lihat daftar <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="am-list">
                    @forelse ($dpUnpaidEnrolments as $enrolment)
                        <div class="am-row">
                            <div>
                                <a class="am-row-title" href="{{ route('enrolment.edit', $enrolment->id) }}">
                                    {{ $enrolment->child_name }}
                                </a>
                                <div class="am-row-meta">
                                    {{ $enrolment->code }}
                                    @if ($enrolment->branch)
                                        · {{ $enrolment->branch->name }}
                                    @endif
                                </div>
                            </div>
                            <span class="am-tag warning">Belum lunas</span>
                        </div>
                    @empty
                        <div class="am-empty">Semua Enrolment aktif sudah membayar DP.</div>
                    @endforelse
                </div>
            </article>
        </section>

        <div class="am-section-heading">
            <div>
                <h2>Daftar yang bisa ditindaklanjuti</h2>
                <p>Pilih nama atau kode untuk membuka detailnya.</p>
            </div>
        </div>

        <section class="row g-3" aria-label="Daftar tindak lanjut">
            <div class="col-12 {{ $canViewDocuments ? 'col-xl-4' : 'col-xl-6' }}">
                <article class="am-panel">
                    <header class="am-panel-head">
                        <div>
                            <h3>Jadwal School Visit berikutnya</h3>
                            <p>Periksa jadwal dan catat kehadiran setelah kunjungan.</p>
                        </div>
                        <span class="am-count">{{ number_format($schoolVisitsCount) }}</span>
                    </header>
                    <div class="am-list">
                        @forelse ($schoolVisits as $visit)
                            <div class="am-row">
                                <div class="am-date">
                                    <strong>{{ \Carbon\Carbon::parse($visit->date)->format('d') }}</strong>
                                    {{ \Carbon\Carbon::parse($visit->date)->format('M') }}
                                </div>
                                <div class="flex-grow-1">
                                    <a class="am-row-title" href="{{ route('schoolvisit.edit', $visit->id) }}">
                                        {{ $visit->child_name }}
                                    </a>
                                    <div class="am-row-meta">
                                        {{ $visit->code }} · {{ \Carbon\Carbon::parse($visit->time)->format('H:i') }}
                                        @if ($visit->branch)
                                            · {{ $visit->branch->name }}
                                        @endif
                                    </div>
                                </div>
                                <span class="am-tag">Terdaftar</span>
                            </div>
                        @empty
                            <div class="am-empty">
                                <i class="bi bi-check-circle" aria-hidden="true"></i>
                                Tidak ada jadwal mendatang yang perlu diperiksa.
                            </div>
                        @endforelse
                    </div>
                    <footer class="am-panel-foot">
                        <a href="{{ route('schoolvisit.index') }}">Buka daftar School Visit <i class="bi bi-arrow-right"></i></a>
                    </footer>
                </article>
            </div>

            <div class="col-12 {{ $canViewDocuments ? 'col-xl-4' : 'col-xl-6' }}">
                <article class="am-panel">
                    <header class="am-panel-head">
                        <div>
                            <h3>{{ $paymentLabel }}</h3>
                            <p>Cek transaksi yang belum berstatus lunas.</p>
                        </div>
                        <span class="am-count">{{ number_format($pendingPaymentsCount) }}</span>
                    </header>
                    <div class="am-list">
                        @forelse ($pendingPayments as $transaction)
                            <div class="am-row">
                                <div class="flex-grow-1">
                                    <a class="am-row-title"
                                        href="{{ $transaction->enrolment ? route('enrolment.edit', $transaction->enrolment_id) : route('enrolment.index') }}">
                                        {{ optional($transaction->enrolment)->child_name ?: $transaction->code }}
                                    </a>
                                    <div class="am-row-meta">
                                        {{ $transaction->code }}
                                        · {{ $transaction->details->pluck('type')->unique()->map(function ($type) { return strtoupper($type); })->implode(', ') }}
                                        @if ($transaction->expiry_va_date)
                                            · Jatuh tempo {{ $transaction->expiry_va_date->format('d M Y') }}
                                        @endif
                                    </div>
                                </div>
                                <span class="am-tag warning">{{ strtoupper($transaction->payment_status) }}</span>
                            </div>
                        @empty
                            <div class="am-empty">
                                <i class="bi bi-check-circle" aria-hidden="true"></i>
                                Tidak ada transaksi aktif yang menunggu pembayaran.
                            </div>
                        @endforelse
                    </div>
                    <footer class="am-panel-foot">
                        <a href="{{ route('enrolment.index') }}">Buka daftar Enrolment <i class="bi bi-arrow-right"></i></a>
                    </footer>
                </article>
            </div>

            @if ($canViewDocuments)
                <div class="col-12 col-xl-4">
                    <article class="am-panel">
                        <header class="am-panel-head">
                            <div>
                                <h3>Data Admission belum lengkap</h3>
                                <p>Form, pernyataan orang tua, atau dokumen masih kurang.</p>
                            </div>
                            <span class="am-count">{{ number_format($incompleteAdmissionsCount) }}</span>
                        </header>
                        <div class="am-list">
                            @forelse ($admissionsNeedingAttention as $admission)
                                <div class="am-row">
                                    <div class="flex-grow-1">
                                        <a class="am-row-title" href="{{ route('applicant.edit', $admission->id) }}">
                                            {{ optional($admission->enrolment)->child_name ?: $admission->code }}
                                        </a>
                                        <div class="am-row-meta">
                                            {{ $admission->code }}
                                            @if (!$admission->is_complete)
                                                · Form siswa
                                            @endif
                                            @if (!$admission->statement || !$admission->statement->is_completed)
                                                · Pernyataan
                                            @endif
                                            @if ($admission->documents_count != 4)
                                                · Dokumen {{ $admission->documents_count }}/4
                                            @endif
                                        </div>
                                    </div>
                                    <span class="am-tag warning">Perlu dicek</span>
                                </div>
                            @empty
                                <div class="am-empty">
                                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                                    Semua data Admission pada cabang ini sudah lengkap.
                                </div>
                            @endforelse
                        </div>
                        <footer class="am-panel-foot">
                            <a href="{{ route('applicant.index') }}">Buka daftar Documents <i class="bi bi-arrow-right"></i></a>
                        </footer>
                    </article>
                </div>
            @endif
        </section>
    </main>
@endsection
