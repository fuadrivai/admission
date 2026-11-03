@extends('main-layout.index')

@section('content-child')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <p class="text-muted mb-0">Ringkasan katalog dan persediaan merchandise.</p>
        <div class="d-flex gap-2">
            <a href="{{ route('merchandise.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </a>
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('merchandise.stock-ins.create') }}" class="btn btn-success">
                    <i class="bi bi-box-arrow-in-down"></i> Catat Stock In
                </a>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="{{ auth()->user()->role === 'admin' ? 'col-md-3' : 'col-md-4' }}">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted">Produk aktif</div>
                <div class="fs-3 fw-bold">{{ number_format($total_products) }}</div>
            </div></div>
        </div>
        <div class="{{ auth()->user()->role === 'admin' ? 'col-md-3' : 'col-md-4' }}">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted">Kategori aktif</div>
                <div class="fs-3 fw-bold">{{ number_format($total_categories) }}</div>
            </div></div>
        </div>
        <div class="{{ auth()->user()->role === 'admin' ? 'col-md-3' : 'col-md-4' }}">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted">Total stok tersedia</div>
                <div class="fs-3 fw-bold">{{ number_format($total_stock) }} <small class="fs-6">pcs</small></div>
            </div></div>
        </div>
        @if (auth()->user()->role === 'admin')
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted">Nilai modal Stock In</div>
                <div class="fs-3 fw-bold">Rp{{ number_format($inventory_value, 0, ',', '.') }}</div>
            </div></div>
        </div>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stok Menipis</h5>
                    <a href="{{ route('merchandise.products') }}" class="small">Lihat produk</a>
                </div>
                <div class="card-body p-0">
                    @if ($low_stock_variants->isEmpty())
                        <div class="text-muted text-center py-4">Belum ada varian yang mencapai batas stok minimum.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead><tr><th>Produk</th><th>Varian</th><th class="text-end">Stok</th><th class="text-end">Minimum</th></tr></thead>
                                <tbody>
                                    @foreach ($low_stock_variants as $variant)
                                        <tr>
                                            <td>{{ $variant->product->name }}</td>
                                            <td>{{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}</td>
                                            <td class="text-end">{{ number_format($variant->current_stock) }}</td>
                                            <td class="text-end">{{ number_format($variant->low_stock_threshold) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stock In Terbaru</h5>
                    <a href="{{ route('merchandise.stock-ins') }}" class="small">Semua transaksi</a>
                </div>
                <div class="card-body p-0">
                    @if ($recent_stock_ins->isEmpty())
                        <div class="text-muted text-center py-4">Belum ada transaksi Stock In.</div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($recent_stock_ins as $stockIn)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">Stock In #{{ $stockIn->id }}</div>
                                        <small class="text-muted">{{ $stockIn->stock_in_date->format('d M Y') }}</small>
                                    </div>
                                    <span class="badge bg-light-primary text-primary">{{ number_format($stockIn->items->sum('quantity')) }} pcs</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
