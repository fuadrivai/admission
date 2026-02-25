@extends('main-layout.index')

@section('content-style')
    <style>
        .detail-label {
            font-weight: 600;
            width: 140px;
        }

        .detail-value {
            flex: 1;
        }

        .info-group:not(:last-child) {
            margin-bottom: .75rem;
        }

        .student-avatar-big {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #495057;
            font-weight: 700;
        }

        .section-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .35rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .detail-header {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header detail-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="student-avatar-big">{{ strtoupper(substr($visit->child_name ?? '-', 0, 1)) }}</div>
                    <h5 class="mb-0">{{ $visit->child_name ?? '-' }}</h5>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Code: <span class="fw-bold">{{ $visit->code ?? '-' }}</span></div>
                    @php
                        $st = strtolower($visit->status ?? '');
                        $badgeBg = 'bg-secondary text-white';
                        switch ($st) {
                            case 'registered':
                                $badgeBg = 'bg-info text-white';
                                break;
                            case 'present':
                                $badgeBg = 'bg-success text-white';
                                break;
                            case 'absent':
                                $badgeBg = 'bg-danger text-white';
                                break;
                            case 'cancelled':
                                $badgeBg = 'bg-warning text-dark';
                                break;
                        }
                    @endphp
                    <span class="badge {{ $badgeBg }}">{{ ucfirst($visit->status ?? '-') }}</span>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-card">
                            <h6 class="mb-3"><i class="fa fa-calendar me-1"></i> Visit Information</h6>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-calendar-alt me-1"></i>Date</div>
                                <div class="detail-value">{{ $visit->date ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-clock me-1"></i>Time</div>
                                <div class="detail-value">{{ $visit->time ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-users me-1"></i>Visitors</div>
                                <div class="detail-value">{{ $visit->number_visitor ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-check-circle me-1"></i>Already Enrol</div>
                                <div class="detail-value">{{ ucfirst($visit->already_enrol ?? '-') }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-school me-1"></i>Academic Year</div>
                                <div class="detail-value">{{ $visit->academic_year ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-building me-1"></i>Current School</div>
                                <div class="detail-value">{{ $visit->current_school ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-info-circle me-1"></i>Info From</div>
                                <div class="detail-value">{{ $visit->info_from ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-comment me-1"></i>Message</div>
                                <div class="detail-value">{{ $visit->info_from_message ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-card">
                            <h6 class="mb-3"><i class="fa fa-address-book me-1"></i>Contact & Academic</h6>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-user me-1"></i>Parent</div>
                                <div class="detail-value">{{ $visit->parent_name ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-phone me-1"></i>Phone</div>
                                <div class="detail-value">{{ $visit->phone_number ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-envelope me-1"></i>Email</div>
                                <div class="detail-value">{{ $visit->email ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-map-pin me-1"></i>Zipcode</div>
                                <div class="detail-value">{{ $visit->zipcode ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-building me-1"></i>Branch</div>
                                <div class="detail-value">{{ $visit->branch->name ?? '-' }}</div>
                            </div>
                            <div class="d-flex info-group">
                                <div class="detail-label"><i class="fa fa-layer-group me-1"></i>Level / Grade</div>
                                <div class="detail-value">{{ $visit->level->name ?? '-' }} /
                                    {{ $visit->grade->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="info-group">
                            <div class="detail-label"><i class="fa fa-user-tag me-1"></i>Declaration</div>
                            <div class="detail-value mt-2">
                                @if (!empty($visit->roles))
                                    <ul>
                                        @foreach ($visit->roles as $role)
                                            <li><span class="badge rounded-pill bg-light text-dark border py-1 px-2">

                                                    {{ ucfirst($role['value']) }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
