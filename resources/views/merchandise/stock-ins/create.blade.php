@extends('main-layout.index')

@section('content-child')
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if ($variants->isEmpty())
        <div class="alert alert-warning">Belum ada varian produk aktif. Buat dan aktifkan produk serta variannya terlebih dahulu.</div>
    @endif

    <form method="POST" action="{{ route('merchandise.stock-ins.store') }}">
        @csrf
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Informasi Stock In</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="stock-in-date">Tanggal barang masuk</label>
                        <input id="stock-in-date" name="stock_in_date" class="form-control" type="date" required value="{{ old('stock_in_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="stock-in-notes">Catatan (opsional)</label>
                        <input id="stock-in-notes" name="notes" class="form-control" maxlength="1000" value="{{ old('notes') }}" placeholder="Contoh: Informasi pembelian dari Finance">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Barang yang Masuk</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-stock-item" {{ $variants->isEmpty() ? 'disabled' : '' }}><i class="bi bi-plus-lg"></i> Tambah Item</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th style="min-width:260px">Produk / Varian</th><th style="min-width:130px">Jumlah masuk</th><th style="min-width:180px">Harga modal per pcs (Rp)</th><th style="min-width:150px" class="text-end">Total modal</th><th></th></tr></thead>
                        <tbody id="stock-item-list">
                            @foreach (old('items', [['variant_id' => '', 'quantity' => 1, 'unit_cost' => '']]) as $index => $item)
                                <tr data-stock-item>
                                    <td>
                                        <select name="items[{{ $index }}][variant_id]" class="form-select" required>
                                            <option value="">Pilih produk / varian</option>
                                            @foreach ($variants as $variant)
                                                <option value="{{ $variant->id }}" {{ (string) ($item['variant_id'] ?? '') === (string) $variant->id ? 'selected' : '' }}>
                                                    {{ $variant->product->name }} — {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}{{ $variant->sku ? ' · ' . $variant->sku : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input name="items[{{ $index }}][quantity]" class="form-control stock-quantity" type="number" min="1" step="1" required value="{{ $item['quantity'] ?? 1 }}"></td>
                                    <td><input name="items[{{ $index }}][unit_cost]" class="form-control stock-unit-cost" type="number" min="0" step="0.01" required value="{{ $item['unit_cost'] ?? '' }}"></td>
                                    <td class="text-end stock-line-total">Rp0</td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger" data-remove-stock-item aria-label="Hapus item"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><th colspan="3" class="text-end">Total nilai modal</th><th class="text-end" id="stock-total">Rp0</th><th></th></tr></tfoot>
                    </table>
                </div>
                <small class="text-muted">Varian yang sama dapat dicatat beberapa kali jika harga modal pada barang masuk berbeda.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" type="submit" {{ $variants->isEmpty() ? 'disabled' : '' }}>Simpan Stock In</button>
            <a href="{{ route('merchandise.stock-ins') }}" class="btn btn-light-secondary">Batal</a>
        </div>
    </form>
@endsection

@section('content-script')
    <script>
        (function () {
            var list = document.getElementById('stock-item-list');
            var nextIndex = {{ count(old('items', [['variant_id' => '', 'quantity' => 1, 'unit_cost' => '']])) }};
            var variantOptions = @json($variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'label' => $variant->product->name . ' — ' . ($variant->color ? $variant->color . ' / ' : '') . $variant->name . ($variant->sku ? ' · ' . $variant->sku : ''),
                ];
            })->values());

            function formatRupiah(value) {
                return 'Rp' + Math.round(value).toLocaleString('id-ID');
            }

            function updateTotal() {
                var total = 0;
                list.querySelectorAll('[data-stock-item]').forEach(function (row) {
                    var quantity = Number(row.querySelector('.stock-quantity').value || 0);
                    var unitCost = Number(row.querySelector('.stock-unit-cost').value || 0);
                    var lineTotal = quantity * unitCost;
                    row.querySelector('.stock-line-total').textContent = formatRupiah(lineTotal);
                    total += lineTotal;
                });
                document.getElementById('stock-total').textContent = formatRupiah(total);
            }

            document.getElementById('add-stock-item').addEventListener('click', function () {
                var index = nextIndex++;
                var options = '<option value="">Pilih produk / varian</option>' + variantOptions.map(function (variant) {
                    return '<option value="' + variant.id + '">' + variant.label.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
                }).join('');
                var row = document.createElement('tr');
                row.setAttribute('data-stock-item', '');
                row.innerHTML =
                    '<td><select name="items[' + index + '][variant_id]" class="form-select" required>' + options + '</select></td>' +
                    '<td><input name="items[' + index + '][quantity]" class="form-control stock-quantity" type="number" min="1" step="1" required value="1"></td>' +
                    '<td><input name="items[' + index + '][unit_cost]" class="form-control stock-unit-cost" type="number" min="0" step="0.01" required value=""></td>' +
                    '<td class="text-end stock-line-total">Rp0</td>' +
                    '<td><button type="button" class="btn btn-sm btn-outline-danger" data-remove-stock-item aria-label="Hapus item"><i class="bi bi-trash"></i></button></td>';
                list.appendChild(row);
                updateTotal();
            });

            list.addEventListener('input', updateTotal);
            list.addEventListener('click', function (event) {
                var button = event.target.closest('[data-remove-stock-item]');
                if (button) {
                    button.closest('[data-stock-item]').remove();
                    updateTotal();
                }
            });
            updateTotal();
        })();
    </script>
@endsection
