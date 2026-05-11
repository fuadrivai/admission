@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet"
        href="/assets/static/css/dashboard.css?v={{ filemtime(public_path('assets/static/css/dashboard.css')) }}">
@endsection

@section('content-child')
    <div class="db-wrapper">
        <div class="db-header">
            <div class="db-header-title">
                <p>Track school visits, enrolments, and student activity in real time.</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="db-branch-filter">
                    <select id="branchFilter" class="form-select form-select-sm"
                        onchange="window.location.href = this.value ? '{{ request()->url() }}?branch_id=' + this.value : '{{ request()->url() }}'">
                        <option value="" {{ is_null($selectedBranch) ? 'selected' : '' }}>All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ (string) $selectedBranch === (string) $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="db-date-badge">
                    <i class="bi bi-calendar3"></i>
                    <span id="liveDate"></span>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="kpi-card c-primary card h-100">
                    <div class="card-body">
                        <div class="kpi-icon ic-primary"><i class="bi bi-building-fill-check"></i></div>
                        <div class="kpi-label">School Visits Today</div>
                        <div class="kpi-value">{{ number_format($schoolVisitsToday) }}</div>
                        <span class="kpi-badge up mt-2"><i class="bi bi-arrow-up-short"></i>
                            {{ number_format($schoolVisitsMonth) }} visits this month</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="kpi-card c-success card h-100">
                    <div class="card-body">
                        <div class="kpi-icon ic-success"><i class="bi bi-journal-check"></i></div>
                        <div class="kpi-label">Paid Enrolments</div>
                        <div class="kpi-value">{{ number_format($paidEnrolment) }}</div>
                        <span class="kpi-badge up mt-2"><i class="bi bi-arrow-up-short"></i>
                            {{ number_format($enrolmentsMonth) }} enrolments this month</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="kpi-card c-warning card h-100">
                    <div class="card-body">
                        <div class="kpi-icon ic-warning"><i class="bi bi-people-fill"></i></div>
                        <div class="kpi-label">Student Documents</div>
                        <div class="kpi-value">{{ number_format($documentComplete) }}</div>
                        <span class="kpi-badge neu mt-2"><i class="bi bi-calendar-week"></i>
                            {{ number_format($studentDocumentsMonth) }} submitted this month</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="kpi-card c-info card h-100">
                    <div class="card-body">
                        <div class="kpi-icon ic-info"><i class="bi bi-hourglass-split"></i></div>
                        <div class="kpi-label">Observation Today</div>
                        <div class="kpi-value">{{ number_format($observationsToday) }}</div>
                        <span class="kpi-badge down mt-2"><i class="bi bi-exclamation-circle"></i>
                            {{ number_format($observationsMonth) }} this month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">

            <div class="col-xl-8">
                <div class="db-card">
                    <div class="db-card-header">
                        <div>
                            <p class="db-card-title">School Visit Weekly Trend</p>
                            <p class="db-card-sub">
                                @php
                                    $twStart = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
                                    $twEnd = $twStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                                    $lwStart = $twStart->copy()->subWeek();
                                    $lwEnd = $lwStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                                @endphp
                                <span class="fw-semibold text-primary">This week</span>
                                {{ $twStart->format('d M') }} – {{ $twEnd->format('d M Y') }}
                                &nbsp;vs&nbsp;
                                <span class="fw-semibold" style="color:#a5b4fc;">Last week</span>
                                {{ $lwStart->format('d M') }} – {{ $lwEnd->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="db-card-body">
                        <div class="chart-wrap" style="height:240px;">
                            <canvas id="trendChart" data-this-week="@json($thisWeekVisits)"
                                data-last-week="@json($lastWeekVisits)"></canvas>
                        </div>
                        <div class="chart-legend">
                            <div class="chart-legend-item">
                                <div class="legend-dot" style="background:#4361ee;"></div> This Week
                            </div>
                            <div class="chart-legend-item">
                                <div class="legend-dot" style="background:#cbd5fb; border:1.5px dashed #4361ee;"></div> Last
                                Week
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Calendar --}}
            <div class="col-xl-4">
                <div class="db-card">
                    <div class="db-card-header">
                        <div>
                            <p class="db-card-title">Schedule</p>
                            <p class="db-card-sub">Google Calendar schedule</p>
                        </div>
                    </div>
                    <div class="db-card-body">
                        <iframe class="schedule-embed"
                            src="https://calendar.google.com/calendar/embed?src=c_s5q1v17u6t1o005crn4ac8kj0k%40group.calendar.google.com&ctz=Asia%2FJakarta"
                            style="border:0; width:100%;" width="800" height="600" frameborder="0" scrolling="no"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Calendar">
                        </iframe>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══ Row: Enrolment Weekly Trend ══════════════════════════════ --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <div>
                            <p class="db-card-title">Enrolment Weekly Trend</p>
                            <p class="db-card-sub">
                                @php
                                    $ewStart = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
                                    $ewEnd = $ewStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                                    $elwStart = $ewStart->copy()->subWeek();
                                    $elwEnd = $elwStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                                @endphp
                                <span class="fw-semibold text-success">This week</span>
                                {{ $ewStart->format('d M') }} – {{ $ewEnd->format('d M Y') }}
                                &nbsp;vs&nbsp;
                                <span class="fw-semibold" style="color:#86efac;">Last week</span>
                                {{ $elwStart->format('d M') }} – {{ $elwEnd->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="db-card-body">
                        <div class="chart-wrap" style="height:220px;">
                            <canvas id="enrolChart" data-this-week="@json($thisWeekEnrolments)"
                                data-last-week="@json($lastWeekEnrolments)"></canvas>
                        </div>
                        <div class="chart-legend">
                            <div class="chart-legend-item">
                                <div class="legend-dot" style="background:#2cb67d;"></div> This Week
                            </div>
                            <div class="chart-legend-item">
                                <div class="legend-dot" style="background:#86efac; border:1.5px dashed #2cb67d;"></div>
                                Last Week
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ Row 3: Grade Chart + Activity + Events ══════════════════ --}}
        <div class="row g-4">

            {{-- Level Distribution Bar Chart --}}
            <div class="col-xl-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <div>
                            <p class="db-card-title">Level Distribution</p>
                            <p class="db-card-sub">Visits vs all enrolments per level this month</p>
                        </div>
                    </div>
                    <div class="db-card-body">
                        <div class="chart-wrap" style="height:260px;">
                            <canvas id="levelChart" data-labels='@json($levelLabels)'
                                data-visits='@json($levelVisits)'
                                data-enrolments='@json($levelEnrolments)'></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Level Progress List --}}
            <div class="col-xl-6 col-md-6">
                <div class="db-card">
                    <div class="db-card-header">
                        <div>
                            <p class="db-card-title">Paid Enrolment by Level</p>
                            <p class="db-card-sub">PAID enrolments per level this month in this branch</p>
                        </div>
                    </div>
                    <div class="db-card-body">
                        @php
                            $levelStats = collect($levelLabels ?? [])->map(
                                fn($label, $index) => [$label, $levelPaidEnrolments[$index] ?? 0],
                            );
                            $levelMax = max(1, collect($levelPaidEnrolments ?? [])->max() ?? 0);
                        @endphp
                        @forelse ($levelStats as [$name, $val])
                            <div class="grade-row">
                                <span class="grade-label">{{ $name }}</span>
                                <div class="grade-bar-wrap">
                                    <div class="grade-bar" style="width:{{ round(($val / $levelMax) * 100) }}%;"></div>
                                </div>
                                <span class="grade-count">{{ $val }}</span>
                            </div>
                        @empty
                            <div class="text-muted">No level enrolment data for this branch.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Level Principal Info --}}
            <div class="col-xl-6 col-md-6">
                <div class="db-card">
                    <div class="db-card-header">
                        <div>
                            <p class="db-card-title">Level Principals</p>
                            <p class="db-card-sub">Principal contact per level in this branch</p>
                        </div>
                    </div>
                    <div class="db-card-body">
                        @forelse ($levels as $level)
                            <div class="activity-item">
                                @php
                                    $div = strtolower($level->division->name ?? '');
                                    $avatarBg = '#6c757d';
                                    $avatarTextColor = '#ffffff';
                                    if (str_contains($div, 'preschool')) {
                                        $avatarBg = '#ffc107';
                                        $avatarTextColor = '#212529';
                                    } elseif (str_contains($div, 'primary')) {
                                        $avatarBg = '#198754';
                                    } elseif (str_contains($div, 'secondary')) {
                                        $avatarBg = '#0dcaf0';
                                        $avatarTextColor = '#212529';
                                    } elseif (str_contains($div, 'development')) {
                                        $avatarBg = '#6f42c1';
                                    }
                                @endphp
                                <div class="activity-avatar"
                                    style="background:{{ $avatarBg }}; color:{{ $avatarTextColor }};">
                                    {{ strtoupper(substr($level->name, 0, 2)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="activity-text">
                                        <strong>{{ $level->name }}</strong>
                                        @if ($level->principal)
                                            &mdash; {{ $level->principal }}
                                        @endif
                                    </div>
                                    @if ($level->email)
                                        <div class="activity-time mt-1">
                                            <i class="bi bi-envelope me-1"></i>{{ $level->email }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No levels found for this branch.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- ══ Row: Recent Activity ══════════════════════════════════ --}}
        <div class="row g-4 mt-2">

            {{-- Activity Feed --}}
            <div class="col-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <div>
                            <p class="db-card-title">Recent Activity</p>
                            <p class="db-card-sub">Latest prospect activities</p>
                        </div>
                    </div>
                    <div class="db-card-body">
                        @php
                            $avatarColors = [
                                'var(--primary)',
                                'var(--success)',
                                'var(--warning)',
                                'var(--purple)',
                                'var(--info)',
                                'var(--danger)',
                            ];
                            $activityTypeLabel = [
                                \App\Models\SchoolVisit::class => 'School Visit',
                                \App\Models\Enrolment::class => 'Enrolment',
                                \App\Models\Observation::class => 'Observation',
                            ];
                        @endphp
                        @forelse ($recentActivities as $i => $activity)
                            @php
                                $prospect = $activity->prospect;
                                $initials = $prospect
                                    ? strtoupper(
                                        substr($prospect->child_name, 0, 1) . substr($prospect->parent_name, 0, 1),
                                    )
                                    : '?';
                                $color = $avatarColors[$i % count($avatarColors)];
                                $typeLabel = $activityTypeLabel[$activity->activityable_type] ?? 'Activity';
                            @endphp
                            <div class="activity-item">
                                <div class="activity-avatar" style="background:{{ $color }};">{{ $initials }}
                                </div>
                                <div class="flex-1">
                                    <div class="activity-text">
                                        <strong>{{ $prospect->child_name ?? 'Unknown' }}</strong>
                                        — {{ $typeLabel }}
                                        @if ($activity->note)
                                            : {{ $activity->note }}
                                        @endif
                                    </div>
                                    <div class="activity-time mt-1">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No recent activity found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('content-script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script
        src="/assets/static/js/pages/dashboard.js?v={{ filemtime(public_path('assets/static/js/pages/dashboard.js')) }}">
    </script>
@endsection
