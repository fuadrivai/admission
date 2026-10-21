@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-lg-4">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="mb-1">Data Historis Enrolment</h5>
                        <p class="text-muted small mb-0">Simpan angka laporan lama tanpa membuat data enrolment atau transaksi buatan.</p>
                    </div>
                    <a href="{{ route('enrolment.historical-report.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i> Tambah Snapshot
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @endif

                <div class="alert alert-info small">
                    Masukkan angka sesuai data yang benar-benar tersedia. Pilih cakupan Level bila angka hanya tersedia
                    sebagai agregat level, atau Grade bila data lama memang dirinci per grade. Kedua cakupan tidak boleh
                    dicampur pada satu level, dan nilai total enrolment/DP serta persentase target dihitung otomatis.
                </div>

                <form method="GET" action="{{ route('enrolment.historical-report.index') }}"
                    class="row g-3 align-items-end p-3 mb-4 border rounded bg-light">
                    <div class="col-md-5">
                        <label for="filter-academic-year" class="form-label">Academic Year</label>
                        <select id="filter-academic-year" name="academic_year_id" class="form-select" required>
                            <option value="">Pilih Academic Year</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}"
                                    {{ (string) $selectedAcademicYearId === (string) $academicYear->id ? 'selected' : '' }}>
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
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
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('enrolment.historical-report.index') }}" class="btn btn-light border">Reset</a>
                    </div>
                </form>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Level</th>
                                <th>Cakupan</th>
                                <th class="text-end">Active</th>
                                <th class="text-end">Cancel</th>
                                <th class="text-end">Enrolment Expired</th>
                                <th class="text-end">Enrolment Pending</th>
                                <th class="text-end">Enrolment Paid</th>
                                <th class="text-end">DP Expired</th>
                                <th class="text-end">DP Pending</th>
                                <th class="text-end">DP Paid</th>
                                <th class="text-end">Target</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($snapshots as $snapshot)
                                <tr>
                                    <td>{{ $snapshot->level->name }}</td>
                                    <td>{{ $snapshot->scope_type === 'level' ? 'Level' : 'Grade: ' . optional($snapshot->grade)->name }}</td>
                                    @foreach (['active_children', 'cancelled_children', 'enrolment_expired', 'enrolment_pending', 'enrolment_paid', 'dp_expired', 'dp_pending', 'dp_paid', 'target'] as $metric)
                                        <td class="text-end">{{ number_format($snapshot->{$metric}) }}</td>
                                    @endforeach
                                    <td class="text-center text-nowrap">
                                        <a href="{{ route('enrolment.historical-report.edit', $snapshot) }}"
                                            class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('enrolment.historical-report.destroy', $snapshot) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Hapus snapshot ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        @if ($hasFilters)
                                            Belum ada snapshot untuk filter yang dipilih.
                                        @else
                                            Pilih Academic Year dan Cabang untuk melihat snapshot.
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
