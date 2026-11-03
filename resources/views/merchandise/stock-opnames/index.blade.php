@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <p class="text-muted mb-0">Catat jumlah fisik dan sesuaikan selisih stok dengan jejak audit.</p>
        <a href="{{ route('merchandise.stock-opnames.create') }}" class="btn btn-success">
            <i class="bi bi-clipboard-check"></i> Catat Stock Opname
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($opnames->isEmpty())
                <div class="text-center text-muted py-5">Belum ada Stock Opname.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Opname</th>
                                <th>Varian dihitung</th>
                                <th class="text-end">Net penyesuaian</th>
                                <th>Dicatat oleh</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($opnames as $opname)
                                <tr>
                                    <td>{{ $opname->opname_date->format('d M Y') }}</td>
                                    <td><a href="{{ route('merchandise.stock-opnames.show', $opname->id) }}">#{{ $opname->id }}</a></td>
                                    <td>{{ number_format($opname->items_count) }} varian</td>
                                    <td class="text-end {{ $opname->items_sum_difference > 0 ? 'text-success' : ($opname->items_sum_difference < 0 ? 'text-danger' : '') }}">
                                        {{ $opname->items_sum_difference > 0 ? '+' : '' }}{{ number_format($opname->items_sum_difference ?? 0) }} pcs
                                    </td>
                                    <td>{{ $opname->creator->name ?? '-' }}</td>
                                    <td>{{ $opname->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $opnames->links() }}
            @endif
        </div>
    </div>
@endsection
