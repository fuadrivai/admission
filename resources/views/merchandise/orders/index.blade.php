@extends('main-layout.index')

@section('content-child')
    @php
        $statusLabels = [
            'PENDING' => ['Menunggu Pembayaran', 'bg-warning text-dark'],
            'PAID' => ['Lunas', 'bg-success'],
            'EXPIRED' => ['Kedaluwarsa', 'bg-secondary'],
            'FAILED' => ['Gagal', 'bg-danger'],
        ];
        $fulfillmentLabels = [
            'AWAITING_PAYMENT' => 'Awaiting payment',
            'WAITING_FOR_STOCK' => 'Waiting for stock',
            'PARTIALLY_AVAILABLE' => 'Partially allocated',
            'READY_FOR_FULFILLMENT' => 'Ready to fulfill',
            'FULFILLED' => 'Fulfilled',
            'CANCELLED' => 'Cancelled',
        ];
        $fulfillmentClasses = [
            'WAITING_FOR_STOCK' => 'bg-warning text-dark',
            'PARTIALLY_AVAILABLE' => 'bg-info text-dark',
            'READY_FOR_FULFILLMENT' => 'bg-primary',
            'FULFILLED' => 'bg-success',
        ];
    @endphp

    <div class="row g-3 mb-3">
        @foreach ([
            ['all', 'Semua Pesanan', $stats['all'], 'bi-receipt'],
            ['pending', 'Menunggu Pembayaran', $stats['pending'], 'bi-hourglass-split'],
            ['paid', 'Lunas', $stats['paid'], 'bi-check-circle'],
            ['expired', 'Kedaluwarsa', $stats['expired'], 'bi-clock-history'],
            ['failed', 'Gagal', $stats['failed'], 'bi-x-circle'],
        ] as [$key, $label, $count, $icon])
            <div class="col-6 col-md">
                <a class="text-decoration-none" href="{{ route('merchandise.orders', array_merge(request()->except('page'), ['status' => $key])) }}">
                    <div class="card h-100 mb-0 {{ $filters['status'] === $key ? 'border-primary' : '' }}">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <div>
                                    <div class="small text-muted">{{ $label }}</div>
                                    <div class="fs-4 fw-bold text-dark">{{ number_format($count) }}</div>
                                </div>
                                <i class="bi {{ $icon }} fs-3 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="alert alert-info d-flex align-items-center gap-2">
        <i class="bi bi-box-seam fs-5"></i>
        <span><strong>{{ number_format($stats['held_items']) }} item</strong> sedang dicadangkan untuk invoice yang masih aktif. Stok dilepas otomatis saat invoice kedaluwarsa.</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('merchandise.orders') }}" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="search" name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Cari nomor pesanan, nama, email, telepon, username, atau cabang">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        @foreach (['all' => 'Semua status', 'pending' => 'Menunggu pembayaran', 'paid' => 'Lunas', 'expired' => 'Kedaluwarsa', 'failed' => 'Gagal'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-search"></i> Cari</button>
                    <a class="btn btn-light" href="{{ route('merchandise.orders') }}">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Pesanan</th>
                            <th>Pembeli</th>
                            <th>Cabang</th>
                            <th>Status Pembayaran</th>
                            <th>Stok</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php($statusInfo = $statusLabels[$order->display_payment_status] ?? [$order->display_payment_status, 'bg-secondary'])
                            <tr>
                                <td class="text-nowrap">
                                    <a class="fw-semibold" href="{{ route('merchandise.orders.show', $order->id) }}">{{ $order->code }}</a>
                                    <div class="small text-muted">{{ $order->created_at->format('d M Y H:i') }}</div>
                                    @if ($order->order_type === 'PREORDER')
                                        <span class="badge bg-warning text-dark">Pre-order</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $order->parent_name }}</div>
                                    <div class="small text-muted">{{ $order->email }} · {{ $order->phone }}</div>
                                </td>
                                <td>{{ $order->branch_name }}</td>
                                <td>
                                    <span class="badge {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
                                    @if ($order->display_payment_status === 'PENDING' && $order->expires_at)
                                        <div class="small text-muted mt-1">Batas: {{ $order->expires_at->format('d M Y H:i') }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->order_type === 'PREORDER')
                                        <span class="badge {{ $fulfillmentClasses[$order->fulfillment_status] ?? 'bg-secondary' }}">{{ $fulfillmentLabels[$order->fulfillment_status] ?? $order->fulfillment_status }}</span>
                                    @elseif ($order->stock_is_held)
                                        <span class="badge bg-warning text-dark">Dicadangkan</span>
                                    @elseif ($order->display_payment_status === 'PAID')
                                        <span class="badge bg-success">Keluar</span>
                                    @else
                                        <span class="text-muted">Tidak dicadangkan</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($order->total_items) }}</td>
                                <td class="text-end text-nowrap">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('merchandise.orders.show', $order->id) }}">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-5">Belum ada pesanan yang sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
@endsection
