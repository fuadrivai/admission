@extends('main-layout.index')

@section('content-child')
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @php
        $variantRows = old('variants', $product ? $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'color' => $variant->color,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'image_path' => $variant->image_path,
                'low_stock_threshold' => $variant->low_stock_threshold,
                'is_active' => $variant->is_active,
            ];
        })->toArray() : [['name' => 'Default', 'sku' => '', 'price' => '', 'low_stock_threshold' => 0, 'is_active' => true]]);
        if ($product && is_array($variantRows)) {
            foreach ($variantRows as &$variantRow) {
                $existingVariant = !empty($variantRow['id']) ? $product->variants->firstWhere('id', $variantRow['id']) : null;
                if ($existingVariant && empty($variantRow['image_path'])) {
                    $variantRow['image_path'] = $existingVariant->image_path;
                }
            }
            unset($variantRow);
        }
    @endphp

    <form method="POST" enctype="multipart/form-data" action="{{ $product ? route('merchandise.products.update', $product->id) : route('merchandise.products.store') }}">
        @csrf
        @if ($product) @method('PUT') @endif
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Informasi Produk</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="product-name">Nama produk</label>
                        <input id="product-name" name="name" class="form-control" maxlength="150" required value="{{ old('name', $product->name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="category-id">Kategori</label>
                        <select id="category-id" name="category_id" class="form-select" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id ?? '') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @if ($categories->isEmpty())
                            <small class="text-danger">Buat kategori terlebih dahulu sebelum menambah produk.</small>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="product-description">Deskripsi</label>
                        <textarea id="product-description" name="description" class="form-control" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="main-image">Gambar utama</label>
                        <input id="main-image" name="main_image" class="form-control" type="file" accept="image/jpeg,image/png,image/webp" {{ !$product || !$product->image_path ? 'required' : '' }}>
                        <small class="text-muted">Wajib memiliki satu gambar utama. JPG, PNG, atau WebP, maksimal 5 MB.</small>
                    </div>
                    @if ($product && $product->image_path)
                        <div class="col-md-4">
                            <label class="form-label d-block">Foto saat ini</label>
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}" style="max-width:180px;max-height:140px;object-fit:cover;border-radius:6px">
                        </div>
                    @endif
                    <div class="col-md-8">
                        <label class="form-label" for="gallery-images">Gambar tambahan</label>
                        <input id="gallery-images" name="gallery_images[]" class="form-control" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                        <small class="text-muted">Pilih beberapa gambar sekaligus sebagai galeri produk.</small>
                    </div>
                    @if ($product && $product->images->isNotEmpty())
                        <div class="col-12">
                            <label class="form-label">Gambar galeri saat ini</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($product->images as $image)
                                    <label class="border rounded p-2 text-center">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->image_path) }}" alt="Galeri {{ $product->name }}" style="width:120px;height:90px;object-fit:cover;border-radius:4px">
                                        <span class="form-check d-block mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                                            <span class="form-check-label">Hapus gambar</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="product-active" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="product-active">Produk aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="mb-0">Varian dan Harga Jual</h5><small class="text-muted">Contoh nama varian: S, M, L, Merah, atau Default. Perubahan harga yang sudah ada dilakukan melalui Selling Prices.</small></div>
                <button class="btn btn-sm btn-outline-primary" type="button" id="add-variant"><i class="bi bi-plus-lg"></i> Tambah Varian</button>
            </div>
            <div class="card-body">
                <div id="variant-list">
                    @foreach ($variantRows as $index => $variant)
                        <div class="row g-2 align-items-end mb-3 border-bottom pb-3" data-variant-row>
                            @if (!empty($variant['id']))<input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] }}">@endif
                            <div class="col-md-2"><label class="form-label">Ukuran / nama varian</label><input name="variants[{{ $index }}][name]" class="form-control" maxlength="100" required value="{{ $variant['name'] ?? '' }}"></div>
                            <div class="col-md-1"><label class="form-label">Warna</label><input name="variants[{{ $index }}][color]" class="form-control" maxlength="80" value="{{ $variant['color'] ?? '' }}"></div>
                            <div class="col-md-1"><label class="form-label">SKU</label><input name="variants[{{ $index }}][sku]" class="form-control" maxlength="64" value="{{ $variant['sku'] ?? '' }}"></div>
                            <div class="col-md-2">
                                <label class="form-label">Gambar varian</label>
                                <input name="variants[{{ $index }}][image]" class="form-control" type="file" accept="image/jpeg,image/png,image/webp" {{ empty($variant['image_path']) ? 'required' : '' }}>
                                @if (!empty($variant['image_path']))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($variant['image_path']) }}" alt="Gambar varian {{ $variant['name'] }}" class="mt-2" style="width:90px;height:65px;object-fit:cover;border-radius:4px">
                                @else
                                    <small class="text-muted">Wajib diisi.</small>
                                @endif
                            </div>
                            <div class="col-md-2"><label class="form-label">Harga jual (Rp)</label><input name="variants[{{ $index }}][price]" class="form-control" type="number" min="0" step="0.01" required value="{{ $variant['price'] ?? '' }}" {{ !empty($variant['id']) ? 'readonly' : '' }}></div>
                            <div class="col-md-2"><label class="form-label">Batas stok</label><input name="variants[{{ $index }}][low_stock_threshold]" class="form-control" type="number" min="0" step="1" value="{{ $variant['low_stock_threshold'] ?? 0 }}"></div>
                            <div class="col-md-1">
                                <input type="hidden" name="variants[{{ $index }}][is_active]" value="0">
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="variants[{{ $index }}][is_active]" value="1" id="variant-active-{{ $index }}" {{ !array_key_exists('is_active', $variant) || $variant['is_active'] ? 'checked' : '' }}><label class="form-check-label" for="variant-active-{{ $index }}">Aktif</label></div>
                            </div>
                            <div class="col-md-1"><button class="btn btn-outline-danger w-100" type="button" data-remove-variant aria-label="Hapus varian"><i class="bi bi-trash"></i></button></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" {{ $categories->isEmpty() ? 'disabled' : '' }}>Simpan Produk</button>
            <a href="{{ route('merchandise.products') }}" class="btn btn-light-secondary">Batal</a>
        </div>
    </form>
@endsection

@section('content-script')
    <script>
        (function () {
            var list = document.getElementById('variant-list');
            var nextIndex = {{ count($variantRows) }};

            document.getElementById('add-variant').addEventListener('click', function () {
                var index = nextIndex++;
                var row = document.createElement('div');
                row.className = 'row g-2 align-items-end mb-3 border-bottom pb-3';
                row.setAttribute('data-variant-row', '');
                row.innerHTML =
                    '<div class="col-md-2"><label class="form-label">Ukuran / nama varian</label><input name="variants[' + index + '][name]" class="form-control" maxlength="100" required></div>' +
                    '<div class="col-md-1"><label class="form-label">Warna</label><input name="variants[' + index + '][color]" class="form-control" maxlength="80"></div>' +
                    '<div class="col-md-1"><label class="form-label">SKU</label><input name="variants[' + index + '][sku]" class="form-control" maxlength="64"></div>' +
                    '<div class="col-md-2"><label class="form-label">Gambar varian</label><input name="variants[' + index + '][image]" class="form-control" type="file" accept="image/jpeg,image/png,image/webp" required></div>' +
                    '<div class="col-md-2"><label class="form-label">Harga jual (Rp)</label><input name="variants[' + index + '][price]" class="form-control" type="number" min="0" step="0.01" required></div>' +
                    '<div class="col-md-2"><label class="form-label">Batas stok</label><input name="variants[' + index + '][low_stock_threshold]" class="form-control" type="number" min="0" step="1" value="0"></div>' +
                    '<div class="col-md-1"><input type="hidden" name="variants[' + index + '][is_active]" value="0"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="variants[' + index + '][is_active]" value="1" id="variant-active-' + index + '" checked><label class="form-check-label" for="variant-active-' + index + '">Aktif</label></div></div>' +
                    '<div class="col-md-1"><button class="btn btn-outline-danger w-100" type="button" data-remove-variant aria-label="Hapus varian"><i class="bi bi-trash"></i></button></div>';
                list.appendChild(row);
            });

            list.addEventListener('click', function (event) {
                var button = event.target.closest('[data-remove-variant]');
                if (button) button.closest('[data-variant-row]').remove();
            });
        })();
    </script>
@endsection
