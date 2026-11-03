@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <a href="{{ route('merchandise.stock-opnames') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Stock Opname</a>
            <h4 class="mt-2 mb-0">Stock Opname #{{ $opname->id }}</h4>
            <small class="text-muted">{{ $opname->opname_date->format('d M Y') }} · Dicatat oleh {{ $opname->creator->name ?? '-' }}</small>
        </div>
        <span class="badge bg-success">Sudah disesuaikan</span>
    </div>

    @if ($opname->notes)
        <div class="alert alert-light">{{ $opname->notes }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Rincian Perhitungan</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>Produk</th><th>Varian</th><th class="text-end">Stok sistem</th><th class="text-end">Hitung fisik</th><th class="text-end">Selisih</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($opname->items as $item)
                            <tr>
                                <td>{{ $item->variant->product->name }}</td>
                                <td>{{ $item->variant->color ? $item->variant->color . ' / ' : '' }}{{ $item->variant->name }}</td>
                                <td class="text-end">{{ number_format($item->system_quantity) }} pcs</td>
                                <td class="text-end">{{ number_format($item->counted_quantity) }} pcs</td>
                                <td class="text-end fw-semibold {{ $item->difference > 0 ? 'text-success' : ($item->difference < 0 ? 'text-danger' : 'text-muted') }}">
                                    {{ $item->difference > 0 ? '+' : '' }}{{ number_format($item->difference) }} pcs
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Jumlah perubahan bersih</th>
                            <th class="text-end">{{ $opname->items->sum('difference') > 0 ? '+' : '' }}{{ number_format($opname->items->sum('difference')) }} pcs</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <small class="text-muted">Penyesuaian stok masuk/keluar terhubung ke catatan opname ini.</small>
        </div>
    </div>
@endsection
