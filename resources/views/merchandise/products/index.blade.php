@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Kelola produk, varian, harga jual, dan batas minimum stok.</p>
        <a href="{{ route('merchandise.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Produk</th><th>Kategori</th><th>Varian</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($product->image_path)
                                            <img src="/storage/{{ $product->image_path }}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px">
                                        @endif
                                        <div><div class="fw-semibold">{{ $product->name }}</div><small class="text-muted">{{ $product->description }}</small></div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>
                                    @foreach ($product->variants as $variant)
                                        <div class="mb-1">
                                            <span class="fw-semibold">{{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}</span>
                                            @if ($variant->sku)<small class="text-muted">({{ $variant->sku }})</small>@endif
                                            <span class="text-muted">Â· Rp{{ number_format($variant->price, 0, ',', '.') }}</span>
                                            @if ($variant->is_preorder)
                                                <span class="badge bg-warning text-dark">Pre-order Â· {{ $variant->preorder_expected_at ? $variant->preorder_expected_at->format('d M Y') : 'ETA not set' }}</span>
                                            @endif
                                            @unless ($variant->is_active)<span class="badge bg-light-secondary text-secondary">Nonaktif</span>@endunless
                                        </div>
                                    @endforeach
                                </td>
                                <td><span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('merchandise.products.edit', $product->id) }}">Ubah</a>
                                    <form method="POST" action="{{ route('merchandise.products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Hapus produk ini? Produk dengan riwayat stok harus dinonaktifkan.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada produk. Tambahkan kategori terlebih dahulu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
