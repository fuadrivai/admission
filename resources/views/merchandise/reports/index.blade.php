@extends('main-layout.index')

@section('content-child')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('merchandise.reports') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="report-type">Jenis laporan</label>
                    <select id="report-type" name="report" class="form-select">
                        <option value="sales" {{ $reportType === 'sales' ? 'selected' : '' }}>Sales Report</option>
                        <option value="profit" {{ $reportType === 'profit' ? 'selected' : '' }}>Profit Report</option>
                        <option value="stock" {{ $reportType === 'stock' ? 'selected' : '' }}>Stock Report</option>
                        <option value="movements" {{ $reportType === 'movements' ? 'selected' : '' }}>Stock In / Out Report</option>
                        <option value="promotions" {{ $reportType === 'promotions' ? 'selected' : '' }}>Promotion Report</option>
                    </select>
                </div>
                <div class="col-md-3" data-report-date-filter>
                    <label class="form-label" for="date-from">Dari tanggal</label>
                    <input id="date-from" name="date_from" class="form-control" type="date" value="{{ $dateFrom }}" max="{{ now()->toDateString() }}" required>
                </div>
                <div class="col-md-3" data-report-date-filter>
                    <label class="form-label" for="date-to">Sampai tanggal</label>
                    <input id="date-to" name="date_to" class="form-control" type="date" value="{{ $dateTo }}" max="{{ now()->toDateString() }}" required>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">{{ $title }}</h4>
            <p class="text-muted mb-0">{{ $description }}</p>
        </div>
        @if ($reportType === 'stock')
            <span class="badge bg-light-primary text-primary">Posisi saat ini</span>
        @else
            <span class="badge bg-light-primary text-primary">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} – {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>
        @endif
    </div>

    <div class="row g-3 mb-4">
        @foreach ($metrics as $metric)
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 mb-0">
                    <div class="card-body">
                        <div class="small text-muted">{{ $metric['label'] }}</div>
                        <div class="fs-4 fw-bold mt-1">{{ $metric['value'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Rincian</h5></div>
        <div class="card-body">
            @if ($rows->isEmpty())
                <div class="text-center text-muted py-5">Tidak ada data untuk laporan dan rentang tanggal ini.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        @if ($reportType === 'sales')
                            <thead><tr><th>Tanggal pembayaran</th><th class="text-end">Pesanan</th><th class="text-end">Penjualan bersih</th><th class="text-end">Biaya admin</th><th class="text-end">Total dibayar</th></tr></thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($row->report_date)->format('d M Y') }}</td>
                                        <td class="text-end">{{ number_format($row->order_count) }}</td>
                                        <td class="text-end">Rp{{ number_format($row->net_sales, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp{{ number_format($row->admin_fees, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">Rp{{ number_format($row->collected_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @elseif ($reportType === 'profit')
                            <thead><tr><th>Tanggal lunas</th><th>No. pesanan</th><th>Pembeli</th><th class="text-end">Penjualan bersih</th><th class="text-end">HPP</th><th class="text-end">Laba kotor</th></tr></thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row->paid_at ? \Carbon\Carbon::parse($row->paid_at)->format('d M Y') : '-' }}</td>
                                        <td>{{ $row->code }}</td>
                                        <td>{{ $row->parent_name }}</td>
                                        <td class="text-end">Rp{{ number_format($row->net_sales, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp{{ number_format($row->cost_of_goods, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold {{ $row->gross_profit < 0 ? 'text-danger' : 'text-success' }}">Rp{{ number_format($row->gross_profit, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @elseif ($reportType === 'stock')
                            <thead><tr><th>Produk</th><th>Kategori</th><th>Varian</th><th class="text-end">Stok tersedia</th><th class="text-end">Batas minimum</th><th class="text-end">Nilai persediaan</th></tr></thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row->product->name }}</td>
                                        <td>{{ $row->product->category->name }}</td>
                                        <td>{{ $row->color ? $row->color . ' / ' : '' }}{{ $row->name }}</td>
                                        <td class="text-end {{ $row->low_stock_threshold > 0 && $row->current_stock <= $row->low_stock_threshold ? 'text-danger fw-semibold' : '' }}">{{ number_format($row->current_stock) }} pcs</td>
                                        <td class="text-end">{{ number_format($row->low_stock_threshold) }} pcs</td>
                                        <td class="text-end">Rp{{ number_format($row->inventory_value, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @elseif ($reportType === 'movements')
                            <thead><tr><th>Tanggal</th><th>Tipe</th><th>Produk / Varian</th><th class="text-end">Jumlah</th><th>Referensi</th></tr></thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($row->movement_date)->format('d M Y') }}</td>
                                        <td>{{ $row->movement_type }}</td>
                                        <td>{{ $row->product_name }} — {{ $row->color ? $row->color . ' / ' : '' }}{{ $row->variant_name }}</td>
                                        <td class="text-end {{ strpos($row->movement_type, '(-)') !== false || $row->movement_type === 'Order Stock Out' || $row->movement_type === 'Stock Out' ? 'text-danger' : 'text-success' }}">
                                            {{ strpos($row->movement_type, '(-)') !== false || $row->movement_type === 'Order Stock Out' || $row->movement_type === 'Stock Out' ? '-' : '+' }}{{ number_format($row->quantity) }} pcs
                                        </td>
                                        <td>{{ $row->reference ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <thead><tr><th>Promo</th><th>Kode</th><th class="text-end">Pesanan lunas</th><th class="text-end">Total diskon</th><th class="text-end">Penjualan bersih</th></tr></thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row->promotion_name ?: 'Promo #' . $row->promotion_id }}</td>
                                        <td>{{ $row->promotion_code ?: '-' }}</td>
                                        <td class="text-end">{{ number_format($row->order_count) }}</td>
                                        <td class="text-end">Rp{{ number_format($row->discount_total, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp{{ number_format($row->net_sales, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if ($reportType === 'sales' && $topProducts->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0">Produk Terlaris</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Produk</th><th>Varian</th><th class="text-end">Jumlah</th><th class="text-end">Penjualan produk</th></tr></thead>
                        <tbody>
                            @foreach ($topProducts as $product)
                                <tr>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->variant_name }}</td>
                                    <td class="text-end">{{ number_format($product->quantity) }} pcs</td>
                                    <td class="text-end">Rp{{ number_format($product->sales_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('content-script')
    <script>
        (function () {
            var reportType = document.getElementById('report-type');
            var dateFilters = document.querySelectorAll('[data-report-date-filter]');
            var dateInputs = document.querySelectorAll('#date-from, #date-to');

            function updateDateFilters() {
                var stockSnapshot = reportType.value === 'stock';
                dateFilters.forEach(function (filter) {
                    filter.classList.toggle('d-none', stockSnapshot);
                });
                dateInputs.forEach(function (input) {
                    input.disabled = stockSnapshot;
                });
            }

            reportType.addEventListener('change', updateDateFilters);
            updateDateFilters();
        })();
    </script>
@endsection
