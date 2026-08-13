<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Uniform Collection Monitor</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/extensions/font-awesome/css/font-awesome.min.css">
    <style>
        :root {
            --maroon: #74112b;
            --maroon-dark: #4d091b;
            --maroon-soft: #f8edf0;
            --ink: #29191e;
            --line: #ead9de;
            --gold: #c6a06c;
            --surface: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-width: 320px;
            background: linear-gradient(135deg, #f9f5f6 0%, #f3edef 100%);
            color: var(--ink);
            font-family: Georgia, 'Times New Roman', serif;
        }

        .monitor {
            min-height: 100vh;
        }

        .topbar {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.4rem max(1.25rem, calc((100vw - 1480px) / 2));
            color: #fff !important;
            background: var(--maroon-dark);
            border-bottom: 4px solid var(--gold);
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .15);
        }

        .topbar::after {
            position: absolute;
            top: -80px;
            right: 8%;
            width: 240px;
            height: 240px;
            border: 1px solid rgba(230, 199, 153, .25);
            border-radius: 50%;
            content: '';
        }

        .topbar-brand {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 88px;
            height: 64px;
            padding: .3rem;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .2);
        }

        .topbar-brand img {
            display: block;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .topbar-title {
            position: relative;
            z-index: 1;
        }

        .export-button {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            margin-left: auto;
            padding: .65rem .85rem;
            color: var(--maroon-dark);
            background: #fff;
            border: 1px solid rgba(255, 255, 255, .6);
            border-radius: 4px;
            font: 700 .75rem Arial, sans-serif;
            letter-spacing: .03em;
            text-decoration: none;
            white-space: nowrap;
        }

        .export-button:hover {
            color: #fff;
            background: var(--maroon);
        }

        .eyebrow {
            margin: 0 0 .25rem;
            font-family: Arial, sans-serif;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: #e4c79f;
        }

        .topbar h1 {
            margin: 0;
            font-size: clamp(1.55rem, 2.6vw, 2.4rem);
            font-weight: 700;
            letter-spacing: 0;
            color: #fff !important;
        }

        .topbar h3 {
            margin: 0;
            font-size: clamp(1rem, 1.5vw, 1.5rem);
            font-weight: 600;
            letter-spacing: 0;
            color: #fff !important;
        }

        .topbar p {
            margin: .35rem 0 0;
            font-family: Arial, sans-serif;
            font-size: .9rem;
            color: #f3dfe5 !important;
        }

        .container-monitor {
            max-width: 1480px;
            margin: 0 auto;
            padding: 1.35rem 1.25rem 2.5rem;
        }

        .filter-panel {
            display: grid;
            grid-template-columns: minmax(230px, 2fr) repeat(4, minmax(140px, 1fr));
            gap: .75rem;
            padding: 1.1rem;
            background: var(--surface);
            border: 1px solid #eadce0;
            border-top: 4px solid var(--maroon);
            border-radius: 6px;
            box-shadow: 0 8px 24px rgba(74, 9, 27, .08);
        }

        .field label {
            display: block;
            margin-bottom: .32rem;
            font: 700 .68rem Arial, sans-serif;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6c4c55;
        }

        .field input,
        .field select {
            width: 100%;
            height: 39px;
            padding: .45rem .65rem;
            border: 1px solid #d9c7cd;
            border-radius: 4px;
            background: #fffdfd;
            color: #29191e;
            font: .88rem Arial, sans-serif;
        }

        .field input:focus,
        .field select:focus {
            outline: 2px solid #d9aeba;
            outline-offset: 1px;
            border-color: var(--maroon);
        }

        .reset-filter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            width: 100%;
            height: 39px;
            color: var(--maroon);
            background: var(--maroon-soft);
            border: 1px solid #d9b9c3;
            border-radius: 4px;
            font: 700 .76rem Arial, sans-serif;
            text-decoration: none;
        }

        .reset-filter:hover {
            color: #fff;
            background: var(--maroon);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .75rem;
            margin: 1.2rem 0;
        }

        .stat {
            position: relative;
            overflow: hidden;
            padding: .85rem 1rem;
            background: var(--surface);
            border: 1px solid #eadce0;
            border-left: 4px solid var(--gold);
            border-radius: 5px;
            box-shadow: 0 4px 14px rgba(74, 9, 27, .05);
        }

        .stat:nth-child(2) {
            border-left-color: #369a63;
        }

        .stat:nth-child(3) {
            border-left-color: var(--maroon);
        }

        .stat strong {
            display: block;
            color: var(--maroon);
            font: 700 1.65rem Georgia, serif;
        }

        .stat span {
            font: 700 .68rem Arial, sans-serif;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #765b63;
        }

        .board {
            overflow-x: auto;
            background: var(--surface);
            border: 1px solid #eadce0;
            border-radius: 6px;
            box-shadow: 0 8px 24px rgba(74, 9, 27, .08);
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        thead {
            background: var(--maroon);
            color: #fff;
        }

        th {
            padding: .9rem .8rem;
            text-align: left;
            font: 700 .7rem Arial, sans-serif;
            letter-spacing: .06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        td {
            padding: 1rem .8rem;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
            font-size: .9rem;
        }

        tbody tr.order-row:hover {
            background: #fff8f9;
        }

        .code {
            display: block;
            color: var(--maroon);
            font: 700 .84rem Arial, sans-serif;
        }

        .date,
        .muted {
            display: block;
            margin-top: .22rem;
            color: #806972;
            font: .77rem Arial, sans-serif;
        }

        .student {
            font-size: 1rem;
            font-weight: 700;
        }

        .contact-email {
            color: #68414e;
            font: .77rem Arial, sans-serif;
            overflow-wrap: anywhere;
            text-decoration: none;
        }

        .contact-email:hover {
            color: var(--maroon);
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: .29rem .5rem;
            border-radius: 2px;
            font: 700 .68rem Arial, sans-serif;
            letter-spacing: .04em;
        }

        .paid {
            color: #14532d;
            background: #dcf5e4;
        }

        .pending {
            color: #7a4300;
            background: #fff0cf;
        }

        .failed {
            color: #8d1828;
            background: #fae0e3;
        }

        .neutral {
            color: #4d5560;
            background: #e8eaed;
        }

        .items {
            margin: 0;
            padding: 0;
            list-style: none;
            min-width: 220px;
        }

        .items li {
            padding: .16rem 0;
            border-bottom: 1px dotted #e5d8db;
            font: .8rem Arial, sans-serif;
        }

        .items li:last-child {
            border-bottom: 0;
        }

        .items b {
            color: var(--maroon);
        }

        .pickup {
            white-space: nowrap;
        }

        .pickup-confirmed {
            color: #17683a;
            font: 700 .77rem Arial, sans-serif;
        }

        .pickup-pending {
            color: #87610f;
            font: 700 .77rem Arial, sans-serif;
        }

        .confirm-btn {
            padding: .55rem .72rem;
            border: 0;
            border-radius: 4px;
            background: var(--maroon);
            color: #fff;
            cursor: pointer;
            font: 700 .72rem Arial, sans-serif;
            letter-spacing: .03em;
        }

        .confirm-btn:hover {
            background: var(--maroon-dark);
        }

        .confirm-btn:disabled {
            background: #b9aab0;
            cursor: not-allowed;
        }

        .empty {
            padding: 4rem 1rem;
            text-align: center;
            color: #795e66;
            font-family: Arial, sans-serif;
        }

        .pagination {
            margin: 1.2rem 0 0;
            justify-content: center;
        }

        .page-item.active .page-link {
            background: var(--maroon);
            border-color: var(--maroon);
        }

        .page-link {
            color: var(--maroon);
        }

        @media (max-width: 1000px) {
            .filter-panel {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 760px) {
            .container-monitor {
                padding: 1rem;
            }

            .board {
                overflow: visible;
                border: 0;
                background: transparent;
                box-shadow: none;
            }

            table,
            tbody,
            tr,
            td {
                display: block;
                min-width: 0;
                width: 100%;
            }

            thead {
                display: none;
            }

            tbody {
                display: grid;
                gap: .8rem;
            }

            tbody tr.order-row {
                overflow: hidden;
                background: var(--surface);
                border: 1px solid #eadce0;
                border-top: 4px solid var(--maroon);
                border-radius: 6px;
                box-shadow: 0 5px 15px rgba(74, 9, 27, .06);
            }

            td {
                display: grid;
                grid-template-columns: 105px minmax(0, 1fr);
                gap: .7rem;
                padding: .8rem;
                border-bottom: 1px solid #f0e5e8;
            }

            td:last-child {
                border-bottom: 0;
            }

            td::before {
                color: #765b63;
                content: attr(data-label);
                font: 700 .65rem Arial, sans-serif;
                letter-spacing: .07em;
                text-transform: uppercase;
            }

            .items {
                min-width: 0;
            }

            .pickup {
                white-space: normal;
            }
        }

        @media (max-width: 560px) {

            .filter-panel,
            .stats {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 1.15rem;
            }

            .topbar-brand {
                flex-basis: 70px;
                height: 52px;
            }

            .export-button {
                padding: .55rem .65rem;
                font-size: .7rem;
            }

            .container-monitor {
                padding: .8rem;
            }

            td {
                grid-template-columns: 88px minmax(0, 1fr);
                gap: .5rem;
                padding: .72rem;
            }
        }
        .modal {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease;
}
.modal.show {
    opacity: 1;
    visibility: visible;
}
.modal-content {
    background: rgba(255, 255, 255, 0.9);
    padding: 2rem;
    border-radius: 12px;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    backdrop-filter: blur(6px);
    animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.modal-content h2 {
    margin-top: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}
.modal-close {
    background: transparent;
    border: none;
    font-size: 1.8rem;
    line-height: 1;
    cursor: pointer;
    color: #555;
}
.modal-content .field {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}
.modal-content label {
    margin-bottom: .4rem;
    font-weight: 600;
    color: #333;
}
.modal-content input,
.modal-content textarea {
    width: 100%;
    padding: .6rem .8rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: #fff;
    font-size: 1rem;
    box-sizing: border-box;
}
.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: .8rem;
    margin-top: 1rem;
}
.btn {
    padding: .5rem 1rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
}
.btn-primary {
    background: #4a90e2;
    color: #fff;
}
.btn-primary:hover {
    background: #357ab8;
}
.btn-secondary {
    background: #e0e0e0;
    color: #333;
}
.btn-secondary:hover {
    background: #c5c5c5;
}
    .modal-content h2 {
        margin-top: 0;
    }
    .modal-content .field {
        margin-bottom: 1rem;
    }
    .modal-content label {
        display: block;
        margin-bottom: .3rem;
        font-weight: 600;
    }
    .modal-content input,
    .modal-content textarea {
        width: 100%;
        padding: .4rem .6rem;
        border: 1px solid #d9c7cd;
        border-radius: 4px;
    }
</style>
</head>

<body>
    @php
        $paymentClasses = [
            'PAID' => 'paid',
            'SETTLED' => 'paid',
            'COMPLETED' => 'paid',
            'PENDING' => 'pending',
            'UNPAID' => 'pending',
            'EXPIRED' => 'failed',
            'FAILED' => 'failed',
            'CANCEL' => 'neutral',
            'CANCELLED' => 'neutral',
        ];
        $paidStatuses = ['PAID', 'SETTLED', 'COMPLETED'];
        $selectedBranch = request('branch');
        $branchLevels =
            $selectedBranch && $selectedBranch !== 'all'
                ? optional($branches->firstWhere('id', $selectedBranch))->levels ?? collect()
                : collect();
    @endphp
    <main class="monitor">
        <header class="topbar">
            <div class="topbar-brand">
                <img src="/assets/images/Logo-all-branch.png" alt="Mutiara Harapan Islamic School">
            </div>
            <div class="topbar-title">
                <h3>Admission</h3>
                <h1>Uniform Collection Monitor</h1>
            </div>
            <a class="export-button"
                href="{{ route('uniform.export', request()->only(['search', 'branch', 'level', 'product', 'status', 'order_date_from', 'order_date_to', 'payment_date_from', 'payment_date_to'])) }}">
                <i class="fa fa-file-excel-o"></i> Export Excel
            </a>
        </header>
        <div class="container-monitor">
            <form class="filter-panel" method="GET" action="{{ route('uniform.open') }}">
                <div class="field"><label for="search">Search order</label><input id="search" name="search"
                        value="{{ request('search') }}" placeholder="Code, student, parent, phone"></div>
                <div class="field"><label for="branch">Branch</label><select id="branch" name="branch">
                        <option value="all">All branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label for="level">Level</label><select id="level" name="level"
                        {{ $selectedBranch && $selectedBranch !== 'all' ? '' : 'disabled' }}>
                        <option value="all">All levels</option>
                        @foreach ($branchLevels->sortBy('name') as $level)
                            <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label for="product">Product</label><select id="product" name="product">
                        <option value="all">All products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ request('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label for="status">Payment</label><select id="status" name="status">
                        <option value="all">All statuses</option>
                        @foreach (['PAID', 'PENDING', 'EXPIRED'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label for="order_date_from">Order date from</label><input id="order_date_from"
                        name="order_date_from" type="date" value="{{ request('order_date_from') }}"></div>
                <div class="field"><label for="order_date_to">Order date to</label><input id="order_date_to"
                        name="order_date_to" type="date" value="{{ request('order_date_to') }}"
                        min="{{ request('order_date_from') }}"></div>
                <div class="field"><label for="payment_date_from">Payment date from</label><input
                        id="payment_date_from" name="payment_date_from" type="date"
                        value="{{ request('payment_date_from') }}"></div>
                <div class="field"><label for="payment_date_to">Payment date to</label><input id="payment_date_to"
                        name="payment_date_to" type="date" value="{{ request('payment_date_to') }}"
                        min="{{ request('payment_date_from') }}"></div>
                <div class="field"><label for="reset-filter">Filters</label><a id="reset-filter" class="reset-filter"
                        href="{{ route('uniform.open') }}"><i class="fa fa-refresh"></i> Reset</a></div>
            </form>
            <section class="stats" aria-label="Order totals">
                <div class="stat"><strong>{{ $summary['total'] }}</strong><span>Matching orders</span></div>
                <div class="stat"><strong>{{ $summary['paid'] }}</strong><span>Paid orders</span></div>
                <div class="stat"><strong>{{ $summary['pickedUp'] }}</strong><span>Collected by parents</span></div>
            </section>
            <section class="board">
                @if ($orders->count())
                    <table>
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Student / Parent</th>
                                <th>Branch / Level</th>
                                <th>Uniform order details</th>
                                <th>Payment</th>
                                <th>Collection</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $status = strtoupper($order->payment_status ?: 'UNPAID');
                                    $isPaid = in_array($status, $paidStatuses);
                                @endphp
                                <tr class="order-row" id="order-{{ $order->id }}">
                                    <td data-label="Order"><span class="code">{{ $order->code }}</span><span
                                            class="date">Order date:
                                            {{ $order->order_date ? $order->order_date->format('d M Y') : '-' }}</span>
                                    </td>
                                    <td data-label="Student / Parent">
                                        <div class="student">{{ $order->student_name }}</div><span
                                            class="muted">{{ $order->parent_name }}<br>{{ $order->parent_phone }}<br><a
                                                class="contact-email"
                                                href="mailto:{{ $order->parent_email }}">{{ $order->parent_email }}</a></span>
                                    </td>
                                    <td data-label="Branch / Level">{{ $order->branch_name }}<span
                                            class="muted">{{ $order->level_name }} /
                                            {{ $order->grade_name }}</span></td>
                                    <td data-label="Uniform details">
                                        <ul class="items">
                                            @forelse($order->details as $detail)
                                                <li><b>{{ $detail->product_name }}</b>
                                                    @if ($detail->size)
                                                        ({{ $detail->size }})
                                                    @endif &times;
                                                    {{ rtrim(rtrim(number_format($detail->qty, 2, '.', ''), '0'), '.') }}
                                            </li>@empty<li>No item details</li>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td data-label="Payment"><span
                                            class="badge {{ $paymentClasses[$status] ?? 'neutral' }}">{{ $status }}</span><span
                                            class="muted">Rp
                                            {{ number_format($order->total_amount, 0, ',', '.') }}<br>Payment date:
                                            {{ $order->payment_date ? $order->payment_date->format('d M Y') : '-' }}</span>
                                    </td>
                                    <td class="pickup" data-label="Collection">
                                        @if ($order->picked_up_at)
                                            <span class="pickup-confirmed"><i class="fa fa-check-circle"></i>
                                                Collected</span><span class="muted">Picked up at:
                                                {{ $order->picked_up_at->format('d M Y, H:i') }}<br>Picked up by:
                                                {{ $order->picked_up_name }}</span>
                                        @elseif($isPaid)
                                            <button class="confirm-btn" type="button"
                                                data-order="{{ $order->id }}"
                                                data-name="{{ e($order->student_name) }}"><i class="fa fa-check"></i>
                                            Confirm taken</button>@else<span class="pickup-pending">Awaiting
                                                payment</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No uniform orders match the current filters.</div>
                @endif
            </section>
            {{ $orders->links() }}
        </div>
    </main>
    <!-- Pickup Modal -->
    <div id="pickup-modal" class="modal" style="display:none;">
        
        <div class="modal-content">
            <h2>Confirm Pickup <button class="modal-close" type="button" aria-label="Close">&times;</button></h2>
            <div class="field">
                <label for="pic-name">Pic Name</label>
                <input required type="text" id="pic-name" class="input" />
            </div>
            <div class="field">
                <label for="parent-name">Parent Name</label>
                <input required type="text" id="parent-name" class="input" />
            </div>
            <div class="field">
                <label for="pickup-note">Note</label>
                <textarea id="pickup-note" class="textarea"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" id="modal-confirm" class="btn btn-primary">Confirm</button>
                <button type="button" id="modal-cancel" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
    <script>
        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value;
            return element.innerHTML;
        }

        document.getElementById('branch').addEventListener('change', function() {
            document.getElementById('level').value = 'all';
            this.form.submit();
        });
        document.querySelectorAll('.filter-panel select:not(#branch)').forEach(function(element) {
            element.addEventListener('change', function() {
                this.form.submit();
            });
        });
        document.querySelectorAll('#order_date_to, #payment_date_to').forEach(function(element) {
            element.addEventListener('change', function() {
                this.form.submit();
            });
        });
        [
            ['order_date_from', 'order_date_to'],
            ['payment_date_from', 'payment_date_to']
        ].forEach(function(dateRange) {
            const fromInput = document.getElementById(dateRange[0]);
            const toInput = document.getElementById(dateRange[1]);

            fromInput.addEventListener('change', function() {
                toInput.min = this.value;
                if (toInput.value && toInput.value < this.value) {
                    toInput.value = '';
                }
                this.form.submit();
            });
        });
        let searchTimer;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(searchTimer);
            const form = this.form;
            searchTimer = setTimeout(function() {
                form.submit();
            }, 450);
        });
		
        // Open modal for pickup confirmation
        document.querySelectorAll('.confirm-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const orderId = this.dataset.order;
                const studentName = this.dataset.name;

                // Store reference to button for later UI update
                const buttonElement = this;

                // Show modal
                const modal = document.getElementById('pickup-modal');
                modal.classList.add('show');
                modal.style.display = 'flex';

                // Clear previous inputs
                modal.querySelector('#pic-name').value = '';
                modal.querySelector('#parent-name').value = '';
                modal.querySelector('#pickup-note').value = '';

                // Handle confirm inside modal
                const confirmBtn = modal.querySelector('#modal-confirm');
                const cancelBtn = modal.querySelector('#modal-cancel');

                // Ensure no duplicate listeners
                confirmBtn.replaceWith(confirmBtn.cloneNode(true));
                cancelBtn.replaceWith(cancelBtn.cloneNode(true));
                const newConfirmBtn = modal.querySelector('#modal-confirm');
                const newCancelBtn = modal.querySelector('#modal-cancel');

                newConfirmBtn.addEventListener('click', function() {
                    const picName = modal.querySelector('#pic-name').value.trim();
                    const parentName = modal.querySelector('#parent-name').value.trim();
                    const note = modal.querySelector('#pickup-note').value.trim();

                    if(picName=="" || picName == null){
                        return alert('PIC name or Parent`s name cannot be empty!')
                    }

                    if(parentName=="" || parentName == null){
                        return alert('PIC name or Parent`s name cannot be empty!')
                    }

                    buttonElement.disabled = true;
                    fetch('/uniform/' + orderId + '/pickup', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            pic_name: picName,
                            parent_name: parentName,
                            note: note
                        })
                    }).then(function(response) {
                        return response.json().then(function(data) {
                            if (!response.ok) throw new Error(data.message);
                            return data;
                        });
                    }).then(function(data) {
                        buttonElement.parentElement.innerHTML =
                            '<span class="pickup-confirmed"><i class="fa fa-check-circle"></i> Collected</span>' +
                            '<span class="muted">Picked up at: ' + escapeHtml(data.picked_up_at) + '<br>Picked up by: ' + escapeHtml(data.picked_up_name) + '</span>';
                    }).catch(function(error) {
                        buttonElement.disabled = false;
                        window.alert(error.message || 'Unable to confirm collection.');
                    }).finally(function() {
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                    });
                });

                newCancelBtn.addEventListener('click', function() {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                });
            });
        });
    </script>
</body>

</html>
