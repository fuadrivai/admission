@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Riwayat barang masuk berdasarkan informasi dari Finance/Purchasing.</p>
        <a href="{{ route('merchandise.stock-ins.create') }}" class="btn btn-success"><i class="bi bi-box-arrow-in-down"></i> Catat Stock In</a>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($stockIns->isEmpty())
                <div class="text-center text-muted py-5">Belum ada transaksi Stock In.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Tanggal</th><th>No. Transaksi</th><th>Item</th><th>Dicatat oleh</th><th>Catatan</th><th class="text-end">Total Nilai Modal</th></tr></thead>
                        <tbody>
                            @foreach ($stockIns as $stockIn)
                                <tr>
                                    <td>{{ $stockIn->stock_in_date->format('d M Y') }}</td>
                                    <td>#{{ $stockIn->id }}</td>
                                    <td>
                                        {{ number_format($stockIn->items->sum('quantity')) }} pcs
                                        <details class="small mt-1">
                                            <summary>Rincian ({{ $stockIn->items_count }})</summary>
                                            @foreach ($stockIn->items as $item)
                                                <div>{{ $item->variant->product->name }} — {{ $item->variant->color ? $item->variant->color . ' / ' : '' }}{{ $item->variant->name }}:
                                                    {{ number_format($item->quantity) }} × Rp{{ number_format($item->unit_cost, 0, ',', '.') }}</div>
                                            @endforeach
                                        </details>
                                    </td>
                                    <td>{{ $stockIn->creator->name ?? '-' }}</td>
                                    <td>{{ $stockIn->notes ?: '-' }}</td>
                                    <td class="text-end">Rp{{ number_format($stockIn->items->sum(function ($item) { return $item->quantity * $item->unit_cost; }), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $stockIns->links() }}
            @endif
        </div>
    </div>
@endsection
