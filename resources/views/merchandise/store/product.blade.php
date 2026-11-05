<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#650d20">
    <title>{{ $product->name }} - {{ $settings->store_name }}</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <style>
        :root { --maroon:#690f24; --maroon-dark:#4b0919; --ink:#292329; --muted:#81777a; --line:#f0e9eb; --canvas:#f7f5f5; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--canvas); color:var(--ink); font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; -webkit-font-smoothing:antialiased; }
        .store-topbar { position:sticky; top:0; z-index:20; min-height:64px; display:flex; align-items:center; background:var(--maroon); color:#fff; box-shadow:0 3px 14px rgba(48,7,19,.16); }
        .store-topbar-inner { width:min(100% - 32px,1160px); min-height:64px; margin:auto; display:flex; align-items:center; }
        .store-brand { min-width:0; display:flex; align-items:center; gap:10px; color:inherit; text-decoration:none; }
        .store-logo-wrap { width:112px; height:48px; padding:4px 7px; flex:0 0 auto; display:grid; place-items:center; overflow:hidden; border-radius:9px; background:#fff; }
        .store-logo { width:100%; height:100%; object-fit:contain; }
        .store-brand-copy { min-width:0; }
        .store-brand-name { display:block; overflow:hidden; font-size:.98rem; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
        .store-brand-caption { display:block; margin-top:3px; color:rgba(255,255,255,.77); font-size:.72rem; }
        .store-cart-link { position:absolute; right:max(16px,calc((100vw - 1160px)/2)); color:#fff; font-size:1.45rem; line-height:1; text-decoration:none; }
        .store-cart-count { position:absolute; top:-8px; right:-9px; min-width:17px; height:17px; padding:0 4px; display:grid; place-items:center; border:1px solid var(--maroon); border-radius:999px; background:#fff; color:var(--maroon); font-size:.62rem; font-weight:750; }
        .store-main { width:min(100% - 32px,1080px); min-height:70vh; margin:0 auto; padding:20px 0 42px; }
        .store-back { display:inline-flex; align-items:center; gap:7px; margin-bottom:15px; color:var(--maroon); font-size:.86rem; font-weight:650; text-decoration:none; }
        .product-panel { display:grid; grid-template-columns:minmax(0,1.05fr) minmax(0,.95fr); gap:28px; padding:22px; border:1px solid rgba(55,27,35,.055); border-radius:15px; background:#fff; box-shadow:0 3px 14px rgba(45,22,29,.045); }
        .product-gallery { min-width:0; }
        .product-main-image-wrap { overflow:hidden; aspect-ratio:1/1; border-radius:12px; background:#f1edef; }
        .product-main-image { width:100%; height:100%; display:block; object-fit:contain; }
        .product-thumbnails { display:flex; gap:9px; overflow-x:auto; padding:12px 1px 2px; scrollbar-width:thin; }
        .product-thumb { width:64px; height:64px; flex:0 0 auto; padding:2px; overflow:hidden; border:1px solid #e8dfe2; border-radius:8px; background:#fff; cursor:pointer; }
        .product-thumb.is-active { border:2px solid var(--maroon); }
        .product-thumb img { width:100%; height:100%; object-fit:cover; border-radius:5px; }
        .product-info { min-width:0; }
        .product-category { display:inline-block; padding:5px 9px; border-radius:999px; background:#fbf0f2; color:var(--maroon); font-size:.7rem; font-weight:730; }
        .product-info h1 { margin:11px 0 7px; font-size:clamp(1.35rem,3vw,1.85rem); font-weight:760; letter-spacing:-.03em; }
        .product-price { margin-bottom:17px; color:var(--maroon); font-size:1.3rem; font-weight:790; }
        .product-description { padding:14px 0; border-top:1px solid var(--line); border-bottom:1px solid var(--line); color:#655b5e; font-size:.9rem; line-height:1.65; white-space:pre-line; }
        .product-form { margin-top:18px; }
        .product-form label { margin-bottom:7px; font-size:.84rem; font-weight:700; }
        .product-form .form-select,.product-form .form-control { min-height:43px; border-color:#e7dfe1; border-radius:8px; }
        .product-stock { margin:10px 0 15px; color:#74696d; font-size:.8rem; }
        .product-actions { display:grid; grid-template-columns:1fr 1fr; gap:9px; }
        .btn-maroon { min-height:44px; border:1px solid var(--maroon); border-radius:8px; background:var(--maroon); color:#fff; font-size:.84rem; font-weight:700; }
        .btn-outline-maroon { min-height:44px; border:1px solid var(--maroon); border-radius:8px; background:#fff; color:var(--maroon); font-size:.84rem; font-weight:700; }
        .btn-maroon:disabled,.btn-outline-maroon:disabled { opacity:.55; }
        body { min-height:100vh; min-height:100dvh; display:flex; flex-direction:column; }
        .store-footer { margin-top:auto; padding:18px 16px calc(18px + env(safe-area-inset-bottom)); border-top:1px solid var(--line); background:#fff; color:#91878a; font-size:.73rem; text-align:center; }
        @media(max-width:700px) {
            .store-topbar-inner { width:calc(100% - 24px); min-height:57px; }
            .store-logo-wrap { width:76px; height:40px; padding:3px 5px; border-radius:7px; }
            .store-brand { gap:8px; }
            .store-brand-name { max-width:160px; font-size:.86rem; }
            .store-brand-caption { font-size:.65rem; }
            .store-cart-link { right:15px; font-size:1.3rem; }
            .store-main { width:calc(100% - 24px); padding:14px 0 28px; }
            .product-panel { grid-template-columns:1fr; gap:18px; padding:13px; border-radius:12px; }
            .product-main-image-wrap { aspect-ratio:1/.86; }
            .product-info h1 { margin-top:8px; font-size:1.3rem; }
            .product-price { margin-bottom:12px; font-size:1.16rem; }
        }
        @media(max-width:360px) {
            .store-brand-name { max-width:120px; }
            .store-brand-caption { display:none; }
            .product-actions { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    @include('merchandise.store.partials.header', ['showSearch' => false])
    <main class="store-main">
        <a class="store-back" href="{{ route('merchandise.public-store') }}"><i class="bi bi-arrow-left"></i> Back to store</a>
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <section class="product-panel">
            <div class="product-gallery">
                <div class="product-main-image-wrap">
                    <img id="product-main-image" class="product-main-image" src="/storage/{{ $product->image_path }}" alt="{{ $product->name }}">
                </div>
                <div class="product-thumbnails" aria-label="Product image gallery">
                    <button class="product-thumb is-active" type="button" data-image-src="/storage/{{ $product->image_path }}" aria-label="Main image">
                        <img src="/storage/{{ $product->image_path }}" alt="">
                    </button>
                    @foreach ($product->images as $image)
                        <button class="product-thumb" type="button" data-image-src="/storage/{{ $image->image_path }}" aria-label="Other product image">
                            <img src="/storage/{{ $image->image_path }}" alt="">
                        </button>
                    @endforeach
                    @foreach ($variants as $variant)
                        @if ($variant->image_path)
                            <button class="product-thumb" type="button" data-variant-image="{{ $variant->id }}" data-image-src="/storage/{{ $variant->image_path }}" aria-label="Image for {{ $variant->name }}">
                                <img src="/storage/{{ $variant->image_path }}" alt="">
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="product-info">
                <span class="product-category">{{ $product->category->name }}</span>
                <h1>{{ $product->name }}</h1>
                @if ($variants->isNotEmpty())
                    <div class="product-price" id="product-price">Rp{{ number_format($variants->first()->price, 0, ',', '.') }}</div>
                @endif
                <div class="product-description">{{ $product->description ?: 'Official merchandise from Mutiara Harapan Islamic School.' }}</div>
                @if ($settings->is_open && $variants->isNotEmpty())
                    <form class="product-form" method="POST" action="{{ route('merchandise.public-cart.add', ['productId' => $product->id]) }}">
                        @csrf
                        <label for="variant-id" class="form-label">Select a variant</label>
                        <select id="variant-id" name="variant_id" class="form-select" required>
                            @foreach ($variants as $variant)
                                <option value="{{ $variant->id }}"
                                    data-price="{{ number_format($variant->price, 0, ',', '.') }}"
                                    data-stock="{{ $variant->available_stock }}"
                                    data-preorder="{{ $variant->is_preorder ? '1' : '0' }}"
                                    data-expected="{{ $variant->preorder_expected_at ? $variant->preorder_expected_at->format('d M Y') : '' }}"
                                    data-image="{{ $variant->image_path ? '/storage/' . $variant->image_path : '' }}">
                                    {{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}{{ $variant->is_preorder ? ' Â· Pre-order' : '' }} â€” Rp{{ number_format($variant->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        <div class="product-stock" id="product-stock"></div>
                        <label for="quantity" class="form-label">Quantity</label>
                        <input id="quantity" name="quantity" class="form-control" type="number" min="1" value="1" required>
                        <div class="product-actions mt-3">
                            <button class="btn-outline-maroon" type="submit"><i class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                            <button class="btn-maroon" type="submit" name="buy_now" value="1"><i class="bi bi-lightning-fill me-1"></i> Buy Now</button>
                        </div>
                    </form>
                @elseif (!$settings->is_open)
                    <div class="alert alert-warning mt-3 mb-0">{{ $settings->closed_message ?: 'The store is currently closed.' }}</div>
                @else
                    <div class="alert alert-secondary mt-3 mb-0">This product is currently out of stock.</div>
                @endif
            </div>
        </section>
    </main>
    <footer class="store-footer">&copy; {{ date('Y') }} {{ $settings->store_name }} Â· Mutiara Harapan Islamic School</footer>

    @if ($variants->isNotEmpty())
        <script>
            (function () {
                var selector = document.getElementById('variant-id');
                var quantity = document.getElementById('quantity');
                var stockLabel = document.getElementById('product-stock');
                var priceLabel = document.getElementById('product-price');
                var mainImage = document.getElementById('product-main-image');
                var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-image-src]'));
                function updateVariant() {
                    if (!selector) return;
                    var option = selector.options[selector.selectedIndex];
                    var stock = Number(option.getAttribute('data-stock'));
                    var isPreorder = option.getAttribute('data-preorder') === '1';
                    if (isPreorder) {
                        quantity.removeAttribute('max');
                        stockLabel.textContent = 'Pre-order' + (option.getAttribute('data-expected')
                            ? ' Â· Expected availability ' + option.getAttribute('data-expected')
                            : '');
                    } else {
                        quantity.max = stock;
                        if (Number(quantity.value) > stock) quantity.value = stock;
                        stockLabel.textContent = stock + ' available';
                    }
                    priceLabel.textContent = 'Rp' + option.getAttribute('data-price');
                    var image = option.getAttribute('data-image');
                    if (image) {
                        mainImage.src = image;
                        thumbs.forEach(function (thumb) {
                            thumb.classList.toggle('is-active', thumb.getAttribute('data-variant-image') === option.value);
                        });
                    }
                }
                selector.addEventListener('change', updateVariant);
                thumbs.forEach(function (thumb) {
                    thumb.addEventListener('click', function () {
                        mainImage.src = thumb.getAttribute('data-image-src');
                        thumbs.forEach(function (item) { item.classList.toggle('is-active', item === thumb); });
                    });
                });
                updateVariant();
            })();
        </script>
    @endif
</body>
</html>
