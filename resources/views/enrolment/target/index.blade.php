@extends('main-layout.index')

@section('content-style')
    <style>
        .target-table thead th {
            background: #f3f6fb;
            color: #475467;
            font-size: .78rem;
            letter-spacing: .03em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .target-table td,
        .target-table th {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-lg-4">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="mb-1">Enrolment Target</h5>
                        <p class="text-muted small mb-0">Daftar target siswa per tahun ajaran, cabang, level, dan grade.</p>
                    </div>
                    <a href="{{ route('enrolment.target.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i> Tambah Target
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @endif

                <form method="GET" action="{{ route('enrolment.target.index') }}"
                    class="row g-3 align-items-end p-3 mb-4 border rounded bg-light">
                    <div class="col-lg-3 col-md-6">
                        <label for="filter-academic-year" class="form-label">Academic Year</label>
                        <select id="filter-academic-year" name="academic_year_id" class="form-select" required>
                            <option value="">Pilih Academic Year</option>
                            <option value="all" {{ $selectedAcademicYearId === 'all' ? 'selected' : '' }}>All Academic Years</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}"
                                    {{ (string) $selectedAcademicYearId === (string) $academicYear->id ? 'selected' : '' }}>
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="filter-branch" class="form-label">Cabang</label>
                        <select id="filter-branch" name="branch_id" class="form-select" required>
                            <option value="">Pilih Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ (string) $selectedBranchId === (string) $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="filter-level" class="form-label">Level</label>
                        <select id="filter-level" name="level_id" class="form-select"
                            {{ $selectedBranchId ? '' : 'disabled' }}>
                            <option value="">Semua Level</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" data-branch-id="{{ $level->branch_id }}"
                                    {{ (string) $selectedLevelId === (string) $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('enrolment.target.index') }}" class="btn btn-light border">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover target-table mb-0">
                        <thead>
                            <tr>
                                <th>Academic Year</th>
                                <th>Cabang</th>
                                <th>Level</th>
                                <th>Cakupan</th>
                                <th>Grade</th>
                                <th class="text-end">Target Siswa</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($targets as $target)
                                <tr>
                                    <td>{{ $target->academicYear->name }}</td>
                                    <td>{{ $target->branch->name }}</td>
                                    <td>{{ $target->level->name }}</td>
                                    <td>{{ $target->target_scope === 'level' ? 'Level' : 'Grade' }}</td>
                                    <td>{{ $target->target_scope === 'level' ? 'Semua grade' : optional($target->grade)->name }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($target->target) }}</td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ route('enrolment.target.edit', $target) }}"
                                            class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('enrolment.target.destroy', $target) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Hapus target ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        @if ($hasTargetFilters)
                                            Belum ada target enrolment untuk filter yang dipilih.
                                        @else
                                            Pilih Academic Year dan Cabang, lalu klik Filter untuk menampilkan target.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const branchSelect = document.getElementById('filter-branch');
            const levelSelect = document.getElementById('filter-level');

            function filterLevels(branchId, selectedLevelId) {
                levelSelect.disabled = !branchId;
                Array.from(levelSelect.options).forEach(function(option, index) {
                    option.hidden = index !== 0 && (!branchId || option.dataset.branchId !== String(branchId));
                });

                const selectedOption = Array.from(levelSelect.options).find(function(option) {
                    return option.value === String(selectedLevelId) && !option.hidden;
                });
                levelSelect.value = selectedOption ? selectedOption.value : '';
            }

            branchSelect.addEventListener('change', function() {
                filterLevels(this.value, '');
            });

            filterLevels(branchSelect.value, @json((string) $selectedLevelId));
        });
    </script>
@endsection
