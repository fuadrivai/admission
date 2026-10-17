@extends('main-layout.index')

@section('content-style')
    <style>
        .merge-pair-row {
            border: 1px solid #dfe3e8;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: .75rem;
        }

        .merge-pair-row .pair-number {
            font-weight: 700;
            color: #475467;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Batch merge tidak dapat diproses:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pratinjau dan proses beberapa merge enrolment</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Aturan merge:</strong> data master dan tanggal lahir master tidak diubah. Transaksi beserta
                    detail, status, dan invoice aslinya dipindahkan dari sumber ke master. Aktivitas prospect sumber
                    digabungkan ke prospect master, lalu enrolment sumber diarsipkan. Admission dan applicant tetap
                    tertaut pada enrolment asal untuk menjaga histori.
                    <br><strong>Batch maksimal 20 pasangan.</strong>
                    Jika satu pasangan bermasalah, seluruh batch dibatalkan.
                </div>

                <form method="GET" action="{{ route('enrolment.merge') }}" id="preview-form">
                    <div id="merge-pairs">
                        @foreach ($inputPairs as $index => $inputPair)
                            <div class="merge-pair-row">
                                <div class="row align-items-end">
                                    <div class="col-12 mb-2">
                                        <span class="pair-number">Pasangan {{ $loop->iteration }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-danger float-end remove-pair">
                                            Hapus
                                        </button>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Kode sumber (akan diarsipkan)</label>
                                        <input name="pairs[{{ $index }}][source_code]" class="form-control"
                                            value="{{ $inputPair['source_code'] }}" placeholder="Contoh: MHIS-SEC52">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Kode master (tetap aktif)</label>
                                        <input name="pairs[{{ $index }}][master_code]" class="form-control"
                                            value="{{ $inputPair['master_code'] }}" placeholder="Contoh: MHIS-SEC41">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button type="button" id="add-pair" class="btn btn-outline-secondary">Tambah pasangan</button>
                        <button type="submit" class="btn btn-primary">Pratinjau semua pasangan</button>
                    </div>
                </form>

                @if ($pairs->isNotEmpty())
                    <h5 class="mb-3">Hasil pratinjau ({{ $pairs->count() }} pasangan)</h5>
                    @foreach ($pairs as $pair)
                        <div class="card border mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Pasangan {{ $pair['number'] }}: {{ $pair['source_code'] }} → {{ $pair['master_code'] }}</strong>
                                @if ($pair['blockers'])
                                    <span class="badge bg-danger">Perlu diperbaiki</span>
                                @else
                                    <span class="badge bg-success">Siap</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ([['record' => $pair['source'], 'label' => 'Sumber'], ['record' => $pair['master'], 'label' => 'Master']] as $item)
                                        <div class="col-lg-6 mb-3">
                                            <div class="card h-100 border">
                                                <div class="card-header"><strong>{{ $item['label'] }}</strong></div>
                                                <div class="card-body">
                                                    @if ($item['record'])
                                                        @php($record = $item['record'])
                                                        <dl class="row mb-0">
                                                            <dt class="col-sm-5">Kode</dt>
                                                            <dd class="col-sm-7">{{ $record->code }}</dd>
                                                            <dt class="col-sm-5">Nama siswa</dt>
                                                            <dd class="col-sm-7">{{ $record->child_name }}</dd>
                                                            <dt class="col-sm-5">Tanggal lahir</dt>
                                                            <dd class="col-sm-7">{{ optional($record->date_of_birth)->format('d M Y') ?? '-' }}</dd>
                                                            <dt class="col-sm-5">Orang tua</dt>
                                                            <dd class="col-sm-7">{{ $record->parent_name ?: '-' }}</dd>
                                                            <dt class="col-sm-5">Tahun ajaran / level / kelas</dt>
                                                            <dd class="col-sm-7">
                                                                {{ $record->academic_year ?: '-' }} /
                                                                {{ optional($record->level)->name ?: '-' }} /
                                                                {{ optional($record->grade)->name ?: '-' }}
                                                            </dd>
                                                            <dt class="col-sm-5">Status arsip</dt>
                                                            <dd class="col-sm-7">{{ $record->isMerged() ? 'Sudah merged' : 'Aktif' }}</dd>
                                                            <dt class="col-sm-5">Admission</dt>
                                                            <dd class="col-sm-7">
                                                                @if ($record->admission)
                                                                    {{ $record->admission->code }}
                                                                    ({{ $record->admission->is_complete ? 'lengkap' : 'belum lengkap' }})
                                                                @else
                                                                    Tidak ada
                                                                @endif
                                                            </dd>
                                                        </dl>
                                                        <hr>
                                                        <strong>Transaksi ({{ $record->transactions->count() }})</strong>
                                                        @if ($record->transactions->isEmpty())
                                                            <p class="text-muted mb-0">Tidak ada transaksi.</p>
                                                        @else
                                                            <ul class="mb-0">
                                                                @foreach ($record->transactions as $transaction)
                                                                    <li>
                                                                        {{ $transaction->invoice_id ?: 'Tanpa invoice' }} —
                                                                        {{ strtoupper($transaction->payment_status) }} —
                                                                        Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}
                                                                        ({{ $transaction->details->count() }} detail)
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    @else
                                                        <p class="text-muted mb-0">Record belum ditemukan.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($pair['blockers'])
                                    <div class="alert alert-danger mb-0">
                                        <strong>Pasangan ini belum bisa diproses:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($pair['blockers'] as $blocker)
                                                <li>{{ $blocker }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if ($canMerge)
                        <form method="POST" action="{{ route('enrolment.merge.store') }}"
                            onsubmit="return confirm('Proses seluruh {{ $pairs->count() }} pasangan dalam satu batch? Jika gagal, semua pasangan akan dibatalkan.');">
                            @csrf
                            @foreach ($pairs as $index => $pair)
                                <input type="hidden" name="pairs[{{ $index }}][source_code]" value="{{ $pair['source']->code }}">
                                <input type="hidden" name="pairs[{{ $index }}][master_code]" value="{{ $pair['master']->code }}">
                            @endforeach
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="confirm_merge"
                                    name="confirm_merge" value="1" required>
                                <label class="form-check-label" for="confirm_merge">
                                    Saya sudah memeriksa seluruh pasangan dan memahami bahwa jika satu pasangan gagal,
                                    seluruh batch akan dibatalkan.
                                </label>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                Proses batch merge ({{ $pairs->count() }} pasangan)
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info mb-0">
                            Perbaiki pasangan yang bermasalah dan buat pratinjau kembali. Tidak ada pasangan yang akan
                            diproses sebagian.
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        (function () {
            const container = document.getElementById('merge-pairs');
            const addButton = document.getElementById('add-pair');
            const maxPairs = 20;
            let nextIndex = {{ $inputPairs->count() }};

            function renumberRows() {
                container.querySelectorAll('.merge-pair-row').forEach(function (row, index) {
                    row.querySelector('.pair-number').textContent = 'Pasangan ' + (index + 1);
                });
                addButton.disabled = container.querySelectorAll('.merge-pair-row').length >= maxPairs;
            }

            addButton.addEventListener('click', function () {
                if (container.querySelectorAll('.merge-pair-row').length >= maxPairs) {
                    return;
                }

                const row = document.createElement('div');
                row.className = 'merge-pair-row';
                row.innerHTML =
                    '<div class="row align-items-end">' +
                        '<div class="col-12 mb-2"><span class="pair-number"></span>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger float-end remove-pair">Hapus</button></div>' +
                        '<div class="col-md-5"><label class="form-label">Kode sumber (akan diarsipkan)</label>' +
                            '<input name="pairs[' + nextIndex + '][source_code]" class="form-control" placeholder="Contoh: MHIS-SEC52"></div>' +
                        '<div class="col-md-5"><label class="form-label">Kode master (tetap aktif)</label>' +
                            '<input name="pairs[' + nextIndex + '][master_code]" class="form-control" placeholder="Contoh: MHIS-SEC41"></div>' +
                    '</div>';
                nextIndex += 1;
                container.appendChild(row);
                renumberRows();
            });

            container.addEventListener('click', function (event) {
                if (event.target.closest('.remove-pair')) {
                    const rows = container.querySelectorAll('.merge-pair-row');
                    if (rows.length > 1) {
                        event.target.closest('.merge-pair-row').remove();
                        renumberRows();
                    }
                }
            });

            renumberRows();
        })();
    </script>
@endsection
