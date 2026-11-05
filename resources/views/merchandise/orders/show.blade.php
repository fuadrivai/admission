@extends('main-layout.index')

@section('content-child')
    @php
        $statusLabels = [
            'PENDING' => ['Menunggu Pembayaran', 'bg-warning text-dark'],
            'PAID' => ['Lunas', 'bg-success'],
            'EXPIRED' => ['Kedaluwarsa', 'bg-secondary'],
            'FAILED' => ['Gagal', 'bg-danger'],
        ];
        $statusInfo = $statusLabels[$order->display_payment_status] ?? [$order->display_payment_status, 'bg-secondary'];
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <a href="{{ route('merchandise.orders') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke pesanan</a>
            <h4 class="mt-2 mb-0">{{ $order->code }}</h4>
            <small class="text-muted">Dibuat {{ $order->created_at->format('d M Y H:i') }}</small>
        </div>
        <span class="badge fs-6 {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
    </div>

    @if ($order->order_type === 'PREORDER' && $order->display_payment_status === 'PENDING')
        <div class="alert alert-warning">
            Pre-order invoice is pending payment. Expected availability: {{ $order->preorder_expected_at ? $order->preorder_expected_at->format('d M Y') : 'to be confirmed' }}.
            No physical stock is reserved before payment.
        </div>
    @elseif ($order->order_type === 'PREORDER' && $order->display_payment_status === 'PAID')
        <div class="alert {{ $order->fulfillment_status === 'FULFILLED' ? 'alert-success' : 'alert-info' }}">
            <i class="bi bi-box-seam"></i>
            Pre-order · Expected availability {{ $order->preorder_expected_at ? $order->preorder_expected_at->format('d M Y') : 'to be confirmed' }}.
            Fulfillment status: {{ str_replace('_', ' ', ucfirst(strtolower($order->fulfillment_status))) }}.
            @if ($order->fulfilled_at) Fulfilled {{ $order->fulfilled_at->format('d M Y H:i') }}; stock was recorded out then.
            @else Stock will be recorded out when the order is fulfilled. @endif
        </div>
    @elseif ($order->stock_is_held)
        <div class="alert alert-warning"><i class="bi bi-hourglass-split"></i> Stok sedang dicadangkan sampai {{ $order->expires_at ? $order->expires_at->format('d M Y H:i') : 'invoice kedaluwarsa' }}.</div>
    @elseif ($order->display_payment_status === 'PAID')
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> Pembayaran berhasil{{ $order->paid_at ? ' pada ' . $order->paid_at->format('d M Y H:i') : '' }}. Stok telah dicatat keluar.</div>
    @elseif ($order->display_payment_status === 'EXPIRED')
        <div class="alert alert-secondary"><i class="bi bi-clock-history"></i> Invoice kedaluwarsa. Cadangan stok sudah dilepas.</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Item Pesanan</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Produk</th><th class="text-end">Harga</th><th class="text-end">Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->product_name }}</div>
                                            <div class="small text-muted">{{ $item->variant_name }}{{ $item->sku ? ' · ' . $item->sku : '' }}</div>
                                            @if ($item->is_preorder)
                                                <span class="badge bg-warning text-dark">Pre-order · {{ $item->preorder_expected_at ? $item->preorder_expected_at->format('d M Y') : 'ETA not set' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-nowrap">{{ $item->unit_price > 0 ? 'Rp' . number_format($item->unit_price, 0, ',', '.') : 'Gratis' }}</td>
                                        <td class="text-end">{{ number_format($item->quantity) }}</td>
                                        <td class="text-end text-nowrap">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2"><span>Total barang</span><strong>{{ number_format($order->total_items) }} item</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</strong></div>
                        @if ($order->promotion_name)
                            <div class="d-flex justify-content-between mb-2"><span>Promo: {{ $order->promotion_name }}{{ $order->promotion_code ? ' (' . $order->promotion_code . ')' : '' }}</span><strong class="text-success">-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</strong></div>
                        @endif
                        <div class="d-flex justify-content-between mb-2"><span>Biaya admin</span><strong>Rp{{ number_format($order->bank_charger, 0, ',', '.') }}</strong></div>
                        <div class="d-flex justify-content-between fs-5 border-top pt-2"><strong>Total pembayaran</strong><strong>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Informasi Pembeli</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Nama</dt><dd class="col-sm-7">{{ $order->parent_name }}</dd>
                        <dt class="col-sm-5">Email</dt><dd class="col-sm-7"><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></dd>
                        <dt class="col-sm-5">Telepon</dt><dd class="col-sm-7"><a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></dd>
                        <dt class="col-sm-5">Username Portal</dt><dd class="col-sm-7">{{ $order->portal_username }}</dd>
                        <dt class="col-sm-5">Cabang</dt><dd class="col-sm-7">{{ $order->branch_name }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Informasi Pembayaran</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status</dt><dd class="col-sm-7"><span class="badge {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span></dd>
                        <dt class="col-sm-5">Nomor Pesanan</dt><dd class="col-sm-7">{{ $order->code }}</dd>
                        <dt class="col-sm-5">Batas invoice</dt><dd class="col-sm-7">{{ $order->expires_at ? $order->expires_at->format('d M Y H:i') : '-' }}</dd>
                        <dt class="col-sm-5">ID eksternal</dt><dd class="col-sm-7 text-break">{{ $order->external_id }}</dd>
                    </dl>
                    @if ($order->payment_url && $order->display_payment_status === 'PENDING' && $order->expires_at && $order->expires_at->isFuture())
                        <a class="btn btn-outline-primary w-100 mt-3" href="{{ $order->payment_url }}" target="_blank" rel="noopener">Buka Invoice Xendit <i class="bi bi-box-arrow-up-right"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
