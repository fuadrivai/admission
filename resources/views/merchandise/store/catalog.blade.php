@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="alert alert-light-primary">
        Atur produk yang tampil di toko, periode penjualan, urutan, dan batas jumlah ready-stock yang ditawarkan per varian.
        Stok publik tidak pernah melebihi saldo Stock In. Kosongkan batas stok online untuk menggunakan seluruh stok fisik.
        Pemesanan dan pengurangan stok oleh transaksi online akan diaktifkan pada tahap Orders.
        Pastikan kategori produk aktif dan ditampilkan di toko.
    </div>

    @forelse ($products as $product)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $product->name }}</h5>
                    <small class="text-muted">{{ $product->category->name }} · {{ $product->variants->count() }} varian</small>
                </div>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('merchandise.products.edit', $product->id) }}">Edit detail produk</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('merchandise.catalog.update', $product->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label" for="display-order-{{ $product->id }}">Urutan tampil</label>
                            <input id="display-order-{{ $product->id }}" type="number" min="0" max="4294967295" name="display_order" class="form-control" value="{{ $product->display_order }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="sale-start-{{ $product->id }}">Mulai penjualan (opsional)</label>
                            <input id="sale-start-{{ $product->id }}" type="datetime-local" name="sale_start_at" class="form-control" value="{{ $product->sale_start_at ? $product->sale_start_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="sale-end-{{ $product->id }}">Akhir penjualan (opsional)</label>
                            <input id="sale-end-{{ $product->id }}" type="datetime-local" name="sale_end_at" class="form-control" value="{{ $product->sale_end_at ? $product->sale_end_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-12">
                            <input type="hidden" name="is_published" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="published-{{ $product->id }}" name="is_published" value="1" {{ $product->is_published ? 'checked' : '' }}>
                                <label class="form-check-label" for="published-{{ $product->id }}">Tampilkan produk ini di Online Store</label>
                            </div>
                        </div>
                    </div>

                    @if ($product->variants->isNotEmpty())
                        <div class="table-responsive mt-3">
                            <table class="table table-sm align-middle mb-0">
                                <thead><tr><th>Varian</th><th>Harga jual</th><th>Stok online maksimum</th></tr></thead>
                                <tbody>
                                    @foreach ($product->variants as $variant)
                                        <tr>
                                            <td>
                                                {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}
                                                @if (!$variant->is_active)<span class="badge bg-secondary">Nonaktif</span>@endif
                                                @if ($variant->is_preorder)
                                                    <span class="badge bg-warning text-dark">Pre-order · {{ $variant->preorder_expected_at ? $variant->preorder_expected_at->format('d M Y') : 'ETA not set' }}</span>
                                                @endif
                                                <input type="hidden" name="variants[{{ $loop->index }}][id]" value="{{ $variant->id }}">
                                            </td>
                                            <td>Rp{{ number_format($variant->price, 0, ',', '.') }}</td>
                                            <td style="max-width:250px">
                                                <input type="number" min="0" max="4294967295" step="1" name="variants[{{ $loop->index }}][online_stock_limit]" class="form-control form-control-sm" value="{{ $variant->online_stock_limit }}" placeholder="Gunakan seluruh stok fisik" {{ $variant->is_preorder ? 'readonly' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit">Simpan Pengaturan Katalog</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-center text-muted py-5">Belum ada produk. Tambahkan produk terlebih dahulu.</div></div>
    @endforelse

    {{ $products->links() }}
@endsection
