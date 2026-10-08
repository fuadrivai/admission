@once
    <style>
        .tx-toggle {
            display: flex; align-items: center; justify-content: space-between; width: 100%;
            padding: .55rem .9rem; border: 1px solid #dbe7e1; border-radius: 10px;
            background: #fff; color: #14532d; font-weight: 600; font-size: .9rem;
            transition: background .15s, box-shadow .15s;
        }
        .tx-toggle:hover { background: #f4fbf7; }
        .tx-toggle .tx-count {
            display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px;
            padding: 0 .4rem; margin-left: .45rem; border-radius: 999px; background: #14532d; color: #fff; font-size: .72rem;
        }
        .tx-toggle .tx-chevron { transition: transform .2s; font-size: .75rem; color: #6b7280; }
        .tx-toggle:not(.collapsed) .tx-chevron { transform: rotate(180deg); }
        .tx-card {
            position: relative; margin-top: .75rem; padding: .9rem 1rem .8rem 1.1rem;
            border: 1px solid #e5efe9; border-radius: 12px; background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04); overflow: hidden;
        }
        .tx-card::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 4px; background: var(--tx-color, #94a3b8); }
        .tx-card.paid { --tx-color: #16a34a; }
        .tx-card.pending { --tx-color: #f59e0b; }
        .tx-card.expired { --tx-color: #ef4444; }
        .tx-invoice { font-weight: 700; color: #0f172a; word-break: break-all; }
        .tx-meta { font-size: .78rem; color: #6b7280; }
        .tx-status {
            font-size: .7rem; font-weight: 700; letter-spacing: .4px; padding: .25rem .65rem; border-radius: 999px;
            color: var(--tx-color); background: color-mix(in srgb, var(--tx-color) 12%, #fff);
            border: 1px solid color-mix(in srgb, var(--tx-color) 35%, #fff);
        }
        .tx-lines { margin: .7rem 0 0; padding: 0; list-style: none; }
        .tx-line {
            display: flex; justify-content: space-between; align-items: baseline; gap: .75rem;
            padding: .4rem 0; border-bottom: 1px dashed #e5efe9; font-size: .88rem; color: #0f172a;
        }
        .tx-line .tx-name small { display: block; color: #9ca3af; font-size: .7rem; text-transform: uppercase; letter-spacing: .3px; }
        .tx-line .tx-amt { white-space: nowrap; font-variant-numeric: tabular-nums; text-align: right; }
        .tx-line .tx-amt s { display: block; color: #9ca3af; font-size: .75rem; }
        .tx-line.tx-sub { color: #6b7280; font-size: .82rem; border-bottom: 0; padding: .25rem 0; }
        .tx-line.tx-sub .tx-amt.disc { color: #16a34a; }
        .tx-total {
            display: flex; justify-content: space-between; align-items: center; margin-top: .4rem;
            padding: .55rem .75rem; border-radius: 10px; background: #f0fdf4; color: #14532d; font-weight: 700;
        }
        .tx-foot { display: flex; flex-wrap: wrap; gap: .4rem 1rem; margin-top: .6rem; font-size: .78rem; color: #6b7280; }
        .tx-foot a { color: #0f766e; font-weight: 600; text-decoration: none; }
        .tx-foot a:hover { text-decoration: underline; }
        .tx-legacy { margin-top: .75rem; padding: .75rem 1rem; border: 1px dashed #cbd5e1; border-radius: 12px; font-size: .85rem; color: #6b7280; background: #fff; }
    </style>
@endonce
@php
    $transactions = $enrolment->transactions->sortByDesc('created_at');
    $money = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $collapseId = 'tx-' . $enrolment->id;
@endphp
<div class="enrolment-transactions mt-3">
    <button class="tx-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
        aria-expanded="false" aria-controls="{{ $collapseId }}">
        <span><i class="fa fa-credit-card me-1"></i> Payment Transactions<span class="tx-count">{{ $transactions->count() }}</span></span>
        <i class="fa fa-chevron-down tx-chevron"></i>
    </button>
    <div class="collapse" id="{{ $collapseId }}">
        @forelse ($transactions as $transaction)
            @php
                $status = strtoupper($transaction->payment_status ?? '-');
                $tone = in_array($status, ['PAID', 'SETTLED', 'COMPLETED'], true) ? 'paid' : ($status === 'PENDING' ? 'pending' : 'expired');
            @endphp
            <div class="tx-card {{ $tone }}">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <div class="tx-invoice">{{ $transaction->invoice_id ?? $transaction->code }}</div>
                        <div class="tx-meta">
                            {{ optional($transaction->created_at)->format('d M Y H:i') ?? '-' }}
                            @if ($transaction->payment_place) &middot; {{ $transaction->payment_place }} @endif
                            @if ($transaction->source) &middot; {{ ucfirst($transaction->source) }} @endif
                        </div>
                    </div>
                    <span class="tx-status">{{ $status }}</span>
                </div>

                <ul class="tx-lines">
                    @forelse ($transaction->details as $detail)
                        <li class="tx-line">
                            <span class="tx-name">{{ $detail->description ?? ucfirst($detail->type) }}<small>{{ $detail->type }}</small></span>
                            <span class="tx-amt">
                                @if ((float) $detail->discount > 0)
                                    <s>{{ $money($detail->amount) }}</s>
                                @endif
                                {{ $money($detail->subtotal) }}
                            </span>
                        </li>
                    @empty
                        <li class="tx-line"><span class="text-muted">No item details.</span></li>
                    @endforelse
                    @if ((float) $transaction->discount > 0)
                        <li class="tx-line tx-sub"><span>Discount</span><span class="tx-amt disc">- {{ $money($transaction->discount) }}</span></li>
                    @endif
                    <li class="tx-line tx-sub"><span>Bank charge</span><span class="tx-amt">{{ $money($transaction->bank_charge) }}</span></li>
                </ul>

                <div class="tx-total"><span>Total</span><span>{{ $money($transaction->total_amount) }}</span></div>

                <div class="tx-foot">
                    @if ($transaction->payment_date)
                        <span><i class="fa fa-check-circle text-success"></i> Paid {{ $transaction->payment_date->format('d M Y H:i') }}</span>
                    @elseif ($transaction->expiry_va_date)
                        <span><i class="fa fa-clock-o"></i> Expires {{ $transaction->expiry_va_date->format('d M Y H:i') }}</span>
                    @endif
                    @if ($transaction->payment_url && $tone === 'pending')
                        <a href="{{ $transaction->payment_url }}" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> Payment link</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="tx-legacy">
                No itemised transactions (legacy enrolment).
                @if ((float) $enrolment->amount_paid > 0)
                    Total recorded: <strong>{{ $money($enrolment->amount_paid) }}</strong>
                    ({{ strtoupper($enrolment->payment_status ?? '-') }}).
                @endif
            </div>
        @endforelse
    </div>
</div>