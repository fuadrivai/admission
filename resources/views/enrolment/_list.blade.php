@forelse ($enrolments as $enrolment)
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <div class="student-avatar">{{ $enrolment->avatarName() }}</div>
                </div>
                <div class="col-md-5">
                    <label for="">Kode : {{ $enrolment->code }} <span
                            class="badge text-bg-secondary">{{ ucfirst($enrolment->source_data) }}</span> </label><br>
                    <div class="student-name">{{ $enrolment->child_name }}</div>
                </div>
                <div class="col-md-6 text-end" style="vertical-align: middle">
                    <a data-id="{{ $enrolment->id }}" class="btn btn-sm btn-primary view-detail"><i
                            class="fa fa-eye"></i></a>
                    <a data-id="{{ $enrolment->id }}" class="btn btn-sm btn-info edit-data"><i
                            class="fa fa-edit"></i></a>
                </div>
                <hr>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label">Informasi Akademik</div>
                            <div class="info-value">
                                Tahun Ajaran: {{ $enrolment->academic_year }}<br>
                                Cabang: {{ $enrolment->branch->name ?? '-' }}<br>
                                Level: {{ $enrolment->level->name }} / {{ $enrolment->grade->name }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="parent-info">
                        <div class="info-group">
                            <div class="info-label">Orang Tua
                                (<small>{{ $enrolment->relationship }}</small>)
                            </div>
                            <div class="info-value">
                                {{ $enrolment->parent_name }}
                            </div>
                            <div class="info-value">
                                {{ $enrolment->email }}<br>
                                {{ $enrolment->phone_number }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-value">
                                <span class="invoice-id">{{ $enrolment->invoice_id }}</span><br>
                                Tgl Enrol: {{ \Carbon\Carbon::parse($enrolment->created_at)->format('d M Y H:i') }}<br>
                                Status: <span
                                    class="badge {{ $enrolment->payment_status === 'PAID' ? 'text-bg-success' : ($enrolment->payment_status === 'PENDING' ? 'text-bg-warning' : 'text-bg-danger') }}">
                                    {{ $enrolment->payment_status }}
                                </span><br>
                                {{ $enrolment->payment_date ? 'Tanggal Bayar: ' . \Carbon\Carbon::parse($enrolment->payment_date)->format('d M Y') : 'Belum melakukan pembayaran' }}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center text-muted">
        Data tidak ditemukan
    </div>
@endforelse

<div class="mt-3">
    {{ $enrolments->links() }}
</div>
