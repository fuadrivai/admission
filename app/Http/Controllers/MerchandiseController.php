<?php

namespace App\Http\Controllers;

use App\Models\MerchandiseCategory;
use App\Models\MerchandiseProductImage;
use App\Models\MerchandiseProduct;
use App\Models\MerchandiseVariant;
use App\Services\MerchandiseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MerchandiseController extends Controller
{
    private MerchandiseService $merchandiseService;

    public function __construct(MerchandiseService $merchandiseService)
    {
        $this->merchandiseService = $merchandiseService;
    }

    public function dashboard()
    {
        $data = $this->merchandiseService->dashboard();
        if (auth()->user()->role !== 'admin') {
            unset($data['inventory_value']);
        }

        return view('merchandise.dashboard', array_merge(
            ['title' => 'Merchandise Dashboard'],
            $data
        ));
    }

    public function categories()
    {
        return view('merchandise.categories.index', [
            'title' => 'Product Categories',
            'categories' => $this->merchandiseService->getCategories(),
        ]);
    }

    public function storeSettings()
    {
        $this->ensureCostAccess();

        return view('merchandise.store.settings', [
            'title' => 'Online Store Settings',
            'settings' => $this->merchandiseService->getStoreSettings(),
        ]);
    }

    public function updateStoreSettings(Request $request)
    {
        $this->ensureCostAccess();
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_open' => ['nullable', 'boolean'],
            'closed_message' => ['nullable', 'string', 'max:500'],
        ]);
        $this->merchandiseService->updateStoreSettings($data);

        return redirect()->route('merchandise.store-settings')->with('success', 'Pengaturan Online Store berhasil disimpan.');
    }

    public function catalog()
    {
        $this->ensureCostAccess();

        return view('merchandise.store.catalog', [
            'title' => 'Online Store Catalog',
            'products' => $this->merchandiseService->getCatalogProducts(),
        ]);
    }

    public function updateCatalogProduct(Request $request, $productId)
    {
        $this->ensureCostAccess();
        $data = $request->validate([
            'is_published' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'sale_start_at' => ['nullable', 'date'],
            'sale_end_at' => ['nullable', 'date'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['required', 'integer', 'distinct', 'exists:merchandise_variants,id'],
            'variants.*.online_stock_limit' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
        ]);
        if (!empty($data['sale_start_at']) && !empty($data['sale_end_at'])
            && strtotime($data['sale_end_at']) < strtotime($data['sale_start_at'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'sale_end_at' => 'Periode penjualan berakhir harus setelah waktu mulai.',
            ]);
        }

        $data['product_id'] = $productId;
        $this->merchandiseService->updateCatalogProduct($data);

        return redirect()->route('merchandise.catalog')->with('success', 'Pengaturan katalog produk berhasil disimpan.');
    }

    public function promotions()
    {
        $this->ensureCostAccess();

        return view('merchandise.promotions.index', [
            'title' => 'Promotion Management',
            'promotions' => $this->merchandiseService->getPromotions(),
        ]);
    }

    public function createPromotion()
    {
        $this->ensureCostAccess();

        return view('merchandise.promotions.form', array_merge(
            [
                'title' => 'Create Promotion',
                'promotion' => null,
            ],
            $this->merchandiseService->getPromotionFormData()
        ));
    }

    public function editPromotion($id)
    {
        $this->ensureCostAccess();

        return view('merchandise.promotions.form', array_merge(
            [
                'title' => 'Edit Promotion',
                'promotion' => $this->merchandiseService->showPromotion($id),
            ],
            $this->merchandiseService->getPromotionFormData()
        ));
    }

    public function storePromotion(Request $request)
    {
        $data = $this->validatePromotion($request);
        $this->merchandiseService->postPromotion($data);

        return redirect()->route('merchandise.promotions')->with('success', 'Promo berhasil dibuat.');
    }

    public function updatePromotion(Request $request, $id)
    {
        $data = $this->validatePromotion($request, $id);
        $data['id'] = $id;
        $this->merchandiseService->putPromotion($data);

        return redirect()->route('merchandise.promotions')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroyPromotion($id)
    {
        $this->ensureCostAccess();
        $this->merchandiseService->deletePromotion($id);

        return redirect()->route('merchandise.promotions')->with('success', 'Promo berhasil dihapus.');
    }

    public function orders(Request $request)
    {
        $this->ensureCostAccess();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::in(['all', 'pending', 'paid', 'expired', 'failed'])],
        ]);
        $data = $this->merchandiseService->getOrders($filters);

        return view('merchandise.orders.index', array_merge(
            ['title' => 'Order Management'],
            $data
        ));
    }

    public function showOrder($id)
    {
        $this->ensureCostAccess();

        return view('merchandise.orders.show', [
            'title' => 'Order Details',
            'order' => $this->merchandiseService->showOrder($id),
        ]);
    }

    public function applyPromotion(Request $request)
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:50'],
            'clear' => ['nullable', 'boolean'],
            'gift_variant_id' => ['nullable', 'integer', 'exists:merchandise_variants,id'],
            'promotion_id' => ['nullable', 'integer', 'exists:merchandise_promotions,id'],
        ]);

        if (!empty($data['gift_variant_id'])) {
            $promotionId = (int) ($data['promotion_id'] ?? session('merchandise_selected_promotion_id'));
            $this->merchandiseService->previewPromotionSelection(
                session()->getId(),
                session('merchandise_promotion_code'),
                $promotionId,
                (int) $data['gift_variant_id']
            );
            session(['merchandise_selected_promotion_id' => $promotionId]);
            session(['merchandise_promotion_gift_variant_id' => (int) $data['gift_variant_id']]);

            return redirect()->route('merchandise.public-checkout')->with('success', 'Hadiah promo berhasil dipilih.');
        }

        if (!empty($data['promotion_id'])) {
            $promotionId = (int) $data['promotion_id'];
            $this->merchandiseService->previewPromotionSelection(
                session()->getId(),
                session('merchandise_promotion_code'),
                $promotionId
            );
            session(['merchandise_selected_promotion_id' => $promotionId]);
            session()->forget('merchandise_promotion_gift_variant_id');

            return redirect()->route('merchandise.public-checkout')->with('success', 'Promo berhasil dipilih.');
        }

        if (!empty($data['clear']) || empty(trim($data['code'] ?? ''))) {
            session()->forget('merchandise_promotion_code');
            session()->forget('merchandise_promotion_gift_variant_id');
            session()->forget('merchandise_selected_promotion_id');
            return redirect()->route('merchandise.public-checkout')->with('success', 'Kode promo dihapus.');
        }

        $promotion = $this->merchandiseService->previewPromotion(
            session()->getId(),
            trim($data['code'])
        );
        session(['merchandise_promotion_code' => $promotion->code]);
        session()->forget('merchandise_promotion_gift_variant_id');
        session()->forget('merchandise_selected_promotion_id');

        return redirect()->route('merchandise.public-checkout')
            ->with('success', 'Voucher memenuhi syarat dan ditambahkan ke pilihan promo.');
    }

    public function publicStore()
    {
        return view('merchandise.store.public', array_merge(
            ['title' => 'MHIS Merchandise Store'],
            $this->merchandiseService->getPublicStore(session()->getId())
        ));
    }

    public function publicProduct($productId)
    {
        return view('merchandise.store.product', array_merge(
            ['title' => 'Detail Merchandise'],
            $this->merchandiseService->getPublicProduct($productId, session()->getId())
        ));
    }

    public function cart()
    {
        return view('merchandise.store.cart', array_merge(
            ['title' => 'Keranjang Merchandise'],
            $this->merchandiseService->getCart(session()->getId())
        ));
    }

    public function addToCart(Request $request, $productId)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', Rule::exists('merchandise_variants', 'id')->where('product_id', $productId)],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'buy_now' => ['nullable', 'boolean'],
        ]);
        $this->merchandiseService->addToCart(session()->getId(), $data['variant_id'], $data['quantity']);

        if (!empty($data['buy_now'])) {
            return redirect()->route('merchandise.public-checkout');
        }

        return redirect()->route('merchandise.public-product', ['productId' => $productId])
            ->with('success', 'Produk berhasil dimasukkan ke keranjang.');
    }

    public function updateCart(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:merchandise_cart_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.is_selected' => ['required', 'boolean'],
        ]);
        $this->merchandiseService->updateCart(session()->getId(), $data['items']);

        return redirect()->route('merchandise.public-cart')->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function removeCartItem($itemId)
    {
        $this->merchandiseService->removeCartItem(session()->getId(), $itemId);

        return redirect()->route('merchandise.public-cart')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout()
    {
        $checkoutData = $this->merchandiseService->getCheckout(
            session()->getId(),
            session('merchandise_promotion_code'),
            session('merchandise_promotion_gift_variant_id'),
            session('merchandise_selected_promotion_id')
        );
        if (!empty($checkoutData['selectedPromotionId'])) {
            session(['merchandise_selected_promotion_id' => $checkoutData['selectedPromotionId']]);
        } else {
            session()->forget('merchandise_selected_promotion_id');
        }

        return view('merchandise.store.checkout', array_merge(
            ['title' => 'Konfirmasi Pembelian'],
            $checkoutData
        ));
    }

    public function createPayment(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'parent_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:40'],
            'portal_username' => ['required', 'string', 'max:100'],
        ]);
        $data['promotion_code'] = session('merchandise_promotion_code');
        $data['promotion_gift_variant_id'] = session('merchandise_promotion_gift_variant_id');
        $data['promotion_id'] = session('merchandise_selected_promotion_id');
        $order = $this->merchandiseService->createOrderAndInvoice(session()->getId(), $data);
        session()->forget('merchandise_promotion_code');
        session()->forget('merchandise_promotion_gift_variant_id');
        session()->forget('merchandise_selected_promotion_id');

        return redirect()->away($order->payment_url);
    }

    private function validatePromotion(Request $request, $id = null): array
    {
        $this->ensureCostAccess();
        $request->merge([
            'code' => $request->filled('code') ? strtoupper(trim($request->input('code'))) : null,
        ]);
        $type = $request->input('type');
        $types = ['minimum_purchase', 'quantity_discount', 'buy_x_get_y', 'free_gift', 'bundle', 'voucher'];
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::in($types)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('merchandise_promotions', 'code')->ignore($id)],
            'discount_type' => ['exclude_unless:type,minimum_purchase,quantity_discount,voucher', 'nullable', Rule::in(['percentage', 'fixed'])],
            'discount_value' => ['exclude_unless:type,minimum_purchase,quantity_discount,voucher', 'nullable', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'minimum_purchase' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'min_quantity' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'buy_quantity' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'get_quantity' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'gift_variant_id' => ['nullable', 'integer', 'exists:merchandise_variants,id'],
            'bundle_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'applicable_variants' => ['nullable', 'array'],
            'applicable_variants.*' => ['required', 'integer', 'distinct', 'exists:merchandise_variants,id'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if (in_array($type, ['minimum_purchase', 'quantity_discount', 'voucher'], true)) {
            $rules['discount_type'][] = 'required';
            $rules['discount_value'][] = 'required';
            if ($request->input('discount_type') === 'percentage') {
                $rules['discount_value'][] = 'max:100';
            }
        }
        if ($type === 'minimum_purchase') {
            $rules['minimum_purchase'][] = 'required';
            $rules['minimum_purchase'][] = 'gt:0';
        } elseif ($type === 'quantity_discount') {
            $rules['min_quantity'][] = 'required';
        } elseif ($type === 'buy_x_get_y') {
            $rules['buy_quantity'][] = 'required';
            $rules['get_quantity'][] = 'required';
            $rules['applicable_variants'][] = 'required';
            $rules['applicable_variants'][] = 'array';
            $rules['applicable_variants'][] = 'min:1';
            $rules['gift_options'] = ['required', 'array', 'min:1'];
            $rules['gift_options.*.variant_id'] = ['required', 'integer', 'distinct', 'exists:merchandise_variants,id'];
            $rules['gift_options.*.max_quantity'] = ['required', 'integer', 'min:1', 'max:4294967295'];
        } elseif ($type === 'free_gift') {
            $rules['min_quantity'][] = 'required';
            $rules['get_quantity'][] = 'required';
            $rules['gift_variant_id'][] = 'required';
        } elseif ($type === 'bundle') {
            $rules['bundle_price'][] = 'required';
            $rules['bundle_items'] = ['required', 'array', 'min:2'];
            $rules['bundle_items.*.variant_id'] = ['required', 'integer', 'distinct', 'exists:merchandise_variants,id'];
            $rules['bundle_items.*.quantity'] = ['required', 'integer', 'min:1', 'max:4294967295'];
        } elseif ($type === 'voucher') {
            $rules['code'][] = 'required';
        }

        $data = $request->validate($rules);
        if (!empty($data['valid_from']) && !empty($data['valid_until'])
            && strtotime($data['valid_until']) < strtotime($data['valid_from'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'valid_until' => 'Waktu akhir promo harus setelah waktu mulai.',
            ]);
        }

        return $data;
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:merchandise_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'is_store_visible' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $this->merchandiseService->postCategory($data);

        return redirect()->route('merchandise.categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('merchandise_categories', 'name')->ignore($id)],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'is_store_visible' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['id'] = $id;
        $this->merchandiseService->putCategory($data);

        return redirect()->route('merchandise.categories')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory($id)
    {
        $this->merchandiseService->deleteCategory($id);

        return redirect()->route('merchandise.categories')->with('success', 'Kategori berhasil dihapus.');
    }

    public function products()
    {
        return view('merchandise.products.index', [
            'title' => 'Products',
            'products' => $this->merchandiseService->getProducts(),
        ]);
    }

    public function createProduct()
    {
        return view('merchandise.products.form', [
            'title' => 'Add Product',
            'product' => null,
            'categories' => $this->activeCategories(),
        ]);
    }

    public function editProduct($id)
    {
        return view('merchandise.products.form', [
            'title' => 'Edit Product',
            'product' => $this->merchandiseService->showProduct($id),
            'categories' => $this->activeCategories(),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $data = $this->validateProduct($request);
        $this->merchandiseService->postProduct($data);

        return redirect()->route('merchandise.products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function updateProduct(Request $request, $id)
    {
        $product = $this->merchandiseService->showProduct($id);
        $data = $this->validateProduct($request, $id);
        foreach ($data['variants'] as &$variant) {
            if (!empty($variant['id'])) {
                $variant['price'] = $product->variants->firstWhere('id', $variant['id'])->price;
            }
        }
        unset($variant);
        $data['id'] = $id;
        $this->merchandiseService->putProduct($data);

        return redirect()->route('merchandise.products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroyProduct($id)
    {
        $this->merchandiseService->deleteProduct($id);

        return redirect()->route('merchandise.products')->with('success', 'Produk berhasil dihapus.');
    }

    public function stockIns()
    {
        $this->ensureCostAccess();

        return view('merchandise.stock-ins.index', [
            'title' => 'Stock In History',
            'stockIns' => $this->merchandiseService->getStockIns(),
        ]);
    }

    public function inventory()
    {
        $inventory = $this->merchandiseService->getInventory();
        if (auth()->user()->role !== 'admin') {
            foreach ($inventory as $variant) {
                unset($variant->total_cost);
            }
        }

        return view('merchandise.inventory.index', [
            'title' => 'Stock Overview',
            'inventory' => $inventory,
            'canViewCost' => auth()->user()->role === 'admin',
        ]);
    }

    public function sellingPrices()
    {
        $this->ensureCostAccess();

        return view('merchandise.selling-prices.index', [
            'title' => 'Selling Price Management',
            'variants' => $this->merchandiseService->getSellingPrices(),
        ]);
    }

    public function sellingPriceHistory($variantId)
    {
        $this->ensureCostAccess();
        $variant = MerchandiseVariant::with('product.category')->findOrFail($variantId);

        return view('merchandise.selling-prices.history', [
            'title' => 'Selling Price History',
            'variant' => $variant,
            'history' => $this->merchandiseService->getSellingPriceHistory($variant->id),
        ]);
    }

    public function updateSellingPrice(Request $request, $variantId)
    {
        $this->ensureCostAccess();
        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'reason' => ['required', 'string', 'max:500'],
        ]);
        $data['variant_id'] = $variantId;
        $history = $this->merchandiseService->updateSellingPrice($data, $request->user()->id);

        return redirect()
            ->route('merchandise.selling-prices')
            ->with('success', $history ? 'Harga jual berhasil diperbarui dan riwayatnya tersimpan.' : 'Harga jual tidak berubah.');
    }

    public function createStockIn()
    {
        $this->ensureCostAccess();

        return view('merchandise.stock-ins.create', [
            'title' => 'Record Stock In',
            'variants' => $this->merchandiseService->getStockInVariants(),
        ]);
    }

    public function storeStockIn(Request $request)
    {
        $this->ensureCostAccess();

        $data = $request->validate([
            'stock_in_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'integer', 'exists:merchandise_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);
        $this->merchandiseService->postStockIn($data, $request->user()->id);

        return redirect()->route('merchandise.stock-ins')->with('success', 'Stock In berhasil dicatat.');
    }

    public function stockOpnames()
    {
        $this->ensureCostAccess();

        return view('merchandise.stock-opnames.index', [
            'title' => 'Stock Opname',
            'opnames' => $this->merchandiseService->getStockOpnames(),
        ]);
    }

    public function createStockOpname()
    {
        $this->ensureCostAccess();

        return view('merchandise.stock-opnames.create', [
            'title' => 'Catat Stock Opname',
            'variants' => $this->merchandiseService->getStockOpnameVariants(),
        ]);
    }

    public function storeStockOpname(Request $request)
    {
        $this->ensureCostAccess();

        $data = $request->validate([
            'opname_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.counted_quantity' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);

        $opname = $this->merchandiseService->postStockOpname($data, $request->user()->id);

        return redirect()->route('merchandise.stock-opnames.show', $opname->id)
            ->with('success', 'Stock Opname berhasil dicatat dan selisih stok sudah disesuaikan.');
    }

    public function showStockOpname($id)
    {
        $this->ensureCostAccess();

        return view('merchandise.stock-opnames.show', [
            'title' => 'Detail Stock Opname',
            'opname' => $this->merchandiseService->showStockOpname($id),
        ]);
    }

    public function reports(Request $request)
    {
        $this->ensureCostAccess();

        $filters = $request->validate([
            'report' => ['nullable', Rule::in(['sales', 'profit', 'stock', 'movements', 'promotions'])],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $filters['date_from'] = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $filters['date_to'] = $filters['date_to'] ?? now()->toDateString();
        if ($filters['date_to'] < $filters['date_from']) {
            throw ValidationException::withMessages([
                'date_to' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
            ]);
        }

        return view('merchandise.reports.index', array_merge(
            ['title' => 'Reports & Analytics'],
            $this->merchandiseService->getReports($filters)
        ));
    }

    private function activeCategories()
    {
        return MerchandiseCategory::where('is_active', true)->orderBy('name')->get();
    }

    private function validateProduct(Request $request, $productId = null): array
    {
        $request->validate([
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.id' => ['nullable', 'integer', 'exists:merchandise_variants,id'],
        ]);

        foreach ($request->input('variants', []) as $variant) {
            if (!empty($variant['id'])) {
                $belongsToProduct = MerchandiseVariant::where('product_id', $productId)
                    ->whereKey($variant['id'])
                    ->exists();
                if (!$productId || !$belongsToProduct) {
                    abort(404);
                }
            }
        }

        $variantRules = [];
        foreach ($request->input('variants', []) as $index => $variant) {
            $skuRule = Rule::unique('merchandise_variants', 'sku');
            $nameRules = ['required', 'string', 'max:100'];
            if (!empty($variant['id'])) {
                $skuRule->ignore($variant['id']);
            }

            if ($productId) {
                $nameRule = Rule::unique('merchandise_variants', 'name')
                    ->where('product_id', $productId)
                    ->where('color', trim($variant['color'] ?? ''));
                if (!empty($variant['id'])) {
                    $nameRule->ignore($variant['id']);
                }
                $nameRules[] = $nameRule;
            }

            $variantRules["variants.$index.sku"] = ['nullable', 'string', 'max:64', $skuRule];
            $variantRules["variants.$index.name"] = $nameRules;
            $hasVariantImage = !empty($variant['id'])
                && MerchandiseVariant::whereKey($variant['id'])->whereNotNull('image_path')->exists();
            $variantRules["variants.$index.image"] = [
                $hasVariantImage ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ];
        }

        $validator = Validator::make($request->all(), array_merge([
            'category_id' => ['required', 'integer', Rule::exists('merchandise_categories', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:merchandise_product_images,id'],
            'is_active' => ['nullable', 'boolean'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.id' => ['nullable', 'integer', 'exists:merchandise_variants,id'],
            'variants.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'variants.*.color' => ['nullable', 'string', 'max:80'],
            'variants.*.price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
        ], $variantRules));
        if (!$productId && !$request->hasFile('main_image')) {
            $validator->errors()->add('main_image', 'Gambar utama produk wajib diunggah.');
        }
        if ($productId && !MerchandiseProduct::whereKey($productId)->whereNotNull('image_path')->exists()
            && !$request->hasFile('main_image')) {
            $validator->errors()->add('main_image', 'Produk wajib memiliki satu gambar utama.');
        }
        foreach ($request->input('remove_images', []) as $imageId) {
            if (!$productId || !MerchandiseProductImage::where('product_id', $productId)->whereKey($imageId)->exists()) {
                $validator->errors()->add('remove_images', 'Gambar galeri tidak valid.');
                break;
            }
        }
        $validator->after(function ($validator) use ($request) {
            $combinations = [];
            $skus = [];
            foreach ($request->input('variants', []) as $index => $variant) {
                $combination = mb_strtolower(trim($variant['name'] ?? '') . '|' . trim($variant['color'] ?? ''));
                if (isset($combinations[$combination])) {
                    $validator->errors()->add("variants.$index.name", 'Kombinasi nama varian dan warna harus unik.');
                }
                $combinations[$combination] = true;

                $sku = mb_strtolower(trim($variant['sku'] ?? ''));
                if ($sku !== '' && isset($skus[$sku])) {
                    $validator->errors()->add("variants.$index.sku", 'SKU harus unik.');
                }
                if ($sku !== '') {
                    $skus[$sku] = true;
                }
            }
        });

        return $validator->validate();
    }

    private function ensureCostAccess(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }
}
