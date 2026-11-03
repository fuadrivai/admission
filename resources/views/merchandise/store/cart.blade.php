<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#650d20">
    <title>Cart - {{ $settings->store_name }}</title>
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
        .store-main { width:min(100% - 32px,1000px); min-height:70vh; margin:0 auto; padding:20px 0 42px; }
        .store-title { margin:0 0 5px; font-size:1.55rem; font-weight:760; letter-spacing:-.03em; }
        .store-subtitle { margin:0 0 18px; color:var(--muted); font-size:.88rem; }
        .cart-layout { display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:16px; align-items:start; }
        .cart-items,.cart-summary { border:1px solid rgba(55,27,35,.055); border-radius:13px; background:#fff; box-shadow:0 3px 12px rgba(45,22,29,.045); }
        .cart-item { display:grid; grid-template-columns:20px 86px minmax(0,1fr) auto; gap:11px; align-items:center; padding:15px; border-bottom:1px solid var(--line); }
        .cart-item:last-child { border-bottom:0; }
        .cart-item-image { width:86px; height:86px; border-radius:9px; background:#f1edef; object-fit:cover; }
        .cart-item-name { margin:0 0 4px; font-size:.95rem; font-weight:720; }
        .cart-item-select { display:flex; justify-content:center; align-items:center; margin:0; }
        .cart-item-select input { width:16px; height:16px; margin:0; accent-color:var(--maroon); }
        .cart-item-variant { color:var(--muted); font-size:.76rem; }
        .cart-item-price { margin-top:5px; color:var(--maroon); font-size:.84rem; font-weight:750; }
        .cart-item-stock { color:#74696d; font-size:.72rem; }
        .cart-item-tools { display:flex; align-items:center; gap:7px; margin-top:9px; }
        .cart-item-tools input { width:78px; height:35px; border:1px solid #e7dfe1; border-radius:7px; padding:0 8px; }
        .cart-summary { padding:17px; }
        .cart-summary h2 { margin:0 0 14px; font-size:1rem; font-weight:730; }
        .summary-row { display:flex; justify-content:space-between; gap:10px; margin:9px 0; color:#74696d; font-size:.82rem; }
        .summary-row > :last-child { text-align:right; white-space:nowrap; }
        .summary-total { padding-top:12px; margin-top:12px; border-top:1px solid var(--line); color:var(--ink); font-size:.95rem; font-weight:760; }
        .btn-maroon { width:100%; min-height:44px; margin-top:10px; border:1px solid var(--maroon); border-radius:8px; background:var(--maroon); color:#fff; font-size:.88rem; font-weight:720; }
        .store-empty { padding:42px 18px; border:1px dashed #e2d5d9; border-radius:13px; background:#fff; color:var(--muted); text-align:center; }
        .store-empty i { display:block; margin-bottom:9px; color:#c4aeb5; font-size:2rem; }
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
            .cart-layout { grid-template-columns:1fr; }
            .cart-item { grid-template-columns:18px 68px minmax(0,1fr); gap:8px; padding:12px; }
            .cart-item-image { width:68px; height:68px; }
            .cart-item-side { grid-column:3; }
            .cart-summary { order:2; }
        }
        @media(max-width:360px) { .store-brand-name { max-width:120px; } .store-brand-caption { display:none; } }
    </style>
</head>
<body>
    @include('merchandise.store.partials.header', ['showSearch' => false])
    <main class="store-main">
        <h1 class="store-title">Shopping Cart</h1>
        <p class="store-subtitle">Review your merchandise before proceeding to checkout.</p>
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        @if ($items->isEmpty())
            <div class="store-empty">
                <i class="bi bi-cart-x" aria-hidden="true"></i>
                <strong>Your cart is empty</strong>
                <div class="mt-1 mb-3">Find merchandise you love in the store.</div>
                <a class="btn btn-primary" href="{{ route('merchandise.public-store') }}">Browse Products</a>
            </div>
        @else
            <div class="cart-layout">
                <section class="cart-items" aria-label="Items in cart">
                    @foreach ($items as $item)
                        <div class="cart-item">
                            <form method="POST" action="{{ route('merchandise.public-cart.update') }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="items[0][id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[0][quantity]" value="{{ $item->quantity }}">
                                <input type="hidden" name="items[0][is_selected]" value="0">
                                <label class="cart-item-select">
                                    <input type="checkbox" name="items[0][is_selected]" value="1" aria-label="Select {{ $item->variant->product->name }} for purchase" {{ $item->is_selected ? 'checked' : '' }} onchange="this.form.submit()">
                                </label>
                            </form>
                            <img class="cart-item-image" src="/storage/{{ $item->variant->image_path ?: $item->variant->product->image_path }}" alt="{{ $item->variant->product->name }}">
                            <div>
                                <h2 class="cart-item-name">{{ $item->variant->product->name }}</h2>
                                <div class="cart-item-variant">{{ $item->variant->color ? $item->variant->color . ' / ' : '' }}{{ $item->variant->name }}</div>
                                <div class="cart-item-price">Rp{{ number_format($item->variant->price, 0, ',', '.') }} / item</div>
                                <div class="cart-item-stock">Available stock: {{ number_format($item->variant->available_stock) }}</div>
                                @unless ($item->is_available)
                                    <div class="text-danger small mt-1">Quantity exceeds available stock. Adjust it to continue.</div>
                                @endunless
                            </div>
                            <div class="cart-item-side">
                                <form method="POST" action="{{ route('merchandise.public-cart.update') }}" class="cart-item-tools">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="items[0][id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[0][is_selected]" value="{{ $item->is_selected ? 1 : 0 }}">
                                    <input type="number" name="items[0][quantity]" value="{{ $item->quantity }}" min="1" max="{{ max(1, $item->variant->available_stock) }}" aria-label="Quantity of {{ $item->variant->product->name }}" onchange="this.form.submit()">
                                </form>
                                <form method="POST" action="{{ route('merchandise.public-cart.remove', ['itemId' => $item->id]) }}" class="mt-2 text-end">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash me-1"></i>Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </section>
                <aside class="cart-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-row"><span>Items</span><strong>{{ $totalItems }} {{ \Illuminate\Support\Str::plural('item', $totalItems) }}</strong></div>
                    <div class="summary-row"><span>Subtotal</span><strong>Rp{{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                    <div class="summary-row"><span>Admin fee</span><strong>Rp{{ number_format($bank_charger, 0, ',', '.') }}</strong></div>
                    <div class="summary-row summary-total"><span>Total</span><span>Rp{{ number_format($subtotal + $bank_charger, 0, ',', '.') }}</span></div>
                    <div class="text-muted small">Shipping is not included.</div>
                    @if (!$items->contains(function ($item) { return $item->is_selected; }))
                        <button class="btn-maroon" type="button" disabled>Select Items First</button>
                    @elseif ($items->contains(function ($item) { return $item->is_selected && !$item->is_available; }))
                        <button class="btn-maroon" type="button" disabled>Adjust Stock First</button>
                    @else
                        <a class="btn-maroon d-flex justify-content-center align-items-center text-decoration-none" href="{{ route('merchandise.public-checkout') }}">Proceed to Checkout</a>
                    @endif
                    <a class="btn btn-light w-100 mt-2" href="{{ route('merchandise.public-store') }}">Continue Shopping</a>
                </aside>
            </div>
        @endif
    </main>
    <footer class="store-footer">&copy; {{ date('Y') }} {{ $settings->store_name }} · Mutiara Harapan Islamic School</footer>
</body>
</html>
