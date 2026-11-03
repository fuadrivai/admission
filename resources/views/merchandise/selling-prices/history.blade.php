@extends('main-layout.index')

@section('content-child')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1">{{ $variant->product->name }} — {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}</h5>
            <span class="text-muted">{{ $variant->sku ?: 'Tanpa SKU' }} · Harga jual saat ini Rp{{ number_format($variant->price, 0, ',', '.') }}</span>
        </div>
        <a href="{{ route('merchandise.selling-prices') }}" class="btn btn-light-secondary">Kembali ke Selling Prices</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($history->isEmpty())
                <div class="text-center text-muted py-5">Belum ada riwayat harga untuk varian ini.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr><th>Tanggal perubahan</th><th class="text-end">Harga sebelumnya</th><th class="text-end">Harga baru</th><th class="text-end">HPP saat perubahan</th><th>Diubah oleh</th><th>Alasan</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $entry)
                                <tr>
                                    <td>{{ $entry->created_at->format('d M Y H:i') }}</td>
                                    <td class="text-end">{{ $entry->old_price === null ? 'Harga awal' : 'Rp' . number_format($entry->old_price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold">Rp{{ number_format($entry->new_price, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ $entry->average_unit_cost === null ? '-' : 'Rp' . number_format($entry->average_unit_cost, 0, ',', '.') }}</td>
                                    <td>{{ $entry->changer->name ?? '-' }}</td>
                                    <td>{{ $entry->reason ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $history->links() }}
            @endif
        </div>
    </div>
@endsection
