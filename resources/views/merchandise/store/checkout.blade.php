<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#650d20">
    <title>Checkout - {{ $settings->store_name }}</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <style>
        :root { --maroon:#690f24; --ink:#292329; --muted:#81777a; --line:#f0e9eb; --canvas:#f7f5f5; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--canvas); color:var(--ink); font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; }
        .store-topbar { position:sticky; top:0; z-index:20; min-height:64px; display:flex; align-items:center; background:var(--maroon); color:#fff; box-shadow:0 3px 14px rgba(48,7,19,.16); }
        .store-topbar-inner { width:min(100% - 32px,1160px); min-height:64px; margin:auto; display:flex; align-items:center; }
        .store-brand { min-width:0; display:flex; align-items:center; gap:10px; color:inherit; text-decoration:none; }
        .store-logo-wrap { width:112px; height:48px; padding:4px 7px; flex:0 0 auto; display:grid; place-items:center; overflow:hidden; border-radius:9px; background:#fff; }
        .store-logo { width:100%; height:100%; object-fit:contain; }
        .store-brand-copy { min-width:0; }
        .store-brand-name { display:block; overflow:hidden; font-size:.98rem; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
        .store-brand-caption { display:block; margin-top:3px; color:rgba(255,255,255,.77); font-size:.72rem; }
        .store-cart-link { position:absolute; top:32px; right:max(16px,calc((100vw - 1160px)/2)); transform:translateY(-50%); color:#fff; font-size:1.45rem; line-height:1; text-decoration:none; }
        .store-cart-count { position:absolute; top:-8px; right:-9px; min-width:17px; height:17px; padding:0 4px; display:grid; place-items:center; border:1px solid var(--maroon); border-radius:999px; background:#fff; color:var(--maroon); font-size:.62rem; font-weight:750; }
        .store-main { width:min(100% - 32px,1020px); min-height:70vh; margin:0 auto; padding:20px 0 42px; }
        .store-title { margin:0 0 5px; font-size:1.55rem; font-weight:760; letter-spacing:-.03em; }
        .store-subtitle { margin:0 0 18px; color:var(--muted); font-size:.88rem; }
        .checkout-layout { display:grid; grid-template-columns:minmax(0,1.1fr) minmax(280px,.9fr); gap:16px; align-items:start; }
        .checkout-card { padding:18px; border:1px solid rgba(55,27,35,.055); border-radius:13px; background:#fff; box-shadow:0 3px 12px rgba(45,22,29,.045); }
        .checkout-card h2 { margin:0 0 15px; font-size:1rem; font-weight:740; }
        .checkout-fields { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .checkout-fields .full-width { grid-column:1/-1; }
        .checkout-fields .form-label { margin-bottom:5px; font-size:.81rem; font-weight:680; }
        .checkout-fields .form-control,.checkout-fields .form-select { min-height:42px; border-color:#e7dfe1; border-radius:8px; font-size:.88rem; }
        .checkout-item { display:grid; grid-template-columns:52px minmax(0,1fr) auto; gap:10px; align-items:center; padding:10px 0; border-bottom:1px solid var(--line); }
        .checkout-item:last-of-type { border-bottom:0; }
        .checkout-item img { width:52px; height:52px; border-radius:7px; background:#f1edef; object-fit:cover; }
        .checkout-item-name { font-size:.81rem; font-weight:710; }
        .checkout-item-detail { margin-top:3px; color:var(--muted); font-size:.7rem; }
        .checkout-item-total { color:var(--maroon); font-size:.78rem; font-weight:740; white-space:nowrap; }
        .summary-row { display:flex; justify-content:space-between; gap:10px; margin:9px 0; color:#74696d; font-size:.82rem; }
        .summary-row > :last-child { text-align:right; white-space:nowrap; }
        .checkout-total { display:flex; justify-content:space-between; gap:10px; padding-top:13px; margin-top:8px; border-top:1px solid var(--line); font-size:.95rem; font-weight:780; }
        .checkout-submit { width:100%; min-height:45px; margin-top:13px; border:1px solid var(--maroon); border-radius:8px; background:var(--maroon); color:#fff; font-size:.88rem; font-weight:730; }
        .store-footer { padding:18px 16px calc(18px + env(safe-area-inset-bottom)); border-top:1px solid var(--line); background:#fff; color:#91878a; font-size:.73rem; text-align:center; }
        @media(max-width:700px) {
            .store-topbar-inner { width:calc(100% - 24px); min-height:57px; }
            .store-logo-wrap { width:76px; height:40px; padding:3px 5px; border-radius:7px; }
            .store-brand { gap:8px; }
            .store-brand-name { max-width:160px; font-size:.86rem; }
            .store-brand-caption { font-size:.65rem; }
            .store-cart-link { right:15px; font-size:1.3rem; }
            .store-main { width:calc(100% - 24px); padding:15px 0 28px; }
            .store-title { font-size:1.3rem; }
            .checkout-layout { grid-template-columns:1fr; }
            .checkout-card { padding:14px; }
        }
        @media(max-width:420px) { .checkout-fields { grid-template-columns:1fr; } .checkout-fields .full-width { grid-column:auto; } }
        @media(max-width:360px) { .store-brand-name { max-width:120px; } .store-brand-caption { display:none; } }
    </style>
</head>
<body>
    @include('merchandise.store.partials.header', ['showSearch' => false])
    <main class="store-main">
        <h1 class="store-title">Checkout</h1>
        <p class="store-subtitle">Review your order and enter the parent or guardian information.</p>
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        @if ($items->isEmpty())
            <div class="alert alert-warning">Your cart is empty. <a href="{{ route('merchandise.public-store') }}">Back to store</a>.</div>
        @else
            @php($hasUnavailableItem = $items->contains(function ($item) { return !$item->is_available; }))
            @php($voucherAvailable = collect($promotionOptions)->contains(function ($option) {
                return $option['promotion']->type === 'voucher'
                    && strtoupper((string) $option['promotion']->code) === strtoupper((string) session('merchandise_promotion_code'));
            }))
            @if ($order_type === 'PREORDER')
                <div class="alert alert-warning">
                    <strong>Pre-order:</strong> Full payment is required now. Your order will be allocated when the next Stock In is recorded.
                    Expected availability: {{ $preorder_expected_at ? \Illuminate\Support\Carbon::parse($preorder_expected_at)->format('d M Y') : 'to be confirmed' }}.
                    Pre-orders are checked out separately from ready-stock items, and promotions are not available for pre-orders.
                </div>
            @else
            <section class="checkout-card mb-3">
                <h2 class="mb-2">Promo Code</h2>
                <form method="POST" action="{{ route('merchandise.public-checkout.promotion') }}" class="d-flex flex-wrap gap-2">
                    @csrf
                    <div class="input-group">
                        <input name="code" class="form-control" maxlength="50" placeholder="Enter voucher code" value="{{ old('code', session('merchandise_promotion_code')) }}">
                        <button class="btn btn-outline-primary" type="submit">Apply</button>
                    </div>
                    @if (session('merchandise_promotion_code'))
                        <button class="btn btn-outline-secondary" type="submit" name="clear" value="1">Remove</button>
                    @endif
                </form>
                @if (session('merchandise_promotion_code') && !$voucherAvailable)
                    <div class="text-danger small mt-2">This voucher is invalid or your cart does not meet its requirements. Try another code or remove the voucher.</div>
                @elseif ($promotionQuote['promotion'])
                    <div class="text-success small mt-2">
                        Selected promotion:
                        <strong>{{ $promotionQuote['promotion']->name }}</strong>
                        @if ($promotionQuote['promotion']->code) · {{ $promotionQuote['promotion']->code }} @endif
                    </div>
                @endif
            </section>
            @if (count($promotionOptions) > 1)
                <section class="checkout-card mb-3">
                    <h2 class="mb-2">Choose a Promotion</h2>
                    <p class="text-muted small">The promotion with the highest discount is selected by default. You can choose another promotion; promotions cannot be combined.</p>
                    <form method="POST" action="{{ route('merchandise.public-checkout.promotion') }}">
                        @csrf
                        <select name="promotion_id" class="form-select" aria-label="Choose a promotion" onchange="this.form.submit()">
                            @foreach ($promotionOptions as $option)
                                @php($optionPromotion = $option['promotion'])
                                <option value="{{ $optionPromotion->id }}" {{ (int) $selectedPromotionId === (int) $optionPromotion->id ? 'selected' : '' }}>
                                    {{ $optionPromotion->name }}
                                    @if ($optionPromotion->code) · {{ $optionPromotion->code }} @endif
                                    · {{ $option['discount_amount'] > 0 ? 'Save Rp' . number_format($option['discount_amount'], 0, ',', '.') : 'Free gift' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </section>
            @endif
            @if (!empty($promotionQuote['gift_options']))
                <section class="checkout-card mb-3">
                    <h2 class="mb-2">Choose a Promotional Gift</h2>
                    <p class="text-muted small">Choose one gift. The first available option is selected by default.</p>
                    <form method="POST" action="{{ route('merchandise.public-checkout.promotion') }}">
                        @csrf
                        <input type="hidden" name="promotion_id" value="{{ $selectedPromotionId }}">
                        <select name="gift_variant_id" class="form-select" aria-label="Choose a promotional gift" onchange="this.form.submit()">
                            @foreach ($promotionQuote['gift_options'] as $giftOption)
                                <option value="{{ $giftOption['variant_id'] }}" {{ (string) ($promotionQuote['selected_gift_variant_id'] ?? '') === (string) $giftOption['variant_id'] ? 'selected' : '' }}>
                                    {{ $giftOption['name'] }} · up to {{ $giftOption['quantity'] }} {{ \Illuminate\Support\Str::plural('item', $giftOption['quantity']) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </section>
            @endif
            @endif
            <form method="POST" action="{{ route('merchandise.public-checkout.submit') }}">
                @csrf
                <div class="checkout-layout">
                    <section class="checkout-card">
                        <h2>Parent or Guardian Information</h2>
                        <div class="checkout-fields">
                            <div class="full-width">
                                <label for="parent-name" class="form-label">Parent or guardian name</label>
                                <input id="parent-name" name="parent_name" class="form-control" value="{{ old('parent_name') }}" maxlength="150" required autocomplete="name">
                            </div>
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" class="form-control" type="email" value="{{ old('email') }}" maxlength="150" required autocomplete="email">
                            </div>
                            <div>
                                <label for="phone" class="form-label">Phone number</label>
                                <input id="phone" name="phone" class="form-control" type="tel" value="{{ old('phone') }}" maxlength="40" required autocomplete="tel">
                            </div>
                            <div>
                                <label for="portal-username" class="form-label">Username MHIS Portal</label>
                                <input id="portal-username" name="portal_username" class="form-control" value="{{ old('portal_username') }}" maxlength="100" required autocomplete="username">
                            </div>
                            <div>
                                <label for="branch-id" class="form-label">Payment branch</label>
                                <select id="branch-id" name="branch_id" class="form-select" required>
                                    <option value="">Select a branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (string) old('branch_id') === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="text-muted small mt-3">Your MHIS Portal username is used to identify your merchandise order.</div>
                    </section>

                    <aside class="checkout-card">
                        <h2>Order Summary</h2>
                        @foreach ($items as $item)
                            <div class="checkout-item">
                                <img src="/storage/{{ $item->variant->image_path ?: $item->variant->product->image_path }}" alt="">
                                <div>
                                    <div class="checkout-item-name">{{ $item->variant->product->name }}</div>
                                    <div class="checkout-item-detail">
                                        {{ $item->variant->color ? $item->variant->color . ' / ' : '' }}{{ $item->variant->name }} · {{ $item->quantity }} {{ \Illuminate\Support\Str::plural('item', $item->quantity) }}
                                        @if ($item->variant->is_preorder)
                                            <br>Pre-order · Expected {{ $item->variant->preorder_expected_at ? $item->variant->preorder_expected_at->format('d M Y') : 'to be confirmed' }}
                                        @endif
                                    </div>
                                    @unless ($item->is_available)<div class="text-danger small">Insufficient stock</div>@endunless
                                </div>
                                <div class="checkout-item-total">Rp{{ number_format($item->line_total, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                        @foreach ($promotionQuote['gift_items'] as $giftItem)
                            <div class="checkout-item">
                                <div class="d-flex align-items-center justify-content-center rounded bg-light text-success"><i class="bi bi-gift"></i></div>
                                <div>
                                    <div class="checkout-item-name">{{ $giftItem['name'] }}</div>
                                    <div class="checkout-item-detail">Promotional gift · {{ $giftItem['quantity'] }} {{ \Illuminate\Support\Str::plural('item', $giftItem['quantity']) }}</div>
                                </div>
                                <div class="checkout-item-total">Free</div>
                            </div>
                        @endforeach
                        @php($checkoutItemCount = $totalItems + collect($promotionQuote['gift_items'])->sum('quantity'))
                        <div class="summary-row mt-3"><span>Items</span><strong>{{ $checkoutItemCount }} {{ \Illuminate\Support\Str::plural('item', $checkoutItemCount) }}</strong></div>
                        <div class="summary-row"><span>Subtotal</span><strong>Rp{{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                        @if ($promotionQuote['discount_amount'] > 0)
                            <div class="summary-row text-success"><span>Promotion discount</span><strong>-Rp{{ number_format(min($subtotal, $promotionQuote['discount_amount']), 0, ',', '.') }}</strong></div>
                        @endif
                        <div class="summary-row"><span>Admin fee</span><strong>Rp{{ number_format($bank_charger, 0, ',', '.') }}</strong></div>
                        <div class="checkout-total"><span>Total payment</span><span>Rp{{ number_format(max(0, $subtotal - min($subtotal, $promotionQuote['discount_amount'])) + $bank_charger, 0, ',', '.') }}</span></div>
                        <div class="text-muted small mt-2">
                            Payment is processed securely through Xendit.
                            @if ($order_type === 'PREORDER')
                                This order will wait for incoming stock after payment succeeds.
                            @else
                                Stock is reserved while the invoice is active (up to 24 hours) and deducted only after payment succeeds.
                            @endif
                        </div>
                        <button class="checkout-submit" type="submit" {{ $hasUnavailableItem || $branches->isEmpty() ? 'disabled' : '' }}>
                            Continue to Payment <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                        @if ($branches->isEmpty())<div class="text-danger small mt-2">No payment branches are currently available.</div>@endif
                        <a class="btn btn-light w-100 mt-2" href="{{ route('merchandise.public-cart') }}">Back to Cart</a>
                    </aside>
                </div>
            </form>
        @endif
    </main>
    <footer class="store-footer">&copy; {{ date('Y') }} {{ $settings->store_name }} · Mutiara Harapan Islamic School</footer>
</body>
</html>
