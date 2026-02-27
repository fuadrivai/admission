<div class="card shadow-sm">
    <div class="card-body py-2">
        <div class="row text-center gy-2">
            <div class="col-6 col-md">
                <div class="small text-muted">Total</div>
                <div class="h5 mb-0">{{ $summary['total'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Registered</div>
                <div class="h5 mb-0">{{ $summary['registered'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Present</div>
                <div class="h5 mb-0">{{ $summary['present'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Cancelled</div>
                <div class="h5 mb-0">{{ $summary['cancelled'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Absent</div>
                <div class="h5 mb-0">{{ $summary['absent'] ?? 0 }}</div>
            </div>
            <hr>
        </div>
        <div class="row text-center gy-2">
            <div class="col-6 col-md">
                <div class="small text-muted">Total Enrol</div>
                <div class="h5 mb-0">{{ $summary['enrolSummary']['enrolled'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Pending</div>
                <div class="h5 mb-0">{{ $summary['enrolSummary']['pending'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Paid</div>
                <div class="h5 mb-0">{{ $summary['enrolSummary']['paid'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Expired</div>
                <div class="h5 mb-0">{{ $summary['enrolSummary']['expired'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

@forelse ($visits as $visit)
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <div class="student-avatar">
                        {{ ucfirst(\App\Helpers\avatarName($visit->child_name)) }}</div>
                </div>
                @php
                    $bg = 'bg-default';
                    switch ($visit->status) {
                        case 'registered':
                            $bg = 'bg-info';
                            break;
                        case 'present':
                            $bg = 'bg-success';
                            break;
                        case 'absent':
                            $bg = 'bg-danger';
                            break;
                        case 'cancelled':
                            $bg = 'bg-warning';
                            break;
                        default:
                            $bg = 'bg-default';
                            break;
                    }
                @endphp

                <div class="col-md-5">
                    <label>Kode : {{ $visit->code }}</label><br>
                    <div class="student-name">{{ ucwords($visit->child_name) }}</div>
                </div>
                <div class="col-md-6 text-end" style="vertical-align: middle">
                    <a data-id="{{ $visit->prospects_id }}" title="view history"
                        class="btn btn-sm btn-success btn-history"><i class="fa fa-history"></i></a>
                    <a href="/schoolvisit/{{ $visit->id }}/edit" title="View Detail"
                        class="btn btn-sm btn-primary view-detail"><i class="fa fa-eye"></i></a>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButtonEmoji"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="fa fa-cog"></i>
                        </button>
                        <div class="dropdown-menu bg-danger ob" aria-labelledby="dropdownMenuButtonEmoji">
                            <button class="dropdown-item text-white btn-edit" data-id="{{ $visit->id }}"><i
                                    class="fa fa-pencil"></i>
                                Reschedule</button>
                            <button class="dropdown-item text-white btn-confirm"
                                onclick="changeStatus('present',{{ $visit->id }})" data-id="{{ $visit->id }}"><i
                                    class="bi bi-star"></i>
                                Confirm</button>
                            <button class="dropdown-item text-white btn-detail"
                                onclick="changeStatus('cancelled',{{ $visit->id }})"
                                data-id="{{ $visit->id }}"><i class="fa fa-trash"></i>
                                Cancel</button>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="visit-info">
                        <div class="info-group">
                            <div class="info-label">Visit Info</div>
                            <div class="info-value">
                                Date: {{ $visit->dateTime() }}<br>
                                Number of Visitors: {{ $visit->number_visitor }}<br>
                                Status: <span class="badge {{ $bg }}">{{ ucwords($visit->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label">Academic Info</div>
                            <div class="info-value">
                                @if (isset($visit->level->division->name))
                                    @php
                                        $div = strtolower($visit->level->division->name);
                                        $badgeClass = 'bg-secondary text-white';
                                        switch ($div) {
                                            case 'preschool':
                                                $badgeClass = 'bg-warning text-dark';
                                                break;
                                            case 'primary':
                                                $badgeClass = 'bg-success text-white';
                                                break;
                                            case 'secondary':
                                                $badgeClass = 'bg-info text-white';
                                                break;
                                            case 'development class':
                                                $badgeClass = 'bg-purple text-white'; // may need custom color
                                                break;
                                        }
                                    @endphp
                                    Academic Year: {{ $visit->academic_year }}<br>
                                    Branch: {{ $visit->branch_name ?? '-' }}<br>
                                    Level:
                                    <span class="badge {{ $badgeClass }}" style="opacity:.85; font-size:.85em;">
                                        {{ $visit->level->name ?? '-' }}/{{ $visit->grade->name ?? '-' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="parent-info">
                        <div class="info-group">
                            <div class="info-label">Parent</div>
                            <div class="info-value">
                                {{ $visit->parent_name }}
                            </div>
                            <div class="info-value">
                                {{ $visit->email }}<br>
                                {{ $visit->phone_number }}
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="visit-history">
                        @php
                            $prospect = $visit->prospect;
                            $enrolment = optional($prospect)->enrolment;
                            $admission = optional($enrolment)->admission;
                            $documents = optional($admission)->documents ?? collect();
                            $agreement = optional($admission)->statement ?? null;
                        @endphp

                        <div class="completion-tracker">
                            <div class="steps-container">
                                <div
                                    class="step {{ !isset($enrolment) ? 'pending' : ($enrolment->payment_status == 'PAID' ? 'completed' : ($enrolment->payment_status == 'PENDING' ? 'active' : 'expired')) }}">
                                    <div class="step-circle"><i class="fa fa-credit-card"></i></div>
                                    <div class="step-label">Payment
                                        <small><i>{{ !isset($enrolment) ? '' : "($enrolment->payment_status)" }}</i></small>
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
    {{ $visits->onEachSide(0)->links() }}
</div>
