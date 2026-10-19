@extends('main-layout.index')

@section('content-style')
    <style>
        .target-form-card {
            border: 1px solid #e8edf3;
            border-radius: 12px;
            background: #f8faff;
        }

        .target-table thead th {
            background: #f3f6fb;
            color: #475467;
            font-size: .78rem;
            letter-spacing: .03em;
            text-transform: uppercase;
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
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="mb-1">{{ $editingTarget ? 'Edit Enrolment Target' : 'Tambah Enrolment Target' }}</h5>
                        <p class="text-muted small mb-0">Tentukan target pada level secara keseluruhan atau pada setiap grade.</p>
                    </div>
                    <a href="{{ route('enrolment.target.index') }}" class="btn btn-light border">Kembali</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($scopeError)
                    <div class="alert alert-warning" role="alert">{{ $scopeError }}</div>
                @endif

                @if ($editingTarget)
                    <div class="target-form-card p-3 mb-4">
                        <div class="row g-3">
                            <div class="col-md-3"><span class="text-muted">Academic Year</span><br><strong>{{ $editingTarget->academicYear->name }}</strong></div>
                            <div class="col-md-3"><span class="text-muted">Cabang</span><br><strong>{{ $editingTarget->branch->name }}</strong></div>
                            <div class="col-md-3"><span class="text-muted">Level</span><br><strong>{{ $editingTarget->level->name }}</strong></div>
                            <div class="col-md-3"><span class="text-muted">Cakupan</span><br><strong>{{ $editingTarget->target_scope === 'level' ? 'Level' : 'Grade: ' . optional($editingTarget->grade)->name }}</strong></div>
                        </div>
                    </div>
                @else
                    <form method="GET" action="{{ route('enrolment.target.create') }}"
                        class="target-form-card row g-3 align-items-end p-3 mb-4">
                        <div class="col-md-4">
                            <label for="academic_year_id" class="form-label">Academic Year</label>
                            <select id="academic_year_id" name="academic_year_id" class="form-select" required>
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}"
                                        {{ (string) $selectedAcademicYearId === (string) $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="branch_id" class="form-label">Cabang</label>
                            <select id="branch_id" name="branch_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ (string) $selectedBranchId === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="level_id" class="form-label">Level</label>
                            <select id="level_id" name="level_id" class="form-select"
                                {{ $selectedBranchId ? '' : 'disabled' }} required>
                                <option value="">Pilih Level</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" data-branch-id="{{ $level->branch_id }}"
                                        {{ (string) $selectedLevelId === (string) $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="target_scope" class="form-label">Cakupan Target</label>
                            <select id="target_scope" name="target_scope" class="form-select"
                                {{ $selectedLevelId ? '' : 'disabled' }} required>
                                <option value="level" {{ $selectedTargetScope === 'level' ? 'selected' : '' }}>Target Level (gabungan seluruh grade)</option>
                                <option value="grade" {{ $selectedTargetScope === 'grade' ? 'selected' : '' }}>Target per Grade</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-list me-1"></i> Tampilkan Target
                            </button>
                        </div>
                    </form>
                @endif

                @if ($editingTarget || ($selectedAcademicYearId && $selectedBranchId && $selectedLevelId && !$scopeError))
                    @if ($editingTarget)
                        <form action="{{ route('enrolment.target.update', $editingTarget) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="table-responsive">
                                <table class="table table-bordered target-table">
                                    <thead>
                                        <tr>
                                            <th>{{ $editingTarget->target_scope === 'level' ? 'Level Target' : 'Grade' }}</th>
                                            <th style="width: 240px">Target Siswa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($editingTarget->target_scope === 'level')
                                            <tr>
                                                <td>{{ $editingTarget->level->name }} (semua grade)</td>
                                                <td>
                                                    <input type="number" name="target" class="form-control"
                                                        min="0" step="1" required
                                                        value="{{ old('target', $editingTarget->target) }}">
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($grades as $grade)
                                                <tr>
                                                    <td>{{ $grade->name }}</td>
                                                    <td>
                                                        <input type="number" name="target" class="form-control"
                                                            min="0" step="1" required
                                                            value="{{ old('target', $editingTarget->target) }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('enrolment.target.index') }}" class="btn btn-light border">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('enrolment.target.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $selectedAcademicYearId }}">
                            <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                            <input type="hidden" name="level_id" value="{{ $selectedLevelId }}">
                            <input type="hidden" name="target_scope" value="{{ $selectedTargetScope }}">

                            @if ($selectedTargetScope === 'level')
                                <div class="row justify-content-center mb-3">
                                    <div class="col-md-6">
                                        <label for="level-target" class="form-label">Target Siswa untuk seluruh grade di {{ $selectedLevelName }}</label>
                                        <input type="number" id="level-target" name="target" class="form-control"
                                            min="0" step="1" required value="{{ old('target', $existingTargets->first() ?? '') }}">
                                    </div>
                                </div>
                            @else
                            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
                                <div>
                                    <h6 class="mb-1">Target per Grade</h6>
                                    <p class="text-muted small mb-0">Isi satu atau beberapa target. Grade yang dikosongkan tidak diubah.</p>
                                </div>
                                <span class="badge bg-light text-dark border">
                                    {{ $academicYears->firstWhere('id', (int) $selectedAcademicYearId)->name ?? '' }}
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover target-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 70px" class="text-center">No</th>
                                            <th>Grade</th>
                                            <th style="width: 240px">Target Siswa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($grades as $grade)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $grade->name }}</td>
                                                <td>
                                                    <input type="number" name="grade_targets[{{ $grade->id }}]"
                                                        class="form-control" min="0" step="1"
                                                        value="{{ old('grade_targets.' . $grade->id, $existingTargets[$grade->id] ?? '') }}"
                                                        placeholder="Kosongkan jika tidak diubah">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('enrolment.target.index') }}" class="btn btn-light border">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Simpan Target
                                </button>
                            </div>
                        </form>
                    @endif
                @elseif (!$editingTarget && $selectedAcademicYearId && $selectedBranchId && $selectedLevelId && $selectedTargetScope === 'grade' && $grades->isEmpty())
                    <div class="alert alert-warning mb-0">Level ini belum memiliki data grade.</div>
                @elseif (!$editingTarget)
                    <div class="text-center text-muted py-4">Pilih Tahun Ajaran, Cabang, dan Level, lalu klik Tampilkan Grade.</div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    @unless ($editingTarget)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const branchSelect = document.getElementById('branch_id');
                const levelSelect = document.getElementById('level_id');
                const scopeSelect = document.getElementById('target_scope');
                const selectedLevelId = @json((string) $selectedLevelId);

                function filterLevels(branchId, preferredLevelId) {
                    Array.from(levelSelect.options).forEach(function(option, index) {
                        if (index === 0) {
                            option.hidden = false;
                            return;
                        }
                        option.hidden = option.dataset.branchId !== String(branchId);
                    });

                    levelSelect.disabled = !branchId;
                    const preferredOption = Array.from(levelSelect.options).find(function(option) {
                        return option.value === String(preferredLevelId) && !option.hidden;
                    });
                    levelSelect.value = preferredOption ? preferredOption.value : '';
                }

                branchSelect.addEventListener('change', function() {
                    filterLevels(this.value, '');
                    scopeSelect.disabled = true;
                });

                levelSelect.addEventListener('change', function() {
                    scopeSelect.disabled = !this.value;
                });

                filterLevels(branchSelect.value, selectedLevelId);
                scopeSelect.disabled = !levelSelect.value;
            });
        </script>
    @endunless
@endsection
