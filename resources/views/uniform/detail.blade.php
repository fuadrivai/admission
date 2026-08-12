@extends('main-layout.index')

@section('content-style')
    <style>
        .uniform-detail-page {
            --u-primary: #1e3a8a;
            --u-primary-soft: #eff6ff;
            --u-border: #e2e8f0;
            --u-text-muted: #64748b;
        }

        .uniform-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--u-border);
            border-radius: 14px;
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 50%, #e0f2fe 100%);
            box-shadow: 0 10px 24px rgba(30, 58, 138, 0.06);
            margin-bottom: 1.5rem;
        }

        .uniform-hero .card-body {
            position: relative;
            z-index: 1;
        }

        .student-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1.3rem;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
        }

        .summary-card {
            border: 1px solid var(--u-border);
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
        }

        .summary-card .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--u-border);
            padding: 1rem 1.25rem;
        }

        .info-label {
            font-size: 0.78rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--u-text-muted);
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
        }
    </style>
@endsection

@section('content-child')
    <div class="uniform-detail-page">
        <!-- Top Back Navigation -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="/uniform" class="btn btn-outline-secondary fw-semibold">
                <i class="fa fa-arrow-left me-1"></i> Back to Uniform List
            </a>
            <div class="d-flex gap-2">
                @if ($order->order_link && !in_array(strtoupper($order->payment_status), ['PAID', 'SETTLED', 'COMPLETED']))
                    <a href="{{ $order->order_link }}" target="_blank" class="btn btn-success fw-bold">
                        <i class="fa fa-credit-card me-1"></i> Pay Now (Xendit Invoice)
                    </a>
                @endif
                @if (!$order->picked_up_at && in_array(strtoupper($order->payment_status), ['PAID', 'SETTLED', 'COMPLETED']))
                    <button type="button" id="confirm-pickup" class="btn btn-success fw-bold"
                        data-order="{{ $order->id }}" data-student="{{ e($order->student_name) }}">
                        <i class="fa fa-check me-1"></i> Confirm Pickup
                    </button>
                @endif
            </div>
        </div>

        <!-- Hero Header -->
        <div class="card uniform-hero border-0">
            <div class="card-body p-4">
                <div class="row align-items-center gy-3">
                    <div class="col-auto">
                        <div class="student-chip">
                            {{ strtoupper(substr($order->student_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                            <span class="badge bg-secondary px-3 py-1">Order Code: {{ $order->code }}</span>
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
                            <span class="badge {{ $badgeClass }} px-3 py-1">{{ $status }}</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $order->student_name }}</h3>
                        <p class="text-muted mb-0 small">
                            <i class="fa fa-building me-1"></i> {{ $order->branch_name ?? ($order->branch->name ?? '-') }}
                            &bull;
                            <i class="fa fa-graduation-cap me-1"></i>
                            {{ $order->level_name ?? ($order->level->name ?? '-') }}
                            / {{ $order->grade_name ?? ($order->grade->name ?? '-') }}
                        </p>
                    </div>
                    <div class="col-md-auto text-md-end">
                        <div class="small text-muted mb-1">Total Order Amount</div>
                        <div class="h3 fw-bold text-success mb-0">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Cards Grid -->
        <div class="row g-3 mb-4">
            <!-- Student & Academic Info -->
            <div class="col-md-4">
                <div class="card summary-card h-100 mb-0">
                    <div class="card-header">
                        <h6 class="fw-bold text-dark mb-0"><i class="fa fa-user me-2 text-primary"></i>Student & Academic
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <div class="info-label">Student Name</div>
                            <div class="info-value">{{ $order->student_name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Branch</div>
                            <div class="info-value">{{ $order->branch_name ?? ($order->branch->name ?? '-') }}</div>
                        </div>
                        <div class="mb-0">
                            <div class="info-label">Level / Grade</div>
                            <div class="info-value">{{ $order->level_name ?? ($order->level->name ?? '-') }} /
                                {{ $order->grade_name ?? ($order->grade->name ?? '-') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent & Contact Info -->
            <div class="col-md-4">
                <div class="card summary-card h-100 mb-0">
                    <div class="card-header">
                        <h6 class="fw-bold text-dark mb-0"><i class="fa fa-users me-2 text-primary"></i>Parent / Contact
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <div class="info-label">Parent Name</div>
                            <div class="info-value">{{ $order->parent_name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">{{ $order->parent_email }}</div>
                        </div>
                        <div class="mb-0">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">{{ $order->parent_phone }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order & Payment Status Info -->
            <div class="col-md-4">
                <div class="card summary-card h-100 mb-0">
                    <div class="card-header">
                        <h6 class="fw-bold text-dark mb-0"><i class="fa fa-receipt me-2 text-primary"></i>Order & Payment
                            Status</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <div class="info-label">Order Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Payment Status</div>
                            <div class="info-value">
                                <span class="badge {{ $badgeClass }} px-2 py-1">{{ $status }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Payment Date</div>
                            <div class="info-value">
                                {{ $order->payment_date ? \Carbon\Carbon::parse($order->payment_date)->format('d M Y H:i') : 'Not paid yet' }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Picked Up At</div>
                            <div class="info-value {{ $order->picked_up_at ? 'text-success' : 'text-muted' }}">
                                {{ $order->picked_up_at ? $order->picked_up_at->format('d M Y H:i') : 'Not collected yet' }}
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="info-label">Picked Up By</div>
                            <div class="info-value {{ $order->picked_up_at ? '' : 'text-muted' }}">
                                {{ $order->picked_up_at ? optional($order->pickupUser)->name ?? 'User #' . $order->picked_up_by : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ordered Items Table Card -->
        <div class="card summary-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa fa-list-check me-2 text-primary"></i> Uniform Order Items ({{ $order->total_items }}
                    items)
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Item Name</th>
                                <th class="text-center">Code</th>
                                <th class="text-center">Size</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->details as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                        <small class="text-muted">Type:
                                            {{ strtoupper($item->unit_type ?? 'PCS') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $item->product_code }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->size)
                                            <span class="badge bg-primary px-3 py-1">{{ $item->size }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        {{ $item->qty }}
                                    </td>
                                    <td class="text-end font-monospace">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-primary font-monospace">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No order items recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Items Subtotal:</td>
                                <td class="text-end pe-4 fw-bold text-dark">Rp
                                    {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if ($order->bank_charger > 0)
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Bank Charge:</td>
                                    <td class="text-end pe-4 fw-bold text-dark">Rp
                                        {{ number_format($order->bank_charger, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="5" class="text-end fw-bold fs-6 text-dark">Grand Total Amount:</td>
                                <td class="text-end pe-4 fw-bold fs-5 text-success">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content-script')
    <script>
        const confirmPickupButton = document.getElementById('confirm-pickup');

        if (confirmPickupButton) {
            confirmPickupButton.addEventListener('click', function() {
                const studentName = this.dataset.student;

                if (!window.confirm(`Confirm that the uniform has been collected by ${studentName}?`)) {
                    return;
                }

                const button = this;
                button.disabled = true;

                fetch(`/uniform/${button.dataset.order}/pickup`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(function(response) {
                    return response.json().then(function(data) {
                        if (!response.ok) {
                            throw new Error(data.message);
                        }

                        return data;
                    });
                }).then(function() {
                    window.location.reload();
                }).catch(function(error) {
                    button.disabled = false;
                    window.alert(error.message || 'Unable to confirm pickup.');
                });
            });
        }
    </script>
@endsection
