@extends('main-layout.index')

@section('content-style')
    <style>
        .enrolment-report .filter-card {
            border: 1px solid #e8edf3;
            border-radius: 14px;
            background: linear-gradient(135deg, #fff 0%, #f8faff 100%);
        }

        .enrolment-report .filter-card .form-label {
            color: #475467;
            font-size: .82rem;
            font-weight: 600;
        }

        .enrolment-report .report-table {
            border-color: #e8edf3;
        }

        .enrolment-report .report-table thead th {
            padding: .9rem 1rem;
            border-bottom: 0;
            background: #f3f6fb;
            color: #475467;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .enrolment-report .report-table tbody td,
        .enrolment-report .report-table tfoot td {
            padding: .85rem 1rem;
            border-color: #edf0f4;
        }

        .enrolment-report .report-table tbody tr:hover {
            background-color: #f8faff;
        }

        .enrolment-report .report-table .level-name {
            color: #344054;
            font-weight: 600;
        }

        .enrolment-report .report-table .badge {
            min-width: 2rem;
            padding: .45rem .65rem;
            border-radius: 999px;
            font-size: .8rem;
        }

        .enrolment-report .report-table tfoot td {
            background: #f3f6fb;
            color: #344054;
        }
    </style>
@endsection

@section('content-child')
    <section class="section enrolment-report">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-lg-4">
                <div class="mb-4">
                    <h5 class="mb-1">Laporan Enrolment</h5>
                    <p class="text-muted small mb-0">Pilih cabang dan tahun ajaran untuk melihat laporan. Level bersifat
                        opsional.</p>
                </div>

                <form id="enrolment-report-filters" method="GET" action="{{ route('enrolment.report') }}"
                    class="filter-card row g-3 align-items-end p-3 mb-4">
                    <div class="col-md-3">
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select id="academic_year_id" name="academic_year_id" class="form-select" required>
                            <option value="" disabled {{ $selectedAcademicYearId === '' ? 'selected' : '' }}>Pilih
                                Academic Year</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}"
                                    {{ (string) $selectedAcademicYearId === (string) $academicYear->id ? 'selected' : '' }}>
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id" class="form-label">Cabang</label>
                        @if (auth()->user()->role === 'user')
                            <select id="branch_id" class="form-select" disabled>
                                @foreach ($branches as $branch)
                                    @if ((string) $selectedBranchId === (string) $branch->id)
                                        <option value="{{ $branch->id }}" selected>{{ $branch->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                        @else
                            <select id="branch_id" name="branch_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ (string) $selectedBranchId === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="level_id" class="form-label">Level</label>
                        <select id="level_id" name="level_id" class="form-select"
                            {{ $selectedBranchId ? '' : 'disabled' }}>
                            <option value="">Semua Level</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" data-branch-id="{{ $level->branch_id }}"
                                    {{ (string) $selectedLevelId === (string) $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}{{ $level->division_name ? ' - ' . $level->division_name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-filter me-1"></i> Submit
                        </button>
                        <a href="{{ route('enrolment.report') }}" class="btn btn-light border px-3">Reset</a>
                    </div>
                </form>

                <div class="d-flex justify-content-center mb-4">
                    <button type="submit" form="enrolment-report-filters" name="export" value="excel"
                        class="btn btn-success">
                        <i class="fa fa-download me-1"></i> Export Excel
                    </button>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div id="report-pending-filter" class="alert alert-info {{ $hasReportFilter ? 'd-none' : '' }}"
                    role="status">
                    @if ($hasReportFilter)
                        Filter berubah. Klik <strong>Filter</strong> untuk menampilkan laporan sesuai pilihan terbaru.
                    @else
                        Pilih cabang dan tahun ajaran, lalu klik <strong>Filter</strong> untuk menampilkan laporan.
                    @endif
                </div>

                <div id="report-results" class="table-responsive {{ $hasReportFilter ? '' : 'd-none' }}">
                    <table class="table table-bordered align-middle report-table mb-0">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Level</th>
                                <th colspan="5" class="text-center">Enrolment Form</th>
                                <th rowspan="2" class="text-end">DP</th>
                                <th colspan="2" class="text-center">Target</th>
                            </tr>
                            <tr>
                                <th class="text-end">Expired</th>
                                <th class="text-end">Cancelled</th>
                                <th class="text-end">Pending</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Siswa</th>
                                <th class="text-end">% Target</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="level-name">{{ $row->level }}</td>
                                    <td class="text-end">
                                        @if ($row->expired != 0)
                                            <span class="badge text-bg-secondary">{{ $row->expired }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row->cancelled != 0)
                                            <span class="badge text-bg-danger">{{ $row->cancelled }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row->pending != 0)
                                            <span class="badge text-bg-warning">{{ $row->pending }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row->paid != 0)
                                            <span class="badge text-bg-success">{{ $row->paid }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row->total_form != 0)
                                            <span class="badge text-bg-primary">{{ $row->total_form }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row->seat_rsvp != 0)
                                            <span class="badge text-bg-info">{{ $row->seat_rsvp }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($row->enrolment_target != 0)
                                            <span
                                                class="badge text-bg-primary">{{ number_format($row->enrolment_target) }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    @php
                                        $targetPercentage =
                                            $row->enrolment_target > 0
                                                ? ($row->seat_rsvp / $row->enrolment_target) * 100
                                                : 0;
                                    @endphp
                                    <td class="text-end">
                                        @if ($targetPercentage > 0)
                                            <span
                                                class="badge text-bg-info">{{ number_format($targetPercentage, 1) }}%</span>
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
                                        @if ($hasReportFilter)
                                            Belum ada data grade.
                                        @else
                                            Pilih cabang dan tahun ajaran, lalu klik Filter untuk melihat laporan.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($hasReportFilter && $rows->isNotEmpty())
                            <tfoot class="fw-bold">
                                <tr>
                                    <td></td>
                                    <td>Total</td>
                                    <td class="text-end">
                                        @if ($totals['expired'] != 0)
                                            <span class="badge text-bg-secondary">{{ $totals['expired'] }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($totals['cancelled'] != 0)
                                            <span class="badge text-bg-danger">{{ $totals['cancelled'] }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($totals['pending'] != 0)
                                            <span class="badge text-bg-warning">{{ $totals['pending'] }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($totals['paid'] != 0)
                                            <span class="badge text-bg-success">{{ $totals['paid'] }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($totals['total_form'] != 0)
                                            <span class="badge text-bg-primary">{{ $totals['total_form'] }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($totals['seat_rsvp'] != 0)
                                            <span class="badge text-bg-info">{{ $totals['seat_rsvp'] }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($totals['enrolment_target'] != 0)
                                            <span
                                                class="badge text-bg-primary">{{ number_format($totals['enrolment_target']) }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    @php
                                        $totalTargetPercentage =
                                            $totals['enrolment_target'] > 0
                                                ? ($totals['seat_rsvp'] / $totals['enrolment_target']) * 100
                                                : 0;
                                    @endphp
                                    <td class="text-end">
                                        @if ($totalTargetPercentage > 0)
                                            <span
                                                class="badge text-bg-info">{{ number_format($totalTargetPercentage, 1) }}%</span>
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const branchSelect = document.getElementById('branch_id');
            const levelSelect = document.getElementById('level_id');
            const reportForm = document.getElementById('enrolment-report-filters');
            const reportResults = document.getElementById('report-results');
            const pendingFilterMessage = document.getElementById('report-pending-filter');

            function markReportAsPending() {
                if (reportResults && pendingFilterMessage) {
                    reportResults.classList.add('d-none');
                    pendingFilterMessage.classList.remove('d-none');
                }
            }

            if (!branchSelect || branchSelect.disabled) {
                if (levelSelect && branchSelect && branchSelect.value) {
                    filterLevels(branchSelect.value);
                }
                if (reportForm) {
                    reportForm.querySelectorAll('input, select').forEach(function(field) {
                        field.addEventListener('change', markReportAsPending);
                    });
                }
                return;
            }

            branchSelect.addEventListener('change', function() {
                filterLevels(this.value);
                markReportAsPending();
            });

            if (reportForm) {
                reportForm.querySelectorAll('input, select').forEach(function(field) {
                    if (field !== branchSelect) {
                        field.addEventListener('change', markReportAsPending);
                    }
                });
            }

            function filterLevels(branchId) {
                if (!levelSelect) {
                    return;
                }

                levelSelect.value = '';
                levelSelect.disabled = !branchId;

                Array.from(levelSelect.options).forEach(function(option) {
                    if (!option.dataset.branchId) {
                        option.hidden = false;
                        return;
                    }

                    option.hidden = option.dataset.branchId !== branchId;
                });
            }

            if (branchSelect.value) {
                filterLevels(branchSelect.value);
                levelSelect.value = @json((string) $selectedLevelId);
            }
        });
    </script>
@endsection
