@extends('main-layout.index')

@section('content-style')
    <style>
        .enrolment-detail-page {
            --en-primary: #14532d;
            --en-primary-soft: #ecfdf3;
            --en-accent: #0f766e;
            --en-accent-soft: #f0fdfa;
            --en-border: #dbe7e1;
            --en-text-muted: #6b7280;
        }

        .enrolment-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--en-border);
            border-radius: 14px;
            background: linear-gradient(140deg, #f8fffb 0%, #f2fbf9 55%, #eff6ff 100%);
            box-shadow: 0 10px 24px rgba(15, 118, 110, 0.08);
            margin-bottom: 1rem;
        }

        .enrolment-hero::after {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 999px;
            right: -70px;
            top: -130px;
            background: radial-gradient(circle, rgba(20, 83, 45, 0.17) 0%, rgba(20, 83, 45, 0) 72%);
            pointer-events: none;
        }

        .enrolment-hero .card-body {
            position: relative;
            z-index: 1;
        }

        .student-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            font-weight: 700;
            letter-spacing: .5px;
            color: #fff;
            background: linear-gradient(135deg, var(--en-primary), var(--en-accent));
            box-shadow: 0 8px 18px rgba(20, 83, 45, 0.28);
        }

        .hero-title {
            margin-bottom: 2px;
            font-weight: 700;
            color: #0f172a;
        }

        .hero-subtitle {
            margin-bottom: 0;
            color: var(--en-text-muted);
            font-size: .92rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin-top: 1rem;
        }

        .summary-item {
            border: 1px solid var(--en-border);
            border-radius: 12px;
            background: #fff;
            padding: .75rem .85rem;
        }

        .summary-label {
            font-size: .75rem;
            letter-spacing: .3px;
            text-transform: uppercase;
            color: var(--en-text-muted);
            margin-bottom: .1rem;
        }

        .summary-value {
            font-size: .94rem;
            font-weight: 700;
            color: #0f172a;
        }

        .detail-card {
            border: 1px solid var(--en-border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
            margin-bottom: 1rem;
        }

        .detail-card .card-header {
            background: linear-gradient(180deg, #ffffff, #fbfffd);
            border-bottom: 1px solid var(--en-border);
            font-weight: 700;
            color: #0f172a;
        }

        .detail-list {
            display: grid;
            gap: .7rem;
        }

        .detail-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
            border-bottom: 1px dashed #e5efe9;
            padding-bottom: .5rem;
        }

        .detail-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .detail-key {
            color: var(--en-text-muted);
            font-weight: 600;
            min-width: 42%;
        }

        .detail-val {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
            word-break: break-word;
        }

        .activity-timeline {
            position: relative;
            margin: 0;
            padding-left: 1.5rem;
            list-style: none;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            top: .2rem;
            bottom: .2rem;
            left: .48rem;
            width: 2px;
            background: linear-gradient(180deg, #86efac 0%, #99f6e4 100%);
        }

        .activity-item {
            position: relative;
            margin-bottom: .95rem;
            padding: .7rem .8rem;
            border: 1px solid var(--en-border);
            border-radius: 11px;
            background: #fff;
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            left: -1.36rem;
            top: .95rem;
            background: #14b8a6;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #99f6e4;
        }

        .activity-date {
            font-size: .78rem;
            color: #0f766e;
            font-weight: 700;
            margin-bottom: .2rem;
        }

        .activity-note {
            margin: 0;
            color: #0f172a;
        }

        .empty-activity {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            color: var(--en-text-muted);
            background: #fafcfc;
        }

        @media (max-width: 991.98px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .detail-val {
                text-align: left;
            }
        }
    </style>
@endsection

@section('content-child')
    @php
        $prospect = $enrolment->prospect;
        $activities = optional($prospect)->activities ? $prospect->activities->sortByDesc('created_at') : collect();
        $admission = $enrolment->admission;
        $dash = fn ($value) => filled($value) ? $value : '-';
        $yesNo = fn ($value) => is_null($value) || $value === '' ? '-' : (in_array($value, [true, 1, '1', 'yes', 'Yes'], true) ? 'Yes' : 'No');
        $placeLabel = $enrolment->regis_place
            ? (\App\Models\RegistrationPlace::where('code', $enrolment->regis_place)->value('name') ?? $enrolment->regis_place)
            : null;
        $money = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $hasTransactions = $enrolment->transactions->isNotEmpty();

        $paymentStatus = strtoupper($enrolment->payment_status ?? '-');
        $paymentBadge = 'text-bg-secondary';
        if ($paymentStatus === 'PAID') {
            $paymentBadge = 'text-bg-success';
        } elseif ($paymentStatus === 'PENDING') {
            $paymentBadge = 'text-bg-warning';
        } elseif ($paymentStatus === 'EXPIRED' || $paymentStatus === 'CANCEL') {
            $paymentBadge = 'text-bg-danger';
        }
    @endphp

    <section class="section enrolment-detail-page">
        <div class="enrolment-hero card">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="student-chip">{{ $enrolment->avatarName() }}</div>
                        <div>
                            <h4 class="hero-title">{{ $enrolment->child_name ?? '-' }} - <i>
                                    {{ isset($enrolment->child_nick_name) ? '(' . $enrolment->child_nick_name . ')' : '' }}</i>
                            </h4>
                            <p class="hero-subtitle">
                                Code: <strong>{{ $enrolment->code ?? '-' }}</strong>
                                <span class="mx-1">•</span>
                                Invoice: <strong>{{ $enrolment->invoice_id ?? '-' }}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        @if (auth()->user()->role !== 'user' && $enrolment->canCreateDp())
                            <a href="{{ route('enrolment.dp.index', ['code' => $enrolment->code]) }}" class="btn btn-sm btn-warning"><i class="fa fa-money me-1"></i> Create DP Form</a>
                        @endif
                        <span class="badge {{ $paymentBadge }}">{{ $paymentStatus }}</span>
                        <span class="badge text-bg-light border">{{ ucfirst($enrolment->source_data ?? '-') }}</span>
                        <span
                            class="badge text-bg-light border">{{ ucfirst(str_replace('_', ' ', $enrolment->data_from ?? '-')) }}</span>
                    </div>
                </div>

                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Academic Year</div>
                        <div class="summary-value">{{ $enrolment->academic_year ?? '-' }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Branch</div>
                        <div class="summary-value">{{ $enrolment->branch->name ?? '-' }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Level / Grade</div>
                        <div class="summary-value">{{ $enrolment->level->name ?? '-' }} /
                            {{ $enrolment->grade->name ?? '-' }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Document Status</div>
                        <div class="summary-value">
                            @if ($admission)
                                {{ $admission->is_complete ? 'Complete' : 'In Progress' }}
                            @else
                                Not Started
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="detail-card card">
                    <div class="card-header"><i class="fa fa-user me-1"></i> Parent & Student Information</div>
                    <div class="card-body">
                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-key">Parent Name</div>
                                <div class="detail-val">{{ $enrolment->parent_name ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Relationship</div>
                                <div class="detail-val">{{ $enrolment->relationship ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Email</div>
                                <div class="detail-val">{{ $enrolment->email ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Phone Number</div>
                                <div class="detail-val">{{ $enrolment->phone_number ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Date of Birth</div>
                                <div class="detail-val">
                                    {{ $enrolment->date_of_birth ? $enrolment->birthDateFormatted() : '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Place of Birth</div>
                                <div class="detail-val">{{ $enrolment->place_of_birth ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Current School</div>
                                <div class="detail-val">{{ $enrolment->current_school ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Address</div>
                                <div class="detail-val">{{ $enrolment->address ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Zipcode</div>
                                <div class="detail-val">{{ $enrolment->zipcode ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Nickname</div>
                                <div class="detail-val">{{ $dash($enrolment->child_nick_name) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Social Media</div>
                                <div class="detail-val">{{ $dash($enrolment->child_sosmed) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Current Student</div>
                                <div class="detail-val">{{ $yesNo($enrolment->is_current_student) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Student Branch</div>
                                <div class="detail-val">{{ $dash($enrolment->student_branch) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">MHIS Portal Username</div>
                                <div class="detail-val">{{ $dash($enrolment->mhis_portal_username) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-card card">
                    <div class="card-header"><i class="fa fa-flag me-1"></i> Registration Information</div>
                    <div class="card-body">
                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-key">Registration Type</div>
                                <div class="detail-val">{{ $enrolment->source_data ? ucfirst($enrolment->source_data) : '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Registration Place</div>
                                <div class="detail-val">{{ $dash($placeLabel) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Data Source</div>
                                <div class="detail-val">{{ $enrolment->data_from ? ucfirst(str_replace('_', ' ', $enrolment->data_from)) : 'Legacy / Unknown' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Prospect Code</div>
                                <div class="detail-val">{{ $dash(optional($prospect)->code) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Created At</div>
                                <div class="detail-val">{{ $enrolment->created_at ? $enrolment->created_at->format('d M Y H:i') : '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Notes</div>
                                <div class="detail-val">{{ $dash($enrolment->noted) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-card card">
                    <div class="card-header"><i class="fa fa-lightbulb-o me-1"></i> Enrolment Notes</div>
                    <div class="card-body">
                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-key">Information From</div>
                                <div class="detail-val">{{ $enrolment->info_from ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Info Message</div>
                                <div class="detail-val">{{ $enrolment->info_from_message ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Reason for Enrolment</div>
                                <div class="detail-val">{{ $enrolment->reason_for_enrolment ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Preferred Program</div>
                                <div class="detail-val">{{ $enrolment->preferred_program ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Expected Impact</div>
                                <div class="detail-val">{{ $enrolment->expectation_mhis_impact ?? '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Already Visited School</div>
                                <div class="detail-val">{{ $yesNo($enrolment->already_visit) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Open Day Visited</div>
                                <div class="detail-val">{{ $yesNo($enrolment->open_day_visited) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Knowledge About Program</div>
                                <div class="detail-val">{{ $dash($enrolment->knowledge_about_program) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Trust Reason</div>
                                <div class="detail-val">{{ $dash($enrolment->trust_reason) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Recommender Name</div>
                                <div class="detail-val">{{ $dash($enrolment->recommender_name) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Recommender Phone</div>
                                <div class="detail-val">{{ $dash($enrolment->recommender_phone) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Recommender Child</div>
                                <div class="detail-val">{{ $dash($enrolment->recommender_child_name) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Recommender Child Class</div>
                                <div class="detail-val">{{ $dash($enrolment->recommender_child_class) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="detail-card card">
                    <div class="card-header"><i class="fa fa-credit-card me-1"></i> Payment Summary</div>
                    <div class="card-body">
                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-key">Registration Fee</div>
                                <div class="detail-val">{{ $money($enrolment->registration_fee) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">DP / Other Payment</div>
                                <div class="detail-val">{{ $money($enrolment->custom_payment) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Discount</div>
                                <div class="detail-val">{{ $money($enrolment->discount) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Bank Charge</div>
                                <div class="detail-val">{{ $money($enrolment->bank_charger) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Total Amount</div>
                                <div class="detail-val">{{ $money($enrolment->amount_paid) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Invoice ID</div>
                                <div class="detail-val">{{ $dash($enrolment->invoice_id) }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Payment Date</div>
                                <div class="detail-val">{{ $enrolment->payment_date ? $enrolment->paymentDateFormatted() : '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Virtual Account Created</div>
                                <div class="detail-val">{{ $enrolment->create_va_date ? \Carbon\Carbon::parse($enrolment->create_va_date)->format('d M Y H:i') : '-' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-key">Virtual Account Expiry</div>
                                <div class="detail-val">{{ $enrolment->expiry_va_date ? \Carbon\Carbon::parse($enrolment->expiry_va_date)->format('d M Y H:i') : '-' }}</div>
                            </div>
                        </div>
                        @if (!$hasTransactions)
                            <div class="small text-muted mt-3">Legacy enrolment: no itemised payment transactions are recorded; amounts come from the enrolment record.</div>
                        @endif
                        @if (!empty($enrolment->payment_url))
                            <a href="{{ $enrolment->payment_url }}" target="_blank" class="btn btn-success btn-sm mt-3">
                                <i class="fa fa-external-link"></i> Open Payment Link
                            </a>
                        @endif
                    </div>
                </div>

                @if ($enrolment->transactions->isNotEmpty())
                    <div class="detail-card card">
                        <div class="card-header"><i class="fa fa-list me-1"></i> Payment Transactions</div>
                        <div class="card-body">
                            @foreach ($enrolment->transactions->sortByDesc('created_at') as $transaction)
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between gap-2">
                                        <strong>{{ $transaction->invoice_id ?? $transaction->code }}</strong>
                                        <span class="badge {{ strtoupper($transaction->payment_status) === 'PAID' ? 'text-bg-success' : 'text-bg-warning' }}">
                                            {{ strtoupper($transaction->payment_status) }}
                                        </span>
                                    </div>
                                    <div class="small text-muted">
                                        {{ $transaction->payment_place ?? '-' }} · {{ ucfirst($transaction->source ?? '-') }}
                                        · {{ $transaction->created_at ? $transaction->created_at->format('d M Y H:i') : '-' }}
                                    </div>
                                    @foreach ($transaction->details as $detail)
                                        <div class="d-flex justify-content-between mt-2">
                                            <span>{{ $detail->description ?? ucfirst($detail->type) }}</span>
                                            <strong>Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</strong>
                                        </div>
                                    @endforeach
                                    <div class="d-flex justify-content-between mt-2 small text-muted">
                                        <span>Subtotal</span><span>{{ $money($transaction->subtotal) }}</span>
                                    </div>
                                    @if ((float) $transaction->discount > 0)
                                        <div class="d-flex justify-content-between small text-success">
                                            <span>Discount</span><span>- {{ $money($transaction->discount) }}</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Bank Charge</span><span>{{ $money($transaction->bank_charge) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 fw-bold">
                                        <span>Transaction Total</span>
                                        <span>Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($transaction->payment_date)
                                        <div class="small text-muted mt-1">Paid: {{ $transaction->payment_date->format('d M Y H:i') }}</div>
                                    @endif
                                    @if ($transaction->expiry_va_date)
                                        <div class="small text-muted">Expires: {{ $transaction->expiry_va_date->format('d M Y H:i') }}</div>
                                    @endif
                                    @if ($transaction->noted)
                                        <div class="small text-muted">{{ $transaction->noted }}</div>
                                    @endif
                                    @if ($transaction->payment_url && strtoupper($transaction->payment_status) !== 'PAID')
                                        <a href="{{ $transaction->payment_url }}" target="_blank" class="btn btn-outline-success btn-sm mt-2"><i class="fa fa-external-link"></i> Payment Link</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="detail-card card">
                    <div class="card-header"><i class="fa fa-history me-1"></i> Enrolment History</div>
                    <div class="card-body">
                        @if ($activities->count())
                            <ul class="activity-timeline">
                                @foreach ($activities as $activity)
                                    <li class="activity-item">
                                        <div class="activity-date">
                                            {{ $activity->created_at ? $activity->created_at->format('d M Y H:i') : '-' }}
                                        </div>
                                        <p class="activity-note">{{ $activity->note ?? '-' }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="empty-activity">
                                <i class="fa fa-inbox mb-2"></i>
                                <div>No prospect activity has been recorded yet.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
