<header class="store-topbar">
    <div class="store-topbar-inner">
        <a class="store-brand" href="{{ route('merchandise.public-store') }}" aria-label="{{ $settings->store_name }}">
            <span class="store-logo-wrap">
                <img class="store-logo" src="/assets/images/Logo-all-branch.png" alt="Mutiara Harapan Islamic School">
            </span>
            <span class="store-brand-copy">
                <span class="store-brand-name">{{ $settings->store_name }}</span>
                <span class="store-brand-caption">Official school merchandise</span>
            </span>
        </a>
    </div>
    @if (!empty($showSearch) && $settings->is_open)
        <div class="store-searchbar">
            <div class="store-searchbar-inner">
                <label class="store-search">
                    <i class="bi bi-search store-search-icon" aria-hidden="true"></i>
                    <input id="store-search" type="search" placeholder="Search merchandise..." autocomplete="off" aria-label="Search merchandise">
                </label>
                <a class="store-cart-link" href="{{ route('merchandise.public-cart') }}" aria-label="Shopping cart">
                    <i class="bi bi-cart3" aria-hidden="true"></i>
                    @if ($cartCount > 0)<span class="store-cart-count">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif
                </a>
            </div>
        </div>
    @else
        <a class="store-cart-link store-cart-header-link" href="{{ route('merchandise.public-cart') }}" aria-label="Shopping cart">
            <i class="bi bi-cart3" aria-hidden="true"></i>
            @if ($cartCount > 0)<span class="store-cart-count">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif
        </a>
    @endif
</header>
