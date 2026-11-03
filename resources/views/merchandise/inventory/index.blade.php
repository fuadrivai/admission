@extends('main-layout.index')

@section('content-child')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Saldo dihitung dari transaksi Stock In yang tersimpan.</p>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('merchandise.stock-ins.create') }}" class="btn btn-success"><i class="bi bi-box-arrow-in-down"></i> Catat Stock In</a>
        @endif
    </div>
    <div class="card">
        <div class="card-body">
            @if ($inventory->isEmpty())
                <div class="text-center text-muted py-5">Belum ada varian produk.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th><th>Kategori</th><th>Varian</th><th>SKU</th>
                                <th>Status</th><th class="text-end">Stok tersedia</th>
                                @if ($canViewCost)<th class="text-end">Nilai modal masuk</th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventory as $variant)
                                <tr>
                                    <td>{{ $variant->product->name }}</td>
                                    <td>{{ $variant->product->category->name }}</td>
                                    <td>{{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}</td>
                                    <td>{{ $variant->sku ?: '-' }}</td>
                                    <td>
                                        <span class="badge {{ $variant->is_active && $variant->product->is_active && $variant->product->category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $variant->is_active && $variant->product->is_active && $variant->product->category->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($variant->current_stock) }} pcs</td>
                                    @if ($canViewCost)
                                        <td class="text-end">Rp{{ number_format($variant->total_cost, 0, ',', '.') }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $inventory->links() }}
            @endif
        </div>
    </div>
@endsection
