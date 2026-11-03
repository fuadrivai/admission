@extends('main-layout.index')

@section('content-child')
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @php
        $selectedVariants = old('applicable_variants', $promotion ? $promotion->applicableVariants->pluck('id')->all() : []);
        $bundleRows = old('bundle_items', $promotion ? $promotion->bundleItems->map(function ($item) {
            return ['variant_id' => $item->variant_id, 'quantity' => $item->quantity];
        })->all() : [['variant_id' => '', 'quantity' => 1], ['', '']]);
        if (count($bundleRows) < 2) {
            $bundleRows[] = ['variant_id' => '', 'quantity' => 1];
        }
        $giftOptionRows = old('gift_options', $promotion ? $promotion->giftOptions->map(function ($option) {
            return ['variant_id' => $option->variant_id, 'max_quantity' => $option->max_quantity];
        })->all() : [['variant_id' => '', 'max_quantity' => 1]]);
        if (count($giftOptionRows) === 0) {
            $giftOptionRows[] = ['variant_id' => '', 'max_quantity' => 1];
        }
    @endphp

    <form method="POST" action="{{ $promotion ? route('merchandise.promotions.update', $promotion->id) : route('merchandise.promotions.store') }}">
        @csrf
        @if ($promotion) @method('PUT') @endif
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Informasi Promo</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="promotion-name">Nama promo</label>
                        <input id="promotion-name" name="name" class="form-control" maxlength="150" required value="{{ old('name', $promotion->name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="promotion-type">Jenis promo</label>
                        <select id="promotion-type" name="type" class="form-select" required>
                            @foreach ([
                                'minimum_purchase' => 'Minimum Purchase',
                                'quantity_discount' => 'Quantity Discount',
                                'buy_x_get_y' => 'Buy X Get Y',
                                'free_gift' => 'Free Gift',
                                'bundle' => 'Bundle',
                                'voucher' => 'Voucher',
                            ] as $type => $label)
                                <option value="{{ $type }}" {{ old('type', $promotion->type ?? '') === $type ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" data-promo-types="voucher">
                        <label class="form-label" for="promotion-code">Kode voucher</label>
                        <input id="promotion-code" name="code" class="form-control text-uppercase" maxlength="50" value="{{ old('code', $promotion->code ?? '') }}">
                        <small class="text-muted">Kode akan disimpan dalam huruf kapital.</small>
                    </div>
                    <div class="col-md-6" data-promo-types="minimum_purchase,quantity_discount,voucher">
                        <label class="form-label" for="discount-type">Bentuk diskon</label>
                        <select id="discount-type" name="discount_type" class="form-select">
                            <option value="percentage" {{ old('discount_type', $promotion->discount_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed" {{ old('discount_type', $promotion->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                        </select>
                    </div>
                    <div class="col-md-6" data-promo-types="minimum_purchase,quantity_discount,voucher">
                        <label class="form-label" for="discount-value">Nilai diskon</label>
                        <input id="discount-value" name="discount_value" type="number" min="0" step="0.01" class="form-control" value="{{ old('discount_value', $promotion->discount_value ?? '') }}">
                    </div>
                    <div class="col-md-6" data-promo-types="minimum_purchase,voucher">
                        <label class="form-label" for="minimum-purchase">Minimum belanja (Rp)</label>
                        <input id="minimum-purchase" name="minimum_purchase" type="number" min="0" step="0.01" class="form-control" value="{{ old('minimum_purchase', $promotion->minimum_purchase ?? '') }}">
                        <small class="text-muted">Wajib untuk Minimum Purchase; opsional untuk Voucher.</small>
                    </div>
                    <div class="col-md-6" data-promo-types="quantity_discount,free_gift">
                        <label class="form-label" for="min-quantity">Jumlah minimal untuk memenuhi promo</label>
                        <input id="min-quantity" name="min_quantity" type="number" min="1" class="form-control" value="{{ old('min_quantity', $promotion->min_quantity ?? '') }}">
                    </div>
                    <div class="col-md-6" data-promo-types="buy_x_get_y">
                        <label class="form-label" for="buy-quantity">Jumlah yang harus dibeli (X)</label>
                        <input id="buy-quantity" name="buy_quantity" type="number" min="1" class="form-control" value="{{ old('buy_quantity', $promotion->buy_quantity ?? '') }}">
                    </div>
                    <div class="col-md-6" data-promo-types="buy_x_get_y,free_gift">
                        <label class="form-label" for="get-quantity">Jumlah hadiah gratis (Y)</label>
                        <input id="get-quantity" name="get_quantity" type="number" min="1" class="form-control" value="{{ old('get_quantity', $promotion->get_quantity ?? '') }}">
                        <small class="text-muted" data-promo-types="buy_x_get_y">Jumlah hadiah yang didapat untuk setiap kelipatan jumlah pembelian X.</small>
                    </div>
                    <div class="col-md-6" data-promo-types="free_gift">
                        <label class="form-label" for="gift-variant">Varian hadiah</label>
                        <select id="gift-variant" name="gift_variant_id" class="form-select">
                            <option value="">Pilih varian</option>
                            @foreach ($promotionVariants as $variant)
                                <option value="{{ $variant->id }}" {{ (string) old('gift_variant_id', $promotion->gift_variant_id ?? '') === (string) $variant->id ? 'selected' : '' }}>
                                    {{ $variant->product->name }} · {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" data-promo-types="bundle">
                        <label class="form-label" for="bundle-price">Harga khusus per paket (Rp)</label>
                        <input id="bundle-price" name="bundle_price" type="number" min="0" step="0.01" class="form-control" value="{{ old('bundle_price', $promotion->bundle_price ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3" data-promo-types="minimum_purchase,quantity_discount,buy_x_get_y,free_gift,voucher">
            <div class="card-header"><h5 class="mb-0">Produk yang Berlaku</h5></div>
            <div class="card-body">
                <label class="form-label" for="applicable-variants">Varian produk</label>
                <select id="applicable-variants" name="applicable_variants[]" class="form-select" multiple size="8">
                    @foreach ($promotionVariants as $variant)
                        <option value="{{ $variant->id }}" {{ in_array($variant->id, $selectedVariants) ? 'selected' : '' }}>
                            {{ $variant->product->name }} · {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted" data-promo-types="buy_x_get_y">Pilih varian produk pemicu pembelian (X); satu pembelian harus memenuhi jumlah X di atas.</small>
                <small class="text-muted" data-promo-types="minimum_purchase,quantity_discount,free_gift,voucher">Kosongkan untuk semua varian.</small>
            </div>
        </div>

        <div class="card mb-3" data-promo-types="buy_x_get_y">
            <div class="card-header"><h5 class="mb-0">Pilihan Hadiah</h5></div>
            <div class="card-body">
                <div id="gift-options">
                    @foreach ($giftOptionRows as $index => $row)
                        <div class="row g-2 align-items-end mb-2 gift-option-row">
                            <div class="col-md-7">
                                <label class="form-label">Varian hadiah</label>
                                <select name="gift_options[{{ $index }}][variant_id]" class="form-select gift-option-select">
                                    <option value="">Pilih varian</option>
                                    @foreach ($promotionVariants as $variant)
                                        <option value="{{ $variant->id }}" {{ (string) ($row['variant_id'] ?? '') === (string) $variant->id ? 'selected' : '' }}>
                                            {{ $variant->product->name }} · {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Maks. hadiah per pesanan</label>
                                <input name="gift_options[{{ $index }}][max_quantity]" type="number" min="1" class="form-control" value="{{ $row['max_quantity'] ?? 1 }}">
                            </div>
                            <div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-gift-option w-100">Hapus</button></div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-gift-option" class="btn btn-sm btn-outline-primary">Tambah pilihan hadiah</button>
                <small class="d-block text-muted mt-2">Pembeli memilih satu varian hadiah. Jumlah hadiah dibatasi jumlah promo dan batas per pesanan di atas, serta tidak dapat melebihi stok tersedia.</small>
            </div>
        </div>

        <div class="card mb-3" data-promo-types="bundle">
            <div class="card-header"><h5 class="mb-0">Isi Paket Bundle</h5></div>
            <div class="card-body">
                <div id="bundle-items">
                    @foreach ($bundleRows as $index => $row)
                        <div class="row g-2 align-items-end mb-2 bundle-row">
                            <div class="col-md-8">
                                <label class="form-label">Varian</label>
                                <select name="bundle_items[{{ $index }}][variant_id]" class="form-select">
                                    <option value="">Pilih varian</option>
                                    @foreach ($promotionVariants as $variant)
                                        <option value="{{ $variant->id }}" {{ (string) ($row['variant_id'] ?? '') === (string) $variant->id ? 'selected' : '' }}>
                                            {{ $variant->product->name }} · {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Jumlah</label>
                                <input name="bundle_items[{{ $index }}][quantity]" type="number" min="1" class="form-control" value="{{ $row['quantity'] ?? 1 }}">
                            </div>
                            <div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-bundle w-100">Hapus</button></div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-bundle" class="btn btn-sm btn-outline-primary">Tambah varian paket</button>
                <small class="d-block text-muted mt-2">Harga promo berlaku untuk satu kombinasi paket; jumlah paket dihitung dari item yang ada di keranjang.</small>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Periode, Kuota, dan Status</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="valid-from">Mulai</label>
                        <input id="valid-from" name="valid_from" type="datetime-local" class="form-control" value="{{ old('valid_from', $promotion && $promotion->valid_from ? $promotion->valid_from->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="valid-until">Berakhir</label>
                        <input id="valid-until" name="valid_until" type="datetime-local" class="form-control" value="{{ old('valid_until', $promotion && $promotion->valid_until ? $promotion->valid_until->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="usage-limit">Kuota penggunaan</label>
                        <input id="usage-limit" name="usage_limit" type="number" min="1" class="form-control" value="{{ old('usage_limit', $promotion->usage_limit ?? '') }}">
                        <small class="text-muted">Kosongkan untuk tanpa batas.</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="promotion-active" name="is_active" value="1" {{ old('is_active', $promotion->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="promotion-active">Promo aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan Promo</button>
            <a href="{{ route('merchandise.promotions') }}" class="btn btn-light">Batal</a>
        </div>
    </form>

    <script>
        (function () {
            const typeInput = document.getElementById('promotion-type');
            const updateSections = function () {
                document.querySelectorAll('[data-promo-types]').forEach(function (section) {
                    const types = section.dataset.promoTypes.split(',');
                    section.hidden = !types.includes(typeInput.value);
                });
            };
            typeInput.addEventListener('change', updateSections);
            updateSections();

            const variantOptions = @json($promotionVariants->map(function ($variant) {
                return ['id' => $variant->id, 'label' => $variant->product->name . ' · ' . ($variant->color ? $variant->color . ' / ' : '') . $variant->name];
            })->values());
            const createVariantOptions = function () {
                return '<option value="">Pilih varian</option>' +
                    variantOptions.map(function (item) { return '<option value="' + item.id + '">' + item.label.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</option>'; }).join('');
            };
            const container = document.getElementById('bundle-items');
            document.getElementById('add-bundle').addEventListener('click', function () {
                const index = container.querySelectorAll('.bundle-row').length;
                const row = document.createElement('div');
                row.className = 'row g-2 align-items-end mb-2 bundle-row';
                row.innerHTML = '<div class="col-md-8"><label class="form-label">Varian</label><select name="bundle_items[' + index + '][variant_id]" class="form-select">' +
                    createVariantOptions() +
                    '</select></div><div class="col-md-2"><label class="form-label">Jumlah</label><input name="bundle_items[' + index + '][quantity]" type="number" min="1" class="form-control" value="1"></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-bundle w-100">Hapus</button></div>';
                container.appendChild(row);
            });
            container.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-bundle') && container.querySelectorAll('.bundle-row').length > 2) {
                    event.target.closest('.bundle-row').remove();
                }
            });

            const giftContainer = document.getElementById('gift-options');
            let nextGiftIndex = giftContainer.querySelectorAll('.gift-option-row').length;
            document.getElementById('add-gift-option').addEventListener('click', function () {
                const index = nextGiftIndex++;
                const row = document.createElement('div');
                row.className = 'row g-2 align-items-end mb-2 gift-option-row';
                row.innerHTML = '<div class="col-md-7"><label class="form-label">Varian hadiah</label><select name="gift_options[' + index + '][variant_id]" class="form-select gift-option-select">' +
                    createVariantOptions() +
                    '</select></div><div class="col-md-3"><label class="form-label">Maks. hadiah per pesanan</label><input name="gift_options[' + index + '][max_quantity]" type="number" min="1" class="form-control" value="1"></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-gift-option w-100">Hapus</button></div>';
                giftContainer.appendChild(row);
            });
            giftContainer.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-gift-option') && giftContainer.querySelectorAll('.gift-option-row').length > 1) {
                    event.target.closest('.gift-option-row').remove();
                }
            });
        })();
    </script>
@endsection

@section('content-script')
    <script>
        $(function () {
            $('.gift-option-select').select2({
                width: '100%',
                placeholder: 'Pilih varian hadiah'
            });

            $('#add-gift-option').on('click', function () {
                window.setTimeout(function () {
                    $('#gift-options .gift-option-row:last .gift-option-select').select2({
                        width: '100%',
                        placeholder: 'Pilih varian hadiah'
                    });
                }, 0);
            });
        });
    </script>
@endsection
