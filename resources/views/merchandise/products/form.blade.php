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
                'is_preorder' => $variant->is_preorder,
                'preorder_expected_at' => $variant->preorder_expected_at
                    ? $variant->preorder_expected_at->format('Y-m-d')
                    : '',
            ];
        })->toArray() : [[
            'name' => 'Default',
            'sku' => '',
            'price' => '',
            'low_stock_threshold' => 0,
            'is_active' => true,
            'is_preorder' => false,
            'preorder_expected_at' => '',
        ]]);
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
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-4">
                            <div>
                                <label class="form-label d-block">Gambar utama</label>
                                <label class="variant-image-tile product-image-tile" title="Gambar utama">
                                    <input id="main-image" name="main_image" type="file" accept="image/jpeg,image/png,image/webp" {{ !$product || !$product->image_path ? 'required' : '' }}>
                                    <i class="bi bi-plus-lg"></i>
                                    @if ($product && $product->image_path)
                                        <img src="/storage/{{ $product->image_path }}" alt="{{ $product->name }}">
                                    @endif
                                </label>
                            </div>
                            <div>
                                <label class="form-label d-block">Gambar tambahan (maks. 3)</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @php($galleryImages = $product ? $product->images : collect())
                                    @foreach ($galleryImages as $image)
                                        <div class="text-center">
                                            <div class="variant-image-tile product-image-tile">
                                                <img src="/storage/{{ $image->image_path }}" alt="Galeri {{ $product->name }}">
                                            </div>
                                            <div class="form-check d-inline-block mt-1">
                                                <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $image->id }}" id="remove-image-{{ $image->id }}">
                                                <label class="form-check-label small" for="remove-image-{{ $image->id }}">Hapus</label>
                                            </div>
                                        </div>
                                    @endforeach
                                    @for ($slot = $galleryImages->count(); $slot < 3; $slot++)
                                        <label class="variant-image-tile product-image-tile" title="Gambar tambahan">
                                            <input name="gallery_images[]" type="file" accept="image/jpeg,image/png,image/webp">
                                            <i class="bi bi-plus-lg"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">1 gambar utama wajib, maksimal 3 gambar tambahan. JPG, PNG, atau WebP, maksimal 5 MB.</small>
                    </div>
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
                            <div class="col-auto">
                                <label class="variant-image-tile" title="Gambar varian">
                                    <input name="variants[{{ $index }}][image]" type="file" accept="image/jpeg,image/png,image/webp" {{ empty($variant['image_path']) ? 'required' : '' }}>
                                    <i class="bi bi-plus-lg"></i>
                                    @if (!empty($variant['image_path']))
                                        <img src="/storage/{{ $variant['image_path'] }}" alt="Gambar varian {{ $variant['name'] }}">
                                    @endif
                                </label>
                            </div>
                            <div class="col-md-2"><label class="form-label">Ukuran / nama varian</label><input name="variants[{{ $index }}][name]" class="form-control" maxlength="100" required value="{{ $variant['name'] ?? '' }}"></div>
                            <div class="col-md-1"><label class="form-label">Warna</label><input name="variants[{{ $index }}][color]" class="form-control" maxlength="80" value="{{ $variant['color'] ?? '' }}"></div>
                            <div class="col-md-1"><label class="form-label">SKU</label><input name="variants[{{ $index }}][sku]" class="form-control" maxlength="64" value="{{ $variant['sku'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label">Harga jual (Rp)</label><input name="variants[{{ $index }}][price]" class="form-control" type="number" min="0" step="0.01" required value="{{ $variant['price'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label">Batas stok</label><input name="variants[{{ $index }}][low_stock_threshold]" class="form-control" type="number" min="0" step="1" value="{{ $variant['low_stock_threshold'] ?? 0 }}"></div>
                            <div class="col-md-1">
                                <input type="hidden" name="variants[{{ $index }}][is_active]" value="0">
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="variants[{{ $index }}][is_active]" value="1" id="variant-active-{{ $index }}" {{ !array_key_exists('is_active', $variant) || $variant['is_active'] ? 'checked' : '' }}><label class="form-check-label" for="variant-active-{{ $index }}">Aktif</label></div>
                            </div>
                            <div class="col-md-1"><button class="btn btn-outline-danger w-100" type="button" data-remove-variant aria-label="Hapus varian"><i class="bi bi-trash"></i></button></div>
                        </div>
                        <div class="row g-3 mb-3 align-items-center" data-preorder-settings>
                            <div class="col-md-3">
                                <input type="hidden" name="variants[{{ $index }}][is_preorder]" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input preorder-toggle" type="checkbox" name="variants[{{ $index }}][is_preorder]" value="1" id="variant-preorder-{{ $index }}" {{ !empty($variant['is_preorder']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="variant-preorder-{{ $index }}">Sell as pre-order</label>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-nowrap" for="variant-preorder-date-{{ $index }}">Expected availability</label>
                                <input class="form-control preorder-date" style="max-width:200px" type="date" name="variants[{{ $index }}][preorder_expected_at]" id="variant-preorder-date-{{ $index }}" value="{{ $variant['preorder_expected_at'] ?? '' }}" {{ empty($variant['is_preorder']) ? 'disabled' : 'required' }}>
                            </div>
                            <div class="col-md-12"><small class="text-muted">Pre-order quantity is not limited by current physical stock. Stock In will allocate received items to paid orders in order.</small></div>
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

@section('content-style')
    <style>
        .variant-image-tile { position: relative; display: flex; align-items: center; justify-content: center; width: 58px; height: 58px; margin: 0; border: 2px dashed #c7cdd8; border-radius: 8px; background: #f6f7fb; color: #7c8798; cursor: pointer; overflow: hidden; }
        .variant-image-tile.product-image-tile { width: 110px; height: 110px; }
        .variant-image-tile i { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-size: 24px; line-height: 1; }
        .variant-image-tile i::before { display: block; line-height: 1; vertical-align: 0; }
        .variant-image-tile:hover { border-color: #435ebe; color: #435ebe; }
        .variant-image-tile input[type=file] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .variant-image-tile img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; }
        .variant-image-tile img + i, .variant-image-tile:has(img) i { display: none; }
    </style>
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
                    '<div class="col-auto"><label class="variant-image-tile" title="Gambar varian"><input name="variants[' + index + '][image]" type="file" accept="image/jpeg,image/png,image/webp" required><i class="bi bi-plus-lg"></i></label></div>' +
                    '<div class="col-md-2"><label class="form-label">Ukuran / nama varian</label><input name="variants[' + index + '][name]" class="form-control" maxlength="100" required></div>' +
                    '<div class="col-md-1"><label class="form-label">Warna</label><input name="variants[' + index + '][color]" class="form-control" maxlength="80"></div>' +
                    '<div class="col-md-1"><label class="form-label">SKU</label><input name="variants[' + index + '][sku]" class="form-control" maxlength="64"></div>' +
                    '<div class="col-md-2"><label class="form-label">Harga jual (Rp)</label><input name="variants[' + index + '][price]" class="form-control" type="number" min="0" step="0.01" required></div>' +
                    '<div class="col-md-2"><label class="form-label">Batas stok</label><input name="variants[' + index + '][low_stock_threshold]" class="form-control" type="number" min="0" step="1" value="0"></div>' +
                    '<div class="col-md-1"><input type="hidden" name="variants[' + index + '][is_active]" value="0"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="variants[' + index + '][is_active]" value="1" id="variant-active-' + index + '" checked><label class="form-check-label" for="variant-active-' + index + '">Aktif</label></div></div>' +
                    '<div class="col-md-1"><button class="btn btn-outline-danger w-100" type="button" data-remove-variant aria-label="Hapus varian"><i class="bi bi-trash"></i></button></div>';
                list.appendChild(row);
                var preorderSettings = document.createElement('div');
                preorderSettings.className = 'row g-3 mb-3 align-items-center';
                preorderSettings.setAttribute('data-preorder-settings', '');
                preorderSettings.innerHTML =
                    '<div class="col-md-3"><input type="hidden" name="variants[' + index + '][is_preorder]" value="0"><div class="form-check form-switch"><input class="form-check-input preorder-toggle" type="checkbox" name="variants[' + index + '][is_preorder]" value="1" id="variant-preorder-' + index + '"><label class="form-check-label" for="variant-preorder-' + index + '">Sell as pre-order</label></div></div>' +
                    '<div class="col-md-6 d-flex align-items-center gap-2"><label class="form-label mb-0 text-nowrap" for="variant-preorder-date-' + index + '">Expected availability</label><input class="form-control preorder-date" style="max-width:200px" type="date" name="variants[' + index + '][preorder_expected_at]" id="variant-preorder-date-' + index + '" disabled></div>' +
                    '<div class="col-md-12"><small class="text-muted">Pre-order quantity is not limited by current physical stock. Stock In will allocate received items to paid orders in order.</small></div>';
                list.appendChild(preorderSettings);
            });

            list.addEventListener('click', function (event) {
                var button = event.target.closest('[data-remove-variant]');
                if (button) {
                    var row = button.closest('[data-variant-row]');
                    var next = row.nextElementSibling;
                    row.remove();
                    if (next && next.hasAttribute('data-preorder-settings')) next.remove();
                }
            });
            document.addEventListener('change', function (event) {
                if (event.target.matches('.variant-image-tile input[type=file]')) {
                    var tile = event.target.closest('.variant-image-tile');
                    var file = event.target.files[0];
                    var preview = tile.querySelector('img');
                    if (file) {
                        if (!preview) {
                            preview = document.createElement('img');
                            tile.appendChild(preview);
                        }
                        preview.src = URL.createObjectURL(file);
                    }
                }
                if (event.target.matches('.preorder-toggle')) {
                    var row = event.target.closest('[data-preorder-settings]');
                    var date = row.querySelector('.preorder-date');
                    date.disabled = !event.target.checked;
                    date.required = event.target.checked;
                }
            });
            list.querySelectorAll('.preorder-toggle').forEach(function (toggle) {
                var date = toggle.closest('[data-preorder-settings]').querySelector('.preorder-date');
                date.disabled = !toggle.checked;
                date.required = toggle.checked;
            });
        })();
    </script>
@endsection
