@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Pengaturan Online Store</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('merchandise.store-settings.update') }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="store-name">Nama toko</label>
                        <input id="store-name" name="store_name" class="form-control" maxlength="150" required value="{{ old('store_name', $settings->store_name) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <input type="hidden" name="is_open" value="0">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="store-open" name="is_open" value="1" {{ old('is_open', $settings->is_open) ? 'checked' : '' }}>
                            <label class="form-check-label" for="store-open">Toko dibuka untuk umum</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="store-description">Deskripsi toko</label>
                        <textarea id="store-description" name="description" class="form-control" rows="4" maxlength="5000">{{ old('description', $settings->description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="closed-message">Pesan saat toko ditutup</label>
                        <textarea id="closed-message" name="closed_message" class="form-control" rows="2" maxlength="500">{{ old('closed_message', $settings->closed_message) }}</textarea>
                        <small class="text-muted">Ditampilkan pada halaman publik saat toko ditutup.</small>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Simpan Pengaturan</button>
                        <a class="btn btn-light-secondary ms-1" href="{{ route('merchandise.public-store') }}" target="_blank">Lihat Toko Publik</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
