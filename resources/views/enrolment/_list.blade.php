<div class="card shadow-sm">
    <div class="card-body py-2">
        <div class="row text-center gy-2">
            <div class="col-6 col-md">
                <div class="small text-muted">Total</div>
                <div class="h5 mb-0">{{ $summary['total'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Pending</div>
                <div class="h5 mb-0">{{ $summary['pending'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Paid</div>
                <div class="h5 mb-0">{{ $summary['paid'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Expired</div>
                <div class="h5 mb-0">{{ $summary['expired'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Cancelled</div>
                <div class="h5 mb-0">{{ $summary['cancelled'] ?? 0 }}</div>
            </div>
            <hr>
        </div>
        <div class="row text-center gy-2">
            <div class="col-6 col-md">
                <div class="small text-muted">Total Visit</div>
                <div class="h5 mb-0">{{ $summary['visitSummary']['visit'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Registered</div>
                <div class="h5 mb-0">{{ $summary['visitSummary']['registered'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Present</div>
                <div class="h5 mb-0">{{ $summary['visitSummary']['present'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Absent</div>
                <div class="h5 mb-0">{{ $summary['visitSummary']['absent'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Cancelled</div>
                <div class="h5 mb-0">{{ $summary['visitSummary']['cancelled'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

@forelse ($enrolments as $enrolment)
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <div class="student-avatar">{{ ucfirst(\App\Helpers\avatarName($enrolment->child_name)) }}</div>
                </div>
                <div class="col-md-5">
                    <label for="">Kode : {{ $enrolment->code }} <span
                            class="badge text-bg-{{ $enrolment->source_data === 'internal' ? 'secondary' : 'danger' }}">
                            {{ ucfirst($enrolment->source_data) }}
                        </span>
                        <span class="badge text-bg-{{ $enrolment->data_from === 'custom_form' ? 'primary' : '' }}">
                            {{ $enrolment->data_from == 'custom_form' ? 'Custom Form' : 'Web Form' }}
                        </span>
                    </label><br>
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
            <div class="row">
                <div class="col-md-12">
                    <div id="visit-history">
                        @php
                            $prospect = $enrolment->prospect;
                            $visit = optional($prospect)->schoolVisit;
                            $admission = optional($enrolment)->admission;
                            $documents = optional($admission)->documents ?? collect();
                            $agreement = optional($admission)->statement ?? null;
                        @endphp

                        <div class="completion-tracker">
                            <div class="steps-container">
                                <div
                                    class="step {{ !isset($visit) ? 'pending' : ($visit->status == 'present' ? 'completed' : ($visit->status == 'registered' ? 'active' : 'expired')) }}">
                                    <div class="step-circle"><i class="fa fa-graduation-cap"></i></div>
                                    <div class="step-label">Visit
                                        <small><i>{{ !isset($visit) ? '(Not Scheduled)' : "($visit->status)" }}</i></small>
                                    </div>
                                </div>
                                <div
                                    class="step {{ !isset($admission) ? 'pending' : ($admission->is_complete == 1 ? 'completed' : 'active') }}">
                                    <div class="step-circle"><i class="fa fa-user"></i></div>
                                    <div class="step-label">Enrolment</div>
                                </div>
                                <div class="step {{ count($documents) < 4 ? 'pending' : 'completed' }}">
                                    <div class="step-circle"><i class="fa fa-folder"></i></div>
                                    <div class="step-label">Documents</div>
                                </div>
                                <div
                                    class="step {{ !isset($agreement) ? 'pending' : ($agreement->is_completed == 1 ? 'completed' : 'active') }}">
                                    <div class="step-circle"><i class="fa fa-check-square"></i></div>
                                    <div class="step-label">Agreement</div>
                                </div>
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
    {{ $enrolments->onEachSide(0)->links() }}
</div>
