<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#650d20">
    <meta name="description" content="{{ $settings->description ?: $settings->store_name }}">
    <title>{{ $settings->store_name }}</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <style>
        :root {
            --store-maroon: #690f24;
            --store-maroon-dark: #4b0919;
            --store-maroon-soft: #fbf0f2;
            --store-ink: #292329;
            --store-muted: #81777a;
            --store-line: #f0e9eb;
            --store-canvas: #f7f5f5;
            --store-green: #287c55;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--store-canvas);
            color: var(--store-ink);
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .store-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--store-maroon);
            color: #fff;
            box-shadow: 0 3px 14px rgba(48, 7, 19, .16);
        }
        .store-topbar-inner {
            width: min(100% - 32px, 1160px);
            min-height: 64px;
            margin: 0 auto;
            display: flex;
            align-items: center;
        }
        .store-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            color: inherit;
            text-decoration: none;
        }
        .store-logo-wrap {
            width: 112px;
            height: 48px;
            padding: 4px 7px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            overflow: hidden;
            border-radius: 9px;
            background: #fff;
        }
        .store-logo { display: block; width: 100%; height: 100%; object-fit: contain; }
        .store-brand-copy { min-width: 0; }
        .store-brand-name {
            display: block;
            overflow: hidden;
            font-size: .98rem;
            font-weight: 750;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .store-brand-caption { display: block; margin-top: 3px; color: rgba(255,255,255,.77); font-size: .72rem; }
        .store-searchbar { padding: 0 0 12px; }
        .store-searchbar-inner { width: min(100% - 32px, 1160px); display: flex; align-items: center; gap: 12px; margin: 0 auto; }
        .store-search { position: relative; flex: 1 1 auto; }
        .store-search-icon {
            position: absolute;
            top: 50%;
            left: 13px;
            color: #97898d;
            font-size: 1rem;
            transform: translateY(-50%);
            pointer-events: none;
        }
        .store-search input {
            width: 100%;
            height: 42px;
            padding: 0 14px 0 39px;
            border: 0;
            border-radius: 9px;
            outline: 0;
            background: #fff;
            color: var(--store-ink);
            font-size: .9rem;
        }
        .store-search input:focus { box-shadow: 0 0 0 3px rgba(255,255,255,.27); }
        .store-cart-link { position: relative; flex: 0 0 auto; color: #fff; font-size: 1.45rem; line-height: 1; text-decoration: none; }
        .store-cart-count { position: absolute; top: -8px; right: -9px; min-width: 17px; height: 17px; padding: 0 4px; display: grid; place-items: center; border: 1px solid var(--store-maroon); border-radius: 999px; background: #fff; color: var(--store-maroon); font-size: .62rem; font-weight: 750; line-height: 1; }
        .store-main { width: min(100% - 32px, 1160px); min-height: 70vh; margin: 0 auto; padding: 20px 0 40px; }
        .store-intro { display: flex; justify-content: space-between; align-items: end; gap: 16px; margin-bottom: 18px; }
        .store-intro h1 { margin: 0; color: var(--store-ink); font-size: clamp(1.25rem, 3vw, 1.65rem); font-weight: 760; letter-spacing: -.035em; }
        .store-intro p { margin: 5px 0 0; color: var(--store-muted); font-size: .88rem; }
        .store-open-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            flex: 0 0 auto;
            padding: 7px 11px;
            border: 1px solid #d5ebdf;
            border-radius: 999px;
            background: #f1faf5;
            color: var(--store-green);
            font-size: .76rem;
            font-weight: 700;
        }
        .store-open-dot { width: 7px; height: 7px; border-radius: 50%; background: #36a269; }
        .store-categories { display: flex; gap: 8px; overflow-x: auto; padding: 2px 0 14px; scrollbar-width: none; }
        .store-categories::-webkit-scrollbar { display: none; }
        .store-category-chip {
            flex: 0 0 auto;
            padding: 8px 14px;
            border: 1px solid #e8dfe2;
            border-radius: 999px;
            background: #fff;
            color: #62585b;
            font-size: .81rem;
            font-weight: 650;
            cursor: pointer;
            transition: background .16s ease, color .16s ease, border-color .16s ease;
        }
        .store-category-chip:hover,
        .store-category-chip.is-active { border-color: var(--store-maroon); background: var(--store-maroon); color: #fff; }
        .store-results-bar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: 2px 0 13px; color: var(--store-muted); font-size: .8rem; }
        .store-products-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .store-product-card {
            display: block;
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(55, 27, 35, .055);
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 3px 12px rgba(45, 22, 29, .045);
            transition: transform .18s ease, box-shadow .18s ease;
            color: inherit;
            text-decoration: none;
        }
        .store-product-card:hover { transform: translateY(-2px); box-shadow: 0 9px 24px rgba(45, 22, 29, .09); }
        .store-product-media { position: relative; overflow: hidden; aspect-ratio: 1 / .88; background: #f1edef; }
        .store-product-image { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
        .store-product-card:hover .store-product-image { transform: scale(1.035); }
        .store-placeholder { width: 100%; height: 100%; display: grid; place-items: center; color: #b6a9ad; font-size: 2.8rem; }
        .store-product-category {
            position: absolute;
            top: 10px;
            left: 10px;
            max-width: calc(100% - 20px);
            overflow: hidden;
            padding: 5px 9px;
            border: 1px solid rgba(255,255,255,.75);
            border-radius: 999px;
            background: rgba(255,255,255,.94);
            color: var(--store-maroon);
            font-size: .67rem;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .store-product-body { padding: 12px 13px 14px; }
        .store-product-name { display: -webkit-box; margin: 0; overflow: hidden; color: var(--store-ink); font-size: .98rem; font-weight: 720; line-height: 1.3; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .store-product-price { margin-top: 5px; color: var(--store-maroon); font-size: .87rem; font-weight: 760; }
        .store-empty {
            grid-column: 1 / -1;
            padding: 52px 20px;
            border: 1px dashed #e2d5d9;
            border-radius: 14px;
            background: #fff;
            color: var(--store-muted);
            text-align: center;
        }
        .store-empty-icon { display: block; margin-bottom: 10px; color: #c4aeb5; font-size: 2rem; }
        .store-closed {
            max-width: 560px;
            margin: 32px auto;
            padding: 27px 22px;
            border: 1px solid #f0d9de;
            border-radius: 14px;
            background: #fff;
            text-align: center;
        }
        .store-closed-icon { color: var(--store-maroon); font-size: 2rem; }
        .store-closed h2 { margin: 8px 0; font-size: 1.2rem; font-weight: 750; }
        .store-closed p { margin: 0; color: var(--store-muted); font-size: .9rem; }
        .store-footer { padding: 18px 16px calc(18px + env(safe-area-inset-bottom)); border-top: 1px solid var(--store-line); background: #fff; color: #91878a; font-size: .73rem; text-align: center; }
        .store-pagination { display: flex; justify-content: center; margin-top: 24px; }
        .store-pagination nav { width: 100%; }
        .store-pagination .pagination { justify-content: center; margin-bottom: 0; }
        @media (max-width: 900px) {
            .store-products-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 13px; }
        }
        @media (max-width: 640px) {
            .store-topbar-inner { width: calc(100% - 24px); min-height: 57px; }
            .store-logo-wrap { width: 76px; height: 40px; padding: 3px 5px; border-radius: 7px; }
            .store-brand { gap: 8px; }
            .store-brand-name { max-width: 145px; font-size: .86rem; }
            .store-brand-caption { font-size: .65rem; }
            .store-searchbar { padding: 0 0 10px; }
            .store-searchbar-inner { width: calc(100% - 24px); gap: 14px; }
            .store-search input { height: 38px; padding-left: 34px; font-size: .8rem; }
            .store-search-icon { left: 11px; font-size: .9rem; }
            .store-cart-link { font-size: 1.35rem; }
            .store-main { width: calc(100% - 24px); padding: 16px 0 28px; }
            .store-intro { align-items: center; margin-bottom: 12px; }
            .store-intro h1 { font-size: 1.18rem; }
            .store-intro p { max-width: 250px; font-size: .77rem; }
            .store-open-badge { padding: 6px 9px; font-size: .68rem; }
            .store-category-chip { padding: 7px 12px; font-size: .75rem; }
            .store-products-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
            .store-product-card { border-radius: 11px; }
            .store-product-media { aspect-ratio: 1 / .9; }
            .store-product-body { padding: 9px 10px 11px; }
            .store-product-name { font-size: .86rem; }
            .store-product-price { margin-top: 4px; font-size: .76rem; }
            .store-product-category { top: 7px; left: 7px; padding: 4px 7px; font-size: .6rem; }
            .store-results-bar { font-size: .72rem; }
        }
        @media (max-width: 360px) {
            .store-brand-caption { display: none; }
            .store-brand-name { max-width: 115px; }
            .store-products-grid { gap: 8px; }
            .store-product-body { padding: 9px; }
            .store-product-price { font-size: .7rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    @include('merchandise.store.partials.header', ['showSearch' => true])

    <main class="store-main">
        @unless ($settings->is_open)
            <section class="store-closed">
                <i class="bi bi-shop-window store-closed-icon" aria-hidden="true"></i>
                <h2>The store is currently closed</h2>
                <p>{{ $settings->closed_message ?: 'Please check back later.' }}</p>
            </section>
        @else
            <section class="store-intro">
                <div>
                    <h1>Find your favorite merchandise</h1>
                    <p>{{ $settings->description ?: 'Official merchandise from Mutiara Harapan Islamic School.' }}</p>
                </div>
                <span class="store-open-badge"><span class="store-open-dot"></span> Store open</span>
            </section>

            @php
                $storeCategories = $products->getCollection()
                    ->pluck('category')
                    ->filter()
                    ->unique('id')
                    ->sortBy('display_order')
                    ->values();
                $visibleProductCount = $products->getCollection()
                    ->filter(function ($product) { return $product->variants->isNotEmpty(); })
                    ->count();
            @endphp

            @if ($storeCategories->isNotEmpty())
                <nav class="store-categories" aria-label="Merchandise categories">
                    <button class="store-category-chip is-active" type="button" data-category-filter="all">All</button>
                    @foreach ($storeCategories as $category)
                        <button class="store-category-chip" type="button" data-category-filter="{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </nav>
            @endif

            <div class="store-results-bar">
                <span id="store-result-count">{{ $visibleProductCount }} {{ \Illuminate\Support\Str::plural('product', $visibleProductCount) }}</span>
                @if ($products->hasPages())
                    <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
                @endif
            </div>

            <section class="store-products-grid" id="store-products" aria-label="Merchandise products">
                @forelse ($products as $product)
                    @continue($product->variants->isEmpty())
                    <a class="store-product-card"
                        href="{{ route('merchandise.public-product', ['productId' => $product->id]) }}"
                        data-store-product
                        data-category="{{ $product->category->id }}"
                        data-search="{{ \Illuminate\Support\Str::lower($product->name . ' ' . $product->description . ' ' . $product->category->name . ' ' . $product->variants->map(function ($variant) { return $variant->name . ' ' . $variant->color . ' ' . $variant->sku; })->implode(' ')) }}">
                        <div class="store-product-media">
                            @if ($product->image_path)
                                <img class="store-product-image" src="/storage/{{ $product->image_path }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="store-placeholder" aria-hidden="true"><i class="bi bi-bag"></i></div>
                            @endif
                            <span class="store-product-category">{{ $product->category->name }}</span>
                        </div>
                        <div class="store-product-body">
                            <h2 class="store-product-name">{{ $product->name }}</h2>
                            @if ($product->variants->contains('is_preorder', true))
                                <span class="badge bg-warning text-dark">Pre-order available</span>
                            @endif
                            @php
                                $prices = $product->variants->pluck('price');
                                $minimumPrice = $prices->min();
                                $maximumPrice = $prices->max();
                            @endphp
                            <div class="store-product-price">
                                Rp{{ number_format($minimumPrice, 0, ',', '.') }} - Rp{{ number_format($maximumPrice, 0, ',', '.') }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="store-empty">
                        <i class="bi bi-bag store-empty-icon" aria-hidden="true"></i>
                        <strong>No merchandise is available yet</strong>
                        <div class="small mt-1">Please check back later.</div>
                    </div>
                @endforelse
                <div class="store-empty d-none" id="store-no-results">
                    <i class="bi bi-search store-empty-icon" aria-hidden="true"></i>
                    <strong>No products found</strong>
                    <div class="small mt-1">Try another search term or category.</div>
                </div>
            </section>

            @if ($products->hasPages())
                <div class="store-pagination">{{ $products->links() }}</div>
            @endif
        @endunless
    </main>

    <footer class="store-footer">
        &copy; {{ date('Y') }} {{ $settings->store_name }} · Mutiara Harapan Islamic School
    </footer>

    @if ($settings->is_open)
        <script>
            (function () {
                var searchInput = document.getElementById('store-search');
                var cards = Array.prototype.slice.call(document.querySelectorAll('[data-store-product]'));
                var chips = Array.prototype.slice.call(document.querySelectorAll('[data-category-filter]'));
                var noResults = document.getElementById('store-no-results');
                var resultCount = document.getElementById('store-result-count');
                var activeCategory = 'all';

                function filterProducts() {
                    var query = (searchInput.value || '').trim().toLocaleLowerCase('en');
                    var visibleCount = 0;

                    cards.forEach(function (card) {
                        var matchesCategory = activeCategory === 'all' || card.getAttribute('data-category') === activeCategory;
                        var matchesSearch = !query || (card.getAttribute('data-search') || '').indexOf(query) !== -1;
                        var isVisible = matchesCategory && matchesSearch;
                        card.classList.toggle('d-none', !isVisible);
                        if (isVisible) visibleCount++;
                    });

                    noResults.classList.toggle('d-none', visibleCount > 0 || cards.length === 0);
                    resultCount.textContent = visibleCount + ' ' + (visibleCount === 1 ? 'product' : 'products');
                }

                searchInput.addEventListener('input', filterProducts);
                chips.forEach(function (chip) {
                    chip.addEventListener('click', function () {
                        activeCategory = chip.getAttribute('data-category-filter');
                        chips.forEach(function (item) { item.classList.toggle('is-active', item === chip); });
                        filterProducts();
                    });
                });
            })();
        </script>
    @endif
</body>
</html>
