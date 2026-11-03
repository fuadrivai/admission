@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Tambah Kategori</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('merchandise.categories.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="category-name">Nama kategori</label>
                            <input id="category-name" name="name" class="form-control" maxlength="100" required value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="category-description">Deskripsi</label>
                            <textarea id="category-description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <input type="hidden" name="is_store_visible" value="0">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="category-visible" name="is_store_visible" value="1" checked>
                            <label class="form-check-label" for="category-visible">Tampilkan di Online Store</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Simpan Kategori</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Daftar Kategori</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>Nama</th><th>Deskripsi</th><th>Produk</th><th>Status</th><th>Tampil</th><th>Urutan</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td colspan="7">
                                            <form method="POST" action="{{ route('merchandise.categories.update', $category->id) }}" class="row g-2 align-items-center">
                                                @csrf @method('PUT')
                                                <div class="col-md-2"><input name="name" class="form-control form-control-sm" value="{{ $category->name }}" required></div>
                                                <div class="col-md-2"><input name="description" class="form-control form-control-sm" value="{{ $category->description }}"></div>
                                                <div class="col-md-1 text-center">{{ $category->products_count }}</div>
                                                <div class="col-md-2">
                                                    <select name="is_active" class="form-select form-select-sm">
                                                        <option value="1" {{ $category->is_active ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ !$category->is_active ? 'selected' : '' }}>Nonaktif</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="hidden" name="is_store_visible" value="0">
                                                    <input class="form-check-input" type="checkbox" name="is_store_visible" value="1" aria-label="Tampilkan kategori di Online Store" {{ $category->is_store_visible ? 'checked' : '' }}>
                                                </div>
                                                <div class="col-md-1"><input name="display_order" type="number" min="0" class="form-control form-control-sm" value="{{ $category->display_order }}"></div>
                                                <div class="col-md-3 d-flex gap-1">
                                                    <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                                                    @if (!$category->products_count)
                                                        <button class="btn btn-sm btn-outline-danger" type="submit" form="delete-category-{{ $category->id }}">Hapus</button>
                                                    @endif
                                                </div>
                                            </form>
                                            @if (!$category->products_count)
                                                <form id="delete-category-{{ $category->id }}" method="POST" action="{{ route('merchandise.categories.destroy', $category->id) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
