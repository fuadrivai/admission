<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="row text-center gy-2">
            <div class="col-6 col-md">
                <div class="small text-muted font-semibold">Total Orders</div>
                <div class="h5 mb-0 fw-bold text-dark">{{ $summary['total'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted font-semibold">Pending / Unpaid</div>
                <div class="h5 mb-0 fw-bold text-warning">{{ $summary['pending'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted font-semibold">Paid</div>
                <div class="h5 mb-0 fw-bold text-success">{{ $summary['paid'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted font-semibold">Expired</div>
                <div class="h5 mb-0 fw-bold text-danger">{{ $summary['expired'] ?? 0 }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted font-semibold">Cancelled</div>
                <div class="h5 mb-0 fw-bold text-secondary">{{ $summary['cancelled'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

@forelse ($orders as $order)
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row align-items-center mb-2 gy-2">
                <div class="col-auto">
                    <div class="student-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-5"
                        style="width: 45px; height: 45px;">
                        {{ strtoupper(substr($order->student_name, 0, 1)) }}
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <span>Code: {{ $order->code }}</span>
                        @php
                            $status = strtoupper($order->payment_status ?? 'UNPAID');
                            $badgeClass = 'bg-warning text-dark';
                            if (in_array($status, ['PAID', 'SETTLED', 'COMPLETED'])) {
                                $badgeClass = 'bg-success text-white';
                            } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
                                $badgeClass = 'bg-danger text-white';
                            } elseif (in_array($status, ['CANCEL', 'CANCELLED'])) {
                                $badgeClass = 'bg-secondary text-white';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 py-1">{{ $status }}</span>
                        @if ($order->picked_up_at)
                            <span class="badge bg-success text-white px-2 py-1">
                                <i class="fa fa-check-circle me-1"></i> Picked Up
                            </span>
                        @else
                            <span class="badge bg-light text-secondary border px-2 py-1">
                                <i class="fa fa-clock-o me-1"></i> Not Picked Up
                            </span>
                        @endif
                    </div>
                    <div class="student-name">{{ $order->student_name }}</div>
                </div>
                <div class="col-md-auto text-md-end">
                    <button type="button" class="btn btn-sm btn-secondary view-order-details me-1"
                        data-id="{{ $order->id }}" data-details="{{ json_encode($order->details) }}"
                        title="Quick View Order Items">
                        <i class="fa fa-folder me-1"></i> Items ({{ $order->total_items }})
                    </button>
                    <a href="/uniform/{{ $order->id }}" class="btn btn-sm btn-primary me-1"
                        title="View Order Detail Page">
                        <i class="fa fa-eye me-1"></i> View Detail
                    </a>
                    @if (!$order->picked_up_at && in_array($status, ['PAID', 'SETTLED', 'COMPLETED']))
                        <button type="button" class="btn btn-sm btn-outline-success confirm-uniform-pickup me-1"
                            data-order="{{ $order->id }}" data-student="{{ e($order->student_name) }}">
                            <i class="fa fa-check me-1"></i> Confirm Pickup
                        </button>
                    @endif
                    @if ($order->order_link && !in_array($status, ['PAID', 'SETTLED', 'COMPLETED']))
                        <a href="{{ $order->order_link }}" target="_blank" class="btn btn-sm btn-success"
                            title="Payment Link">
                            <i class="fa fa-external-link me-1"></i> Pay Now
                        </a>
                    @endif
                </div>
            </div>
            <hr class="my-2 text-muted">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label text-muted small fw-bold">Academic Info</div>
                            <div class="info-value small text-dark">
                                Branch: {{ $order->branch_name ?? ($order->branch->name ?? '-') }}<br>
                                Level / Grade: {{ $order->level_name ?? ($order->level->name ?? '-') }} /
                                {{ $order->grade_name ?? ($order->grade->name ?? '-') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="parent-info">
                        <div class="info-group">
                            <div class="info-label text-muted small fw-bold">Parent / Contact Info</div>
                            <div class="info-value small text-dark">
                                {{ $order->parent_name }}<br>
                                {{ $order->parent_email }}<br>
                                {{ $order->parent_phone }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label text-muted small fw-bold">Order & Payment Info</div>
                            <div class="info-value small text-dark">
                                Order Date: {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}<br>
                                Total Amount: <span class="fw-bold text-success">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</span><br>
                                @if ($order->payment_date)
                                    Payment Date:
                                    {{ \Carbon\Carbon::parse($order->payment_date)->format('d M Y H:i') }}
                                @else
                                    <span class="text-muted italic">Payment Date: -</span>
                                @endif
                                <br>
                                @if ($order->picked_up_at)
                                    <span class="text-success fw-semibold">
                                        <i class="fa fa-check-circle me-1"></i> Picked Up
                                    </span><br>
                                    Picked Up At: {{ $order->picked_up_at->format('d M Y H:i') }}<br>
                                    Picked Up By:
                                    {{ optional($order->pickupUser)->name ?? 'User #' . $order->picked_up_by }}
                                @else
                                    <span class="text-muted italic">Pickup: Not picked up by parent</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card shadow-sm border-0">
        <div class="card-body text-center text-muted py-5">
            <i class="fa fa-info-circle fa-2x mb-2 d-block"></i>
            No uniform orders found matching the filter criteria.
        </div>
    </div>
@endforelse

<div class="mt-3 d-flex justify-content-center">
    {{ $orders->onEachSide(0)->links() }}
</div>
