@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="alert alert-light-primary">
        HPP dihitung dengan metode rata-rata tertimbang: total modal Stock In dibagi total jumlah masuk. Harga modal dan simulasi margin hanya ditampilkan untuk admin.
    </div>

    <div class="card">
        <div class="card-body">
            @if ($variants->isEmpty())
                <div class="text-center text-muted py-5">Belum ada varian produk.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Produk / Varian</th>
                                <th class="text-end">Stok</th>
                                <th class="text-end">HPP rata-rata</th>
                                <th style="min-width:170px" class="text-end">Harga jual saat ini</th>
                                <th style="min-width:135px">Target margin</th>
                                <th class="text-end">Saran harga</th>
                                <th style="min-width:260px">Ubah harga jual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($variants as $variant)
                                @php
                                    $averageCost = $variant->average_unit_cost === null ? null : (float) $variant->average_unit_cost;
                                @endphp
                                <tr id="variant-{{ $variant->id }}" data-price-row data-average-cost="{{ $averageCost ?? '' }}">
                                    <td>
                                        <div class="fw-semibold">{{ $variant->product->name }}</div>
                                        <div>{{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}</div>
                                        <small class="text-muted">
                                            {{ $variant->sku ?: 'Tanpa SKU' }} · {{ $variant->product->category->name }}
                                            @unless ($variant->is_active && $variant->product->is_active && $variant->product->category->is_active)
                                                · Nonaktif
                                            @endunless
                                        </small>
                                        <div><a class="small" href="{{ route('merchandise.selling-prices.history', $variant->id) }}">Riwayat harga</a></div>
                                    </td>
                                    <td class="text-end">{{ number_format($variant->current_stock) }} pcs</td>
                                    <td class="text-end">
                                        @if ($averageCost === null)
                                            <span class="text-muted">Belum ada Stock In</span>
                                        @else
                                            Rp{{ number_format($averageCost, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">Rp{{ number_format($variant->price, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" min="0" max="99.99" step="0.01" value="40" data-margin-input aria-label="Target margin persen">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span data-suggested-price>
                                            {{ $averageCost === null ? '—' : 'Rp' . number_format($averageCost / (1 - 0.4), 0, ',', '.') }}
                                        </span>
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1" data-use-suggested {{ $averageCost === null ? 'disabled' : '' }}>Gunakan</button>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('merchandise.selling-prices.update', $variant->id) }}" data-price-form>
                                            @csrf
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="price" class="form-control" min="0" step="0.01" max="9999999999.99" required value="{{ number_format($variant->price, 2, '.', '') }}" data-price-input aria-label="Harga jual baru">
                                            </div>
                                            <input type="text" name="reason" class="form-control form-control-sm mb-2" maxlength="500" required placeholder="Alasan perubahan harga">
                                            <button type="submit" class="btn btn-sm btn-primary w-100">Simpan Harga</button>
                                        </form>
                                        <div class="small text-muted mt-1" data-margin-result></div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $variants->links() }}
            @endif
        </div>
    </div>
@endsection

@section('content-script')
    <script>
        (function () {
            function rupiah(value) {
                return 'Rp' + Math.round(value).toLocaleString('id-ID');
            }

            function updateRow(row) {
                var costValue = row.getAttribute('data-average-cost');
                var cost = costValue === '' ? null : Number(costValue);
                var marginInput = row.querySelector('[data-margin-input]');
                var priceInput = row.querySelector('[data-price-input]');
                var suggestedPrice = row.querySelector('[data-suggested-price]');
                var result = row.querySelector('[data-margin-result]');
                var useButton = row.querySelector('[data-use-suggested]');
                var targetMargin = Number(marginInput.value);
                var currentPrice = Number(priceInput.value);

                if (cost === null || !Number.isFinite(targetMargin) || targetMargin < 0 || targetMargin >= 100) {
                    suggestedPrice.textContent = '—';
                    useButton.disabled = true;
                } else {
                    var suggested = cost / (1 - targetMargin / 100);
                    suggestedPrice.textContent = rupiah(suggested);
                    useButton.disabled = false;
                }

                if (cost === null || currentPrice <= 0) {
                    result.textContent = cost === null ? 'Isi Stock In terlebih dahulu untuk melihat HPP dan margin.' : '';
                    return;
                }

                var profit = currentPrice - cost;
                var actualMargin = profit / currentPrice * 100;
                result.textContent = 'Estimasi laba/unit ' + rupiah(profit) + ' · margin ' + actualMargin.toFixed(2) + '%';
                result.className = 'small mt-1 ' + (profit < 0 ? 'text-danger' : 'text-muted');
            }

            document.querySelectorAll('[data-price-row]').forEach(function (row) {
                row.querySelector('[data-margin-input]').addEventListener('input', function () {
                    updateRow(row);
                });
                row.querySelector('[data-price-input]').addEventListener('input', function () {
                    updateRow(row);
                });
                row.querySelector('[data-use-suggested]').addEventListener('click', function () {
                    var cost = Number(row.getAttribute('data-average-cost'));
                    var margin = Number(row.querySelector('[data-margin-input]').value);
                    if (Number.isFinite(cost) && Number.isFinite(margin) && margin >= 0 && margin < 100) {
                        row.querySelector('[data-price-input]').value = (cost / (1 - margin / 100)).toFixed(2);
                        updateRow(row);
                    }
                });
                updateRow(row);
            });
        })();
    </script>
@endsection
