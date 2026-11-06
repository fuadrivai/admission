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
                                                <div class="mb-1">
                                                    {{ $item->variant->product->name }} — {{ $item->variant->color ? $item->variant->color . ' / ' : '' }}{{ $item->variant->name }}:
                                                    {{ number_format($item->quantity) }} × Rp{{ number_format($item->unit_cost, 0, ',', '.') }}
                                                    @if ($item->costCorrections->isNotEmpty())
                                                        <span class="badge bg-info text-dark" title="Harga modal pernah dikoreksi">Dikoreksi</span>
                                                    @endif
                                                    @if (auth()->user()->role === 'admin')
                                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1" data-bs-toggle="modal" data-bs-target="#cost-modal-{{ $item->id }}">Koreksi modal</button>
                                                    @endif
                                                    @foreach ($item->costCorrections as $correction)
                                                        <div class="text-muted">
                                                            {{ $correction->created_at->format('d M Y H:i') }}: Rp{{ number_format($correction->old_unit_cost, 0, ',', '.') }} → Rp{{ number_format($correction->new_unit_cost, 0, ',', '.') }}
                                                            oleh {{ $correction->corrector->name ?? '-' }} ({{ $correction->reason }})
                                                        </div>
                                                    @endforeach
                                                </div>
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
                @if (auth()->user()->role === 'admin')
                    @foreach ($stockIns as $stockIn)
                        @foreach ($stockIn->items as $item)
                            <div class="modal fade" id="cost-modal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('merchandise.stock-ins.correct-cost', $item->id) }}" class="modal-content">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Koreksi Harga Modal</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="small text-muted mb-3">
                                                Stock In #{{ $stockIn->id }} — {{ $item->variant->product->name }} ({{ $item->variant->color ? $item->variant->color . ' / ' : '' }}{{ $item->variant->name }}), {{ number_format($item->quantity) }} pcs.
                                                Koreksi memengaruhi nilai stok dan rata-rata modal ke depan. Data stok keluar yang sudah tercatat tidak berubah.
                                            </p>
                                            <div class="mb-3">
                                                <label class="form-label">Harga modal per unit (Rp)</label>
                                                <input type="number" name="unit_cost" class="form-control" min="0" step="0.01" required value="{{ $item->unit_cost }}">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Alasan koreksi</label>
                                                <input type="text" name="reason" class="form-control" maxlength="255" required placeholder="Contoh: salah input harga">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Koreksi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif
            @endif
        </div>
    </div>
@endsection
