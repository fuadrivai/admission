@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-lg-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h5 class="mb-1">{{ $snapshot->exists ? 'Edit Snapshot Historis' : 'Tambah Snapshot Historis' }}</h5>
                        <p class="text-muted small mb-0">Snapshot hanya menyimpan angka agregat; tidak membuat enrolment/transaksi.</p>
                    </div>
                    <a href="{{ route('enrolment.historical-report.index') }}" class="btn btn-light border">Kembali</a>
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

                <form method="POST" action="{{ $snapshot->exists
                    ? route('enrolment.historical-report.update', $snapshot)
                    : route('enrolment.historical-report.store') }}">
                    @csrf
                    @if ($snapshot->exists)
                        @method('PUT')
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="academic_year_id" class="form-label">Academic Year</label>
                            <select id="academic_year_id" name="academic_year_id" class="form-select" required>
                                <option value="">Pilih Academic Year</option>
                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}"
                                        {{ (string) old('academic_year_id', $snapshot->academic_year_id) === (string) $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="branch_id" class="form-label">Cabang</label>
                            <select id="branch_id" name="branch_id" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ (string) old('branch_id', $snapshot->branch_id) === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="level_id" class="form-label">Level</label>
                            <select id="level_id" name="level_id" class="form-select" required>
                                <option value="">Pilih Level</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" data-branch-id="{{ $level->branch_id }}"
                                        {{ (string) old('level_id', $snapshot->level_id) === (string) $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="scope_type" class="form-label">Cakupan Data</label>
                            <select id="scope_type" name="scope_type" class="form-select" required>
                                <option value="level" {{ old('scope_type', $snapshot->scope_type) === 'level' ? 'selected' : '' }}>Agregat Level</option>
                                <option value="grade" {{ old('scope_type', $snapshot->scope_type) === 'grade' ? 'selected' : '' }}>Per Grade</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="grade-field">
                            <label for="grade_id" class="form-label">Grade</label>
                            <select id="grade_id" name="grade_id" class="form-select">
                                <option value="">Pilih Grade</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}" data-level-id="{{ $grade->level_id }}"
                                        {{ (string) old('grade_id', $snapshot->grade_id) === (string) $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="mb-3">Angka Historis</h6>
                    <div class="row g-3">
                        @foreach ([
                            'active_children' => 'Status Anak - Active',
                            'cancelled_children' => 'Status Anak - Cancel',
                            'enrolment_expired' => 'Enrolment - Expired',
                            'enrolment_pending' => 'Enrolment - Pending',
                            'enrolment_paid' => 'Enrolment - Paid',
                            'dp_expired' => 'DP - Expired',
                            'dp_pending' => 'DP - Pending',
                            'dp_paid' => 'DP - Paid',
                            'target' => 'Target Siswa',
                        ] as $field => $label)
                            <div class="col-lg-4 col-md-6">
                                <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                <input id="{{ $field }}" name="{{ $field }}" type="number" class="form-control"
                                    min="0" step="1" required
                                    value="{{ old($field, $snapshot->{$field} ?? 0) }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('enrolment.historical-report.index') }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Snapshot</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const branch = document.getElementById('branch_id');
            const level = document.getElementById('level_id');
            const scope = document.getElementById('scope_type');
            const grade = document.getElementById('grade_id');
            const gradeField = document.getElementById('grade-field');

            function updateLevels() {
                Array.from(level.options).forEach(function(option, index) {
                    option.hidden = index !== 0 && option.dataset.branchId !== branch.value;
                });
                if (level.selectedOptions.length && level.selectedOptions[0].hidden) {
                    level.value = '';
                }
                updateGrades();
            }

            function updateGrades() {
                Array.from(grade.options).forEach(function(option, index) {
                    option.hidden = index !== 0 && option.dataset.levelId !== level.value;
                });
                if (grade.selectedOptions.length && grade.selectedOptions[0].hidden) {
                    grade.value = '';
                }
                gradeField.hidden = scope.value !== 'grade';
                grade.required = scope.value === 'grade';
            }

            branch.addEventListener('change', updateLevels);
            level.addEventListener('change', updateGrades);
            scope.addEventListener('change', updateGrades);
            updateLevels();
            level.value = @json((string) old('level_id', $snapshot->level_id));
            updateGrades();
            grade.value = @json((string) old('grade_id', $snapshot->grade_id));
        });
    </script>
@endsection
