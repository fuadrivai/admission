@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="alert alert-info">
        Pre-orders are paid in full. Stock In is automatically allocated to the oldest paid orders. Stock is recorded out only when an order is marked fulfilled.
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            ['Waiting for stock', $stats['waiting'], 'warning'],
            ['Partially allocated', $stats['partial'], 'info'],
            ['Ready to fulfill', $stats['ready'], 'success'],
            ['Fulfilled', $stats['fulfilled'], 'secondary'],
        ] as [$label, $count, $color])
            <div class="col-6 col-xl-3">
                <div class="card h-100 mb-0">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="fs-4 fw-bold text-{{ $color }}">{{ number_format($count) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Paid Pre-orders</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Expected availability</th>
                            <th>Items and allocation</th>
                            <th>Fulfillment status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $statusLabels = [
                                    'WAITING_FOR_STOCK' => ['Waiting for stock', 'warning text-dark'],
                                    'PARTIALLY_AVAILABLE' => ['Partially allocated', 'info text-dark'],
                                    'READY_FOR_FULFILLMENT' => ['Ready to fulfill', 'success'],
                                    'FULFILLED' => ['Fulfilled', 'secondary'],
                                ];
                                [$statusLabel, $statusClass] = $statusLabels[$order->fulfillment_status] ?? [$order->fulfillment_status, 'secondary'];
                            @endphp
                            <tr>
                                <td>
                                    <a class="fw-semibold" href="{{ route('merchandise.orders.show', $order->id) }}">{{ $order->code }}</a>
                                    <div class="small text-muted">Paid {{ $order->paid_at ? $order->paid_at->format('d M Y H:i') : '-' }}</div>
                                </td>
                                <td>
                                    {{ $order->parent_name }}
                                    <div class="small text-muted">{{ $order->branch_name }} · {{ $order->portal_username }}</div>
                                </td>
                                <td>{{ $order->preorder_expected_at ? $order->preorder_expected_at->format('d M Y') : '-' }}</td>
                                <td>
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach ($order->items as $item)
                                            <li class="mb-1">
                                                {{ $item->product_name }} · {{ $item->variant_name }}
                                                <span class="text-muted">
                                                    ({{ number_format($item->allocated_quantity) }}/{{ number_format($item->quantity) }} allocated)
                                                </span>
                                                @foreach ($item->preorderAllocations as $allocation)
                                                    <div class="text-muted">
                                                        Stock In #{{ $allocation->stockInItem->stock_in_id }} · {{ number_format($allocation->quantity) }} pcs
                                                    </div>
                                                @endforeach
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td><span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td class="text-end text-nowrap">
                                    @if ($order->fulfillment_status === 'READY_FOR_FULFILLMENT')
                                        <form method="POST" action="{{ route('merchandise.preorders.fulfill', $order->id) }}" onsubmit="return confirm('Mark this pre-order as fulfilled? Stock will be recorded out.');">
                                            @csrf
                                            <button class="btn btn-sm btn-primary" type="submit">Mark fulfilled</button>
                                        </form>
                                    @elseif ($order->fulfillment_status === 'FULFILLED')
                                        <span class="small text-muted">{{ $order->fulfilled_at ? $order->fulfilled_at->format('d M Y H:i') : '' }}</span>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" type="button" disabled>Waiting for items</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-muted">There are no paid pre-orders to fulfill.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
@endsection
