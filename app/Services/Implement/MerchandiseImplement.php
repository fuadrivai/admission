<?php

namespace App\Services\Implement;

use App\Models\MerchandiseCategory;
use App\Models\MerchandiseCart;
use App\Models\MerchandiseCartItem;
use App\Models\MerchandiseOrder;
use App\Models\MerchandiseOrderItem;
use App\Models\MerchandiseProduct;
use App\Models\MerchandiseProductImage;
use App\Models\MerchandisePromotion;
use App\Models\MerchandiseStockIn;
use App\Models\MerchandiseStockInItem;
use App\Models\MerchandiseStockOpname;
use App\Models\MerchandiseStockOpnameItem;
use App\Models\MerchandiseStockOut;
use App\Models\MerchandiseStockOutItem;
use App\Models\MerchandiseSellingPriceHistory;
use App\Models\MerchandiseStoreSetting;
use App\Models\MerchandiseVariant;
use App\Models\Branch;
use App\Models\BankCharger;
use App\Services\MerchandiseService;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

use function App\Helpers\createXenditInvoice;

class MerchandiseImplement implements MerchandiseService
{
    public function dashboard()
    {
        $stockInTotals = MerchandiseStockInItem::query()
            ->selectRaw('COALESCE(SUM(quantity), 0) as quantity, COALESCE(SUM(quantity * unit_cost), 0) as value')
            ->first();
        $stockOutTotals = $this->activeStockOutItemsQuery()
            ->selectRaw('COALESCE(SUM(quantity), 0) as quantity, COALESCE(SUM(quantity * unit_cost), 0) as value')
            ->first();

        $stockTotals = MerchandiseStockInItem::query()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as current_stock')
            ->groupBy('variant_id');
        $stockOutByVariant = $this->activeStockOutItemsQuery()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as sold_quantity')
            ->groupBy('variant_id');

        $lowStockVariants = MerchandiseVariant::query()
            ->with('product')
            ->leftJoinSub($stockTotals, 'stock_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_totals.variant_id');
            })
            ->leftJoinSub($stockOutByVariant, 'sold_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'sold_totals.variant_id');
            })
            ->select('merchandise_variants.*')
            ->selectRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0)) as current_stock')
            ->where('merchandise_variants.is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('category', function ($categoryQuery) {
                        $categoryQuery->where('is_active', true);
                    });
            })
            ->where('merchandise_variants.low_stock_threshold', '>', 0)
            ->whereRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0)) <= merchandise_variants.low_stock_threshold')
            ->orderBy('current_stock')
            ->limit(10)
            ->get();

        return [
            'total_products' => MerchandiseProduct::where('is_active', true)->count(),
            'total_categories' => MerchandiseCategory::where('is_active', true)->count(),
            'total_stock' => (int) $stockInTotals->quantity - (int) $stockOutTotals->quantity,
            'inventory_value' => (float) $stockInTotals->value - (float) $stockOutTotals->value,
            'low_stock_variants' => $lowStockVariants,
            'recent_stock_ins' => $this->getStockIns(5),
        ];
    }

    public function getCategories()
    {
        return MerchandiseCategory::withCount('products')->orderBy('display_order')->orderBy('name')->get();
    }

    public function postCategory(array $data)
    {
        return MerchandiseCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_store_visible' => $data['is_store_visible'] ?? true,
            'display_order' => $data['display_order'] ?? 0,
        ]);
    }

    public function putCategory(array $data)
    {
        $category = MerchandiseCategory::findOrFail($data['id']);
        $category->update([
            'name' => $data['name'] ?? $category->name,
            'description' => $data['description'] ?? $category->description,
            'is_active' => $data['is_active'] ?? $category->is_active,
            'is_store_visible' => $data['is_store_visible'] ?? $category->is_store_visible,
            'display_order' => $data['display_order'] ?? $category->display_order,
        ]);

        return $category;
    }

    public function deleteCategory($id)
    {
        $category = MerchandiseCategory::findOrFail($id);
        if ($category->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Kategori tidak dapat dihapus karena masih digunakan produk.',
            ]);
        }

        return $category->delete();
    }

    public function getProducts()
    {
        return MerchandiseProduct::with('category', 'variants')
            ->withCount('variants')
            ->orderByDesc('created_at')
            ->get();
    }

    public function showProduct($id)
    {
        return MerchandiseProduct::with('category', 'images', 'variants')->findOrFail($id);
    }

    public function postProduct(array $data)
    {
        $this->assertProductData($data);
        $storedImages = $this->stageProductImages($data);

        try {
            return DB::transaction(function () use ($data, $storedImages) {
                $product = MerchandiseProduct::create([
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'image_path' => $storedImages['main'],
                    'is_active' => $data['is_active'] ?? false,
                ]);

                $this->saveGalleryImages($product, $storedImages['gallery']);
                $this->saveVariants($product, $data['variants']);

                return $product->load('category', 'images', 'variants');
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedImages['all']);
            throw $exception;
        }
    }

    public function putProduct(array $data)
    {
        $this->assertProductData($data);
        $product = MerchandiseProduct::findOrFail($data['id']);
        $storedImages = $this->stageProductImages($data);

        try {
            $obsoletePaths = DB::transaction(function () use ($data, $product, $storedImages) {
                $obsoletePaths = [];
                if ($storedImages['main']) {
                    if ($product->image_path) {
                        $obsoletePaths[] = $product->image_path;
                    }
                    $product->image_path = $storedImages['main'];
                }
                $product->update([
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'image_path' => $product->image_path,
                    'is_active' => $data['is_active'] ?? false,
                ]);

                $removeIds = $data['remove_images'] ?? [];
                if ($removeIds) {
                    $removedImages = $product->images()->whereIn('id', $removeIds)->get();
                    foreach ($removedImages as $image) {
                        $obsoletePaths[] = $image->image_path;
                        $image->delete();
                    }
                }
                $this->saveGalleryImages($product, $storedImages['gallery']);
                $obsoletePaths = array_merge($obsoletePaths, $this->saveVariants($product, $data['variants']));

                return $obsoletePaths;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedImages['all']);
            throw $exception;
        }

        Storage::disk('public')->delete($obsoletePaths);

        return $product->fresh()->load('category', 'images', 'variants');
    }

    public function deleteProduct($id)
    {
        $product = MerchandiseProduct::findOrFail($id);
        $hasStockHistory = MerchandiseStockInItem::whereIn(
            'variant_id',
            $product->variants()->select('id')
        )->exists()
            || MerchandiseOrderItem::whereIn('variant_id', $product->variants()->select('id'))->exists();

        if ($hasStockHistory) {
            throw ValidationException::withMessages([
                'product' => 'Produk dengan riwayat Stock In tidak dapat dihapus. Nonaktifkan produk sebagai gantinya.',
            ]);
        }

        $imagePaths = array_merge([$product->image_path], $product->images()->pluck('image_path')->all(), $product->variants()->pluck('image_path')->all());
        $deleted = $product->delete();
        if ($deleted) {
            Storage::disk('public')->delete(array_filter($imagePaths));
        }

        return $deleted;
    }

    public function getInventory()
    {
        $stockTotals = MerchandiseStockInItem::query()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as current_stock, SUM(quantity * unit_cost) as total_cost')
            ->groupBy('variant_id');
        $stockOutTotals = $this->activeStockOutItemsQuery()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as sold_quantity, SUM(quantity * unit_cost) as sold_cost')
            ->groupBy('variant_id');

        return MerchandiseVariant::query()
            ->with('product.category')
            ->leftJoinSub($stockTotals, 'stock_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_totals.variant_id');
            })
            ->leftJoinSub($stockOutTotals, 'sold_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'sold_totals.variant_id');
            })
            ->select('merchandise_variants.*')
            ->selectRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0)) as current_stock')
            ->selectRaw('(COALESCE(stock_totals.total_cost, 0) - COALESCE(sold_totals.sold_cost, 0)) as total_cost')
            ->orderBy('product_id')
            ->orderBy('color')
            ->orderBy('name')
            ->paginate(25);
    }

    public function getSellingPrices()
    {
        $stockTotals = MerchandiseStockInItem::query()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as current_stock, SUM(quantity * unit_cost) as total_cost')
            ->groupBy('variant_id');
        $stockOutTotals = $this->activeStockOutItemsQuery()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as sold_quantity')
            ->groupBy('variant_id');

        return MerchandiseVariant::query()
            ->with('product.category')
            ->leftJoinSub($stockTotals, 'stock_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_totals.variant_id');
            })
            ->leftJoinSub($stockOutTotals, 'sold_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'sold_totals.variant_id');
            })
            ->select('merchandise_variants.*')
            ->selectRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0)) as current_stock')
            ->selectRaw('CASE WHEN COALESCE(stock_totals.current_stock, 0) > 0 THEN stock_totals.total_cost / stock_totals.current_stock ELSE NULL END as average_unit_cost')
            ->orderBy('product_id')
            ->orderBy('color')
            ->orderBy('name')
            ->paginate(25);
    }

    public function getSellingPriceHistory($variantId)
    {
        return MerchandiseSellingPriceHistory::with('changer')
            ->where('variant_id', $variantId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(25);
    }

    public function updateSellingPrice(array $data, $userId)
    {
        if (!isset($data['price']) || !is_numeric($data['price']) || !is_finite((float) $data['price']) || (float) $data['price'] < 0) {
            throw ValidationException::withMessages([
                'price' => 'Harga jual harus berupa angka nol atau lebih.',
            ]);
        }

        return DB::transaction(function () use ($data, $userId) {
            $variant = MerchandiseVariant::lockForUpdate()->findOrFail($data['variant_id']);
            return $this->recordSellingPriceChange(
                $variant,
                (float) $data['price'],
                $userId,
                $data['reason'] ?? null
            );
        });
    }

    public function getStoreSettings()
    {
        $this->ensureStoreSettingsExists();

        return MerchandiseStoreSetting::findOrFail(1);
    }

    public function updateStoreSettings(array $data)
    {
        $this->ensureStoreSettingsExists();
        $settings = MerchandiseStoreSetting::findOrFail(1);
        $settings->update([
            'store_name' => $data['store_name'],
            'description' => $data['description'] ?? null,
            'is_open' => $data['is_open'] ?? false,
            'closed_message' => $data['closed_message'] ?? null,
        ]);

        return $settings;
    }

    private function ensureStoreSettingsExists(): void
    {
        $now = now();
        DB::table('merchandise_store_settings')->insertOrIgnore([
            'id' => 1,
            'store_name' => 'MHIS Merchandise Store',
            'is_open' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function getCatalogProducts()
    {
        return MerchandiseProduct::with('category', 'variants')
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(25);
    }

    public function updateCatalogProduct(array $data)
    {
        if (!empty($data['sale_start_at']) && !empty($data['sale_end_at'])
            && strtotime($data['sale_end_at']) < strtotime($data['sale_start_at'])) {
            throw ValidationException::withMessages([
                'sale_end_at' => 'Periode penjualan berakhir harus setelah waktu mulai.',
            ]);
        }

        return DB::transaction(function () use ($data) {
            $product = MerchandiseProduct::findOrFail($data['product_id']);
            foreach ($data['variants'] ?? [] as $variantData) {
                if (!$product->variants()->whereKey($variantData['id'])->exists()) {
                    throw ValidationException::withMessages([
                        'variants' => 'Varian produk tidak valid.',
                    ]);
                }
            }

            $product->update([
                'is_published' => $data['is_published'] ?? false,
                'display_order' => $data['display_order'] ?? 0,
                'sale_start_at' => $data['sale_start_at'] ?? null,
                'sale_end_at' => $data['sale_end_at'] ?? null,
            ]);

            foreach ($data['variants'] ?? [] as $variantData) {
                $product->variants()->whereKey($variantData['id'])->update([
                    'online_stock_limit' => $variantData['online_stock_limit'] ?? null,
                ]);
            }

            return $product->load('category', 'variants');
        });
    }

    public function getPromotions()
    {
        $promotions = MerchandisePromotion::with([
            'applicableVariants.product',
            'giftOptions.variant.product',
            'giftVariant.product',
            'bundleItems.variant.product',
        ])->orderByDesc('created_at')->paginate(25);
        foreach ($promotions as $promotion) {
            $promotion->setAttribute('usage_count', $this->promotionUsageCount($promotion->id));
        }

        return $promotions;
    }

    public function getPromotionFormData()
    {
        return [
            'promotionVariants' => MerchandiseVariant::with('product')
                ->where('is_active', true)
                ->whereHas('product', function ($query) {
                    $query->where('is_active', true);
                })
                ->orderBy('product_id')
                ->orderBy('name')
                ->get(),
        ];
    }

    public function showPromotion($id)
    {
        return MerchandisePromotion::with([
            'applicableVariants',
            'giftOptions',
            'giftVariant',
            'bundleItems',
        ])->findOrFail($id);
    }

    public function postPromotion(array $data)
    {
        return $this->savePromotion($data);
    }

    public function putPromotion(array $data)
    {
        return $this->savePromotion($data, MerchandisePromotion::findOrFail($data['id']));
    }

    public function deletePromotion($id)
    {
        return MerchandisePromotion::findOrFail($id)->delete();
    }

    public function getOrders(array $filters = [])
    {
        $query = MerchandiseOrder::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($orderQuery) use ($search) {
                $orderQuery->where('code', 'like', '%' . $search . '%')
                    ->orWhere('parent_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('portal_username', 'like', '%' . $search . '%')
                    ->orWhere('branch_name', 'like', '%' . $search . '%');
            });
        }

        $status = $filters['status'] ?? 'all';
        $this->applyOrderStatusFilter($query, $status);
        $orders = $query->paginate(25)->withQueryString();
        foreach ($orders as $order) {
            $order->setAttribute('display_payment_status', $this->effectiveOrderStatus($order));
            $order->setAttribute('stock_is_held', $this->isOrderStockHeld($order));
        }

        $stats = [
            'all' => MerchandiseOrder::count(),
            'pending' => $this->orderStatusQuery('pending')->count(),
            'paid' => $this->orderStatusQuery('paid')->count(),
            'expired' => $this->orderStatusQuery('expired')->count(),
            'failed' => $this->orderStatusQuery('failed')->count(),
            'held_items' => DB::table('merchandise_order_items')
                ->join('merchandise_orders', 'merchandise_orders.id', '=', 'merchandise_order_items.order_id')
                ->where('merchandise_orders.payment_status', 'PENDING')
                ->where('merchandise_orders.expires_at', '>', now())
                ->sum('merchandise_order_items.quantity'),
        ];

        return [
            'orders' => $orders,
            'stats' => $stats,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'status' => $status,
            ],
        ];
    }

    public function showOrder($id)
    {
        $order = MerchandiseOrder::with(['items', 'promotion'])->findOrFail($id);
        $order->setAttribute('display_payment_status', $this->effectiveOrderStatus($order));
        $order->setAttribute('stock_is_held', $this->isOrderStockHeld($order));

        return $order;
    }

    private function orderStatusQuery(string $status)
    {
        $query = MerchandiseOrder::query();
        $this->applyOrderStatusFilter($query, $status);

        return $query;
    }

    private function applyOrderStatusFilter($query, string $status): void
    {
        if ($status === 'pending') {
            $query->where('payment_status', 'PENDING')
                ->where(function ($pendingQuery) {
                    $pendingQuery->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                });
        } elseif ($status === 'paid') {
            $query->where('payment_status', 'PAID');
        } elseif ($status === 'expired') {
            $query->where(function ($expiredQuery) {
                $expiredQuery->where('payment_status', 'EXPIRED')
                    ->orWhere(function ($pendingQuery) {
                        $pendingQuery->where('payment_status', 'PENDING')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '<=', now());
                    });
            });
        } elseif ($status === 'failed') {
            $query->where('payment_status', 'FAILED');
        }
    }

    private function effectiveOrderStatus(MerchandiseOrder $order): string
    {
        if ($order->payment_status === 'PENDING'
            && $order->expires_at
            && $order->expires_at->isPast()) {
            return 'EXPIRED';
        }

        return strtoupper($order->payment_status);
    }

    private function isOrderStockHeld(MerchandiseOrder $order): bool
    {
        return $order->payment_status === 'PENDING'
            && $order->expires_at
            && $order->expires_at->isFuture();
    }

    private function savePromotion(array $data, ?MerchandisePromotion $promotion = null): MerchandisePromotion
    {
        return DB::transaction(function () use ($data, $promotion) {
            $variantIds = collect($data['type'] === 'bundle' ? [] : ($data['applicable_variants'] ?? []))
                ->map(function ($id) { return (int) $id; })
                ->unique()
                ->values();
            $bundleItems = $data['type'] === 'bundle'
                ? collect($data['bundle_items'] ?? [])
                : collect();
            $giftOptions = $data['type'] === 'buy_x_get_y'
                ? collect($data['gift_options'] ?? [])
                : collect();
            $existingGiftLimits = $promotion
                ? $promotion->giftOptions->pluck('max_quantity', 'variant_id')
                : collect();
            $referencedIds = $variantIds
                ->merge($bundleItems->pluck('variant_id'))
                ->merge($giftOptions->pluck('variant_id'))
                ->push($data['type'] === 'free_gift' ? ($data['gift_variant_id'] ?? null) : null)
                ->filter()
                ->map(function ($id) { return (int) $id; })
                ->unique();
            $validVariantCount = MerchandiseVariant::whereIn('id', $referencedIds)
                ->where('is_active', true)
                ->whereHas('product', function ($query) {
                    $query->where('is_active', true);
                })
                ->count();
            if ($validVariantCount !== $referencedIds->count()) {
                throw ValidationException::withMessages([
                    'applicable_variants' => 'Pilih varian produk yang aktif.',
                ]);
            }
            foreach ($giftOptions as $option) {
                $variantId = (int) $option['variant_id'];
                $maxQuantity = (int) $option['max_quantity'];
                $limitChanged = !$existingGiftLimits->has($variantId)
                    || (int) $existingGiftLimits->get($variantId) !== $maxQuantity;
                if ($limitChanged && $maxQuantity > $this->availableStockForVariant($variantId)) {
                    throw ValidationException::withMessages([
                        'gift_options' => 'Batas jumlah hadiah tidak boleh melebihi stok tersedia untuk varian yang dipilih.',
                    ]);
                }
            }
            if ($data['type'] === 'bundle') {
                $bundleValue = 0;
                foreach ($bundleItems as $item) {
                    $variant = MerchandiseVariant::findOrFail($item['variant_id']);
                    $bundleValue += (float) $variant->price * (int) $item['quantity'];
                }
                if ((float) $data['bundle_price'] >= $bundleValue) {
                    throw ValidationException::withMessages([
                        'bundle_price' => 'Harga paket harus lebih rendah dari total harga normal item paket.',
                    ]);
                }
            }

            $usesDiscount = in_array($data['type'], ['minimum_purchase', 'quantity_discount', 'voucher'], true);
            $attributes = [
                'name' => trim($data['name']),
                'code' => $data['type'] === 'voucher' && !empty($data['code'])
                    ? Str::upper(trim($data['code']))
                    : null,
                'type' => $data['type'],
                'discount_type' => $usesDiscount ? ($data['discount_type'] ?? null) : null,
                'discount_value' => $usesDiscount ? ($data['discount_value'] ?? 0) : 0,
                'minimum_purchase' => in_array($data['type'], ['minimum_purchase', 'voucher'], true)
                    ? ($data['minimum_purchase'] ?? null)
                    : null,
                'min_quantity' => in_array($data['type'], ['quantity_discount', 'free_gift'], true)
                    ? ($data['min_quantity'] ?? null)
                    : null,
                'buy_quantity' => $data['type'] === 'buy_x_get_y' ? ($data['buy_quantity'] ?? null) : null,
                'get_quantity' => in_array($data['type'], ['buy_x_get_y', 'free_gift'], true)
                    ? ($data['get_quantity'] ?? null)
                    : null,
                'gift_variant_id' => $data['type'] === 'free_gift' ? ($data['gift_variant_id'] ?? null) : null,
                'bundle_price' => $data['type'] === 'bundle' ? ($data['bundle_price'] ?? null) : null,
                'valid_from' => $data['valid_from'] ?? null,
                'valid_until' => $data['valid_until'] ?? null,
                'usage_limit' => $data['usage_limit'] ?? null,
                'is_active' => $data['is_active'] ?? false,
            ];
            $promotion = $promotion
                ? tap($promotion)->update($attributes)
                : MerchandisePromotion::create($attributes);

            $promotion->applicableVariants()->sync($variantIds);
            $promotion->bundleItems()->delete();
            foreach ($bundleItems as $item) {
                $promotion->bundleItems()->create([
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            $promotion->giftOptions()->delete();
            foreach ($giftOptions as $option) {
                $promotion->giftOptions()->create([
                    'variant_id' => $option['variant_id'],
                    'max_quantity' => $option['max_quantity'],
                ]);
            }

            return $promotion->fresh(['applicableVariants', 'bundleItems', 'giftOptions']);
        });
    }

    public function getPublicStore($cartKey)
    {
        $products = $this->publicProductsQuery()
            ->join('merchandise_categories as store_categories', 'store_categories.id', '=', 'merchandise_products.category_id')
            ->select('merchandise_products.*')
            ->with('category', 'images')
            ->with(['variants' => function ($query) {
                $this->applyPublicVariantAvailability($query);
            }])
            ->orderBy('store_categories.display_order')
            ->orderBy('merchandise_products.display_order')
            ->orderBy('merchandise_products.name')
            ->paginate(24);

        return [
            'settings' => $this->getStoreSettings(),
            'products' => $products,
            'cartCount' => $this->cartItemCount($cartKey),
        ];
    }

    public function getPublicProduct($productId, $cartKey)
    {
        $product = $this->publicProductsQuery()
            ->with('category', 'images')
            ->whereKey($productId)
            ->firstOrFail();

        return [
            'settings' => $this->getStoreSettings(),
            'product' => $product,
            'variants' => $this->availablePublicVariants($product->id),
            'cartCount' => $this->cartItemCount($cartKey),
        ];
    }

    public function getCart($cartKey)
    {
        $cart = MerchandiseCart::with([
            'items.variant.product.category',
            'items.variant.product.images',
        ])->where('cart_key', $cartKey)->first();
        $items = $cart ? $cart->items : collect();
        $subtotal = 0;
        $totalItems = 0;
        $cartCount = 0;
        $storeIsOpen = $this->getStoreSettings()->is_open;

        foreach ($items as $item) {
            $available = $this->availableStockForVariant($item->variant_id);
            $product = $item->variant->product;
            $isPublic = $storeIsOpen
                && $item->variant->is_active
                && $product->is_active
                && $product->is_published
                && $product->category->is_active
                && $product->category->is_store_visible
                && (!$product->sale_start_at || !$product->sale_start_at->isFuture())
                && (!$product->sale_end_at || !$product->sale_end_at->isPast());
            $item->variant->setAttribute('available_stock', $available);
            $item->setAttribute('line_total', (float) $item->variant->price * $item->quantity);
            $item->setAttribute('is_available', $isPublic && $item->quantity <= $available);
            $cartCount += $item->quantity;
            if ($item->is_selected) {
                $subtotal += $item->line_total;
                $totalItems += $item->quantity;
            }
        }

        return [
            'settings' => $this->getStoreSettings(),
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
            'bank_charger' => $totalItems > 0 ? (float) (BankCharger::query()->value('price') ?? 0) : 0,
            'totalItems' => $totalItems,
            'cartCount' => $cartCount,
        ];
    }

    public function getCheckout(
        $cartKey,
        ?string $promotionCode = null,
        ?int $giftVariantId = null,
        ?int $promotionId = null
    )
    {
        $cartData = $this->getCart($cartKey);
        $cartData['items'] = $cartData['items']->where('is_selected', true)->values();
        if ($cartData['items']->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Pilih minimal satu barang sebelum melanjutkan ke pembayaran.',
            ]);
        }
        $cartData['branches'] = Branch::orderBy('name')->get()->filter(function ($branch) {
            return (bool) config('services.xendit.api_key_' . strtolower($branch->name));
        })->values();
        $cartData['promotionOptions'] = $this->promotionOptions(
            $cartData['items'],
            $promotionCode
        );
        $cartData['promotionQuote'] = $this->promotionQuote(
            $cartData['items'],
            $promotionCode,
            false,
            $giftVariantId,
            $promotionId
        );
        $cartData['selectedPromotionId'] = $cartData['promotionQuote']['promotion']
            ? (int) $cartData['promotionQuote']['promotion']->id
            : null;

        return $cartData;
    }

    public function previewPromotion($cartKey, string $code)
    {
        $cartData = $this->getCart($cartKey);
        $selectedItems = $cartData['items']->where('is_selected', true)->values();
        $voucher = MerchandisePromotion::with([
            'applicableVariants',
            'giftOptions.variant.product',
            'giftVariant.product',
            'bundleItems.variant.product',
        ])->where('code', Str::upper(trim($code)))
            ->where('type', 'voucher')
            ->first();
        $quote = $voucher ? $this->evaluatePromotion($voucher, $selectedItems) : null;
        if (!$quote) {
            throw ValidationException::withMessages([
                'code' => 'Kode promo tidak valid, belum aktif, kuota habis, atau syarat belanja belum terpenuhi.',
            ]);
        }

        return $voucher;
    }

    public function previewPromotionGift($cartKey, ?string $code, int $giftVariantId)
    {
        $cartData = $this->getCart($cartKey);
        $selectedItems = $cartData['items']->where('is_selected', true)->values();
        $quote = $this->promotionQuote($selectedItems, $code, true, $giftVariantId);
        if (empty($quote['gift_items'])) {
            throw ValidationException::withMessages([
                'gift_variant_id' => 'Hadiah yang dipilih tidak tersedia atau tidak memenuhi syarat promo.',
            ]);
        }

        return $quote;
    }

    public function previewPromotionSelection($cartKey, ?string $code, int $promotionId, ?int $giftVariantId = null)
    {
        $cartData = $this->getCart($cartKey);
        $selectedItems = $cartData['items']->where('is_selected', true)->values();
        return $this->promotionQuote($selectedItems, $code, true, $giftVariantId, $promotionId);
    }

    public function addToCart($cartKey, $variantId, $quantity)
    {
        return DB::transaction(function () use ($cartKey, $variantId, $quantity) {
            $variant = MerchandiseVariant::with('product.category')
                ->lockForUpdate()
                ->findOrFail($variantId);
            $this->assertVariantPublic($variant);
            $cart = MerchandiseCart::firstOrCreate(['cart_key' => $cartKey]);
            $item = $cart->items()->where('variant_id', $variant->id)->lockForUpdate()->first();
            $newQuantity = (int) $quantity + ($item ? (int) $item->quantity : 0);

            if ($newQuantity > $this->availableStockForVariant($variant->id, null, true)) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah yang dipilih melebihi stok tersedia.',
                ]);
            }

            if ($item) {
                $item->update([
                    'quantity' => $newQuantity,
                    'is_selected' => true,
                ]);
            } else {
                $cart->items()->create([
                    'variant_id' => $variant->id,
                    'quantity' => $newQuantity,
                ]);
            }
        });
    }

    public function updateCart($cartKey, array $items)
    {
        return DB::transaction(function () use ($cartKey, $items) {
            $cart = MerchandiseCart::where('cart_key', $cartKey)->lockForUpdate()->firstOrFail();
            foreach ($items as $itemData) {
                $item = $cart->items()->whereKey($itemData['id'])->lockForUpdate()->firstOrFail();
                $variant = MerchandiseVariant::lockForUpdate()->findOrFail($item->variant_id);
                $isSelected = (bool) $itemData['is_selected'];
                if ($isSelected && (int) $itemData['quantity'] > $this->availableStockForVariant($variant->id, null, true)) {
                    throw ValidationException::withMessages([
                        'items' => 'Jumlah ' . $item->variant->product->name . ' melebihi stok tersedia.',
                    ]);
                }
                $item->update([
                    'quantity' => (int) $itemData['quantity'],
                    'is_selected' => $isSelected,
                ]);
            }
        });
    }

    public function removeCartItem($cartKey, $itemId)
    {
        $cart = MerchandiseCart::where('cart_key', $cartKey)->firstOrFail();
        return $cart->items()->whereKey($itemId)->firstOrFail()->delete();
    }

    public function createOrderAndInvoice($cartKey, array $data)
    {
        if (!$this->getStoreSettings()->is_open) {
            throw ValidationException::withMessages([
                'payment' => 'Toko sedang tutup dan belum dapat menerima pesanan.',
            ]);
        }
        $branch = Branch::findOrFail($data['branch_id']);
        if (!config('services.xendit.api_key_' . strtolower($branch->name))) {
            throw ValidationException::withMessages([
                'branch_id' => 'Pembayaran Xendit belum dikonfigurasi untuk cabang yang dipilih.',
            ]);
        }

        $cartItemIds = [];
        $order = DB::transaction(function () use ($cartKey, $data, $branch, &$cartItemIds) {
            $cart = MerchandiseCart::where('cart_key', $cartKey)->lockForUpdate()->firstOrFail();
            $cartItems = $cart->items()->where('is_selected', true)->with('variant.product')->lockForUpdate()->get();
            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Pilih minimal satu barang sebelum melanjutkan ke pembayaran.',
                ]);
            }
            $cartItemIds = $cartItems->pluck('id')->all();

            $variants = MerchandiseVariant::with('product.category')->whereIn('id', $cartItems->pluck('variant_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $subtotal = 0;
            $totalItems = 0;
            $cartLines = collect();
            foreach ($cartItems as $cartItem) {
                $variant = $variants->get($cartItem->variant_id);
                if (!$variant) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu produk dalam keranjang sudah tidak tersedia.',
                    ]);
                }
                $this->assertVariantPublic($variant);
                if ($cartItem->quantity > $this->availableStockForVariant($variant->id, null, true)) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stok ' . $variant->product->name . ' tidak mencukupi. Perbarui keranjang sebelum membayar.',
                    ]);
                }
                $subtotal += (float) $variant->price * $cartItem->quantity;
                $totalItems += $cartItem->quantity;
                $cartItem->setRelation('variant', $variant);
                $cartItem->setAttribute('line_total', (float) $variant->price * $cartItem->quantity);
                $variant->setAttribute('available_stock', $this->availableStockForVariant($variant->id, null, true));
                $cartLines->push($cartItem);
            }
            if ($subtotal <= 0) {
                throw ValidationException::withMessages([
                    'cart' => 'Total pembayaran harus lebih dari Rp0 untuk membuat invoice.',
                ]);
            }

            $promotionQuote = $this->promotionQuote(
                $cartLines,
                $data['promotion_code'] ?? null,
                !empty($data['promotion_id'])
                    || !empty($data['promotion_gift_variant_id']),
                !empty($data['promotion_gift_variant_id'])
                    ? (int) $data['promotion_gift_variant_id']
                    : null,
                !empty($data['promotion_id']) ? (int) $data['promotion_id'] : null
            );
            $promotion = $promotionQuote['promotion'] ?? null;
            if ($promotion) {
                $promotion = MerchandisePromotion::with([
                    'applicableVariants',
                    'giftOptions.variant.product',
                    'giftVariant.product',
                    'bundleItems.variant.product',
                ])->lockForUpdate()->findOrFail($promotion->id);
                $promotionQuote = $this->evaluatePromotion(
                    $promotion,
                    $cartLines,
                    true,
                    !empty($data['promotion_gift_variant_id'])
                        ? (int) $data['promotion_gift_variant_id']
                        : null
                );
                if (!$promotionQuote) {
                    throw ValidationException::withMessages([
                        'promotion_code' => 'Promo tidak lagi memenuhi syarat atau kuotanya sudah habis.',
                    ]);
                }
                if (!empty($promotionQuote['gift_options']) && empty($promotionQuote['gift_items'])) {
                    throw ValidationException::withMessages([
                        'promotion_gift_variant_id' => 'Pilih salah satu hadiah promo sebelum melanjutkan.',
                    ]);
                }
                $promotionQuote['promotion'] = $promotion;
            }

            $bankCharger = (float) (BankCharger::query()->value('price') ?? 0);
            $discountAmount = min($subtotal, (float) ($promotionQuote['discount_amount'] ?? 0));
            $giftItems = $promotionQuote['gift_items'] ?? [];
            $code = 'MERCH-' . now()->format('ymd') . '-' . Str::upper(Str::random(8));
            $order = MerchandiseOrder::create([
                'code' => $code,
                'external_id' => $code,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'parent_name' => $data['parent_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'portal_username' => $data['portal_username'],
                'total_items' => $totalItems + collect($giftItems)->sum('quantity'),
                'subtotal' => round($subtotal, 2),
                'bank_charger' => round($bankCharger, 2),
                'promotion_id' => $promotion ? $promotion->id : null,
                'promotion_name' => $promotion ? $promotion->name : null,
                'promotion_code' => $promotion ? $promotion->code : null,
                'discount_amount' => round($discountAmount, 2),
                'total_amount' => round(max(0, $subtotal - $discountAmount) + $bankCharger, 2),
                'payment_status' => 'PENDING',
                'expires_at' => now()->addDay(),
            ]);

            foreach ($cartItems as $cartItem) {
                $variant = $variants->get($cartItem->variant_id);
                $order->items()->create([
                    'variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => trim(($variant->color ? $variant->color . ' / ' : '') . $variant->name),
                    'sku' => $variant->sku,
                    'image_path' => $variant->image_path ?: $variant->product->image_path,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $variant->price,
                    'subtotal' => round((float) $variant->price * $cartItem->quantity, 2),
                ]);
            }
            foreach ($giftItems as $giftItem) {
                $giftVariant = MerchandiseVariant::with('product')->findOrFail($giftItem['variant_id']);
                $order->items()->create([
                    'variant_id' => $giftVariant->id,
                    'product_name' => $giftVariant->product->name,
                    'variant_name' => trim(($giftVariant->color ? $giftVariant->color . ' / ' : '') . $giftVariant->name)
                        . ' (Hadiah promo)',
                    'sku' => $giftVariant->sku,
                    'image_path' => $giftVariant->image_path ?: $giftVariant->product->image_path,
                    'quantity' => $giftItem['quantity'],
                    'unit_price' => 0,
                    'subtotal' => 0,
                ]);
            }

            $this->createOrderStockOut($order, $order->items);

            return $order;
        });

        try {
            $xendit = createXenditInvoice([
                'external_id' => $order->external_id,
                'amount' => $order->total_amount,
                'payer_email' => $order->email,
                'description' => 'MHIS Merchandise Order ' . $order->code,
                'invoice_duration' => 60 * 60 * 24,
            ], $branch->name);
        } catch (ConnectionException $exception) {
            $order->update(['payment_status' => 'FAILED']);
            throw ValidationException::withMessages([
                'payment' => 'Tidak dapat terhubung ke Xendit. Silakan coba kembali.',
            ]);
        }

        if (!empty($xendit['success']) && $xendit['success'] === false) {
            $order->update(['payment_status' => 'FAILED']);
            throw ValidationException::withMessages([
                'payment' => 'Invoice Xendit gagal dibuat: ' . ($xendit['message'] ?? 'Unknown Xendit error.'),
            ]);
        }
        if (empty($xendit['status']) || empty($xendit['invoice_url'])
            || empty($xendit['created']) || empty($xendit['expiry_date'])) {
            $order->update(['payment_status' => 'FAILED']);
            throw ValidationException::withMessages([
                'payment' => 'Respons Xendit tidak lengkap; silakan coba lagi.',
            ]);
        }

        $order->update([
            'payment_url' => $xendit['invoice_url'],
            'expires_at' => Carbon::parse($xendit['expiry_date']),
        ]);
        MerchandiseCart::where('cart_key', $cartKey)->firstOrFail()->items()->whereIn('id', $cartItemIds)->delete();

        return $order->fresh();
    }

    public function processOrderPaymentCallback(string $externalId, array $callbackData)
    {
        $status = strtoupper($callbackData['status'] ?? '');
        $mappedStatus = in_array($status, ['PAID', 'SETTLED'], true)
            ? 'PAID'
            : (in_array($status, ['EXPIRED', 'FAILED'], true) ? $status : 'PENDING');

        return DB::transaction(function () use ($externalId, $callbackData, $mappedStatus) {
            $order = MerchandiseOrder::where('external_id', $externalId)->lockForUpdate()->first();
            if (!$order) {
                return false;
            }
            if ($order->payment_status === 'PAID') {
                return true;
            }
            if (in_array($order->payment_status, ['FAILED', 'EXPIRED'], true) && $mappedStatus === 'PENDING') {
                return true;
            }

            $orderItems = $order->items;
            MerchandiseVariant::whereIn('id', $orderItems->pluck('variant_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $order->update([
                'payment_status' => $mappedStatus,
                'paid_at' => $mappedStatus === 'PAID'
                    ? (!empty($callbackData['paid_at']) ? Carbon::parse($callbackData['paid_at']) : now())
                    : null,
            ]);

            if ($mappedStatus === 'PAID' && !MerchandiseStockOut::where('order_id', $order->id)->exists()) {
                $this->createOrderStockOut($order, $orderItems, $order->paid_at ?: now());
            }

            return true;
        });
    }

    public function getStockIns($limit = null)
    {
        $query = MerchandiseStockIn::with('creator', 'items.variant.product')
            ->withCount('items')
            ->orderByDesc('stock_in_date')
            ->orderByDesc('id');

        return $limit ? $query->limit($limit)->get() : $query->paginate(25);
    }

    public function getStockInVariants()
    {
        return MerchandiseVariant::with('product.category')
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('category', function ($categoryQuery) {
                        $categoryQuery->where('is_active', true);
                    });
            })
            ->orderBy('product_id')
            ->orderBy('name')
            ->get();
    }

    public function postStockIn(array $data, $userId)
    {
        $this->assertStockInData($data);

        return DB::transaction(function () use ($data, $userId) {
            $stockIn = MerchandiseStockIn::create([
                'stock_in_date' => $data['stock_in_date'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            foreach ($data['items'] as $item) {
                $variant = MerchandiseVariant::with('product.category')
                    ->where('is_active', true)
                    ->whereHas('product', function ($query) {
                        $query->where('is_active', true)
                            ->whereHas('category', function ($categoryQuery) {
                                $categoryQuery->where('is_active', true);
                            });
                    })
                    ->lockForUpdate()
                    ->find($item['variant_id']);

                if (!$variant) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu varian sudah tidak aktif. Muat ulang halaman dan coba lagi.',
                    ]);
                }

                MerchandiseStockInItem::create([
                    'stock_in_id' => $stockIn->id,
                    'variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            return $stockIn->load('items.variant.product');
        });
    }

    public function getStockOpnames()
    {
        return MerchandiseStockOpname::with('creator')
            ->withCount('items')
            ->withSum('items', 'difference')
            ->orderByDesc('opname_date')
            ->orderByDesc('id')
            ->paginate(25);
    }

    public function getStockOpnameVariants()
    {
        $stockInTotals = MerchandiseStockInItem::query()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as quantity')
            ->groupBy('variant_id');
        $stockOutTotals = $this->physicalStockOutItemsQuery()
            ->select('merchandise_stock_out_items.variant_id')
            ->selectRaw('SUM(merchandise_stock_out_items.quantity) as quantity')
            ->groupBy('merchandise_stock_out_items.variant_id');

        return MerchandiseVariant::query()
            ->with('product.category')
            ->leftJoinSub($stockInTotals, 'stock_in_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_in_totals.variant_id');
            })
            ->leftJoinSub($stockOutTotals, 'stock_out_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_out_totals.variant_id');
            })
            ->select('merchandise_variants.*')
            ->selectRaw('CASE WHEN COALESCE(stock_in_totals.quantity, 0) - COALESCE(stock_out_totals.quantity, 0) > 0 THEN COALESCE(stock_in_totals.quantity, 0) - COALESCE(stock_out_totals.quantity, 0) ELSE 0 END as system_quantity')
            ->orderBy('product_id')
            ->orderBy('color')
            ->orderBy('name')
            ->get();
    }

    public function showStockOpname($id)
    {
        return MerchandiseStockOpname::with([
            'creator',
            'items.variant.product.category',
        ])->findOrFail($id);
    }

    public function postStockOpname(array $data, $userId)
    {
        $this->assertStockOpnameData($data);

        return DB::transaction(function () use ($data, $userId) {
            $variantIds = array_map('intval', array_keys($data['items']));
            $variants = MerchandiseVariant::whereIn('id', $variantIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($variants->count() !== count(array_unique($variantIds))) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu varian tidak ditemukan. Muat ulang halaman dan coba lagi.',
                ]);
            }

            $opname = MerchandiseStockOpname::create([
                'opname_date' => $data['opname_date'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);
            $positiveDifferences = [];
            $negativeDifferences = [];

            foreach ($data['items'] as $variantId => $itemData) {
                $variant = $variants->get((int) $variantId);
                if (!$variant) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu varian tidak ditemukan. Muat ulang halaman dan coba lagi.',
                    ]);
                }
                $systemQuantity = $this->stockOnHandForVariant($variant->id);
                $countedQuantity = (int) $itemData['counted_quantity'];
                $difference = $countedQuantity - $systemQuantity;

                MerchandiseStockOpnameItem::create([
                    'opname_id' => $opname->id,
                    'variant_id' => $variant->id,
                    'system_quantity' => $systemQuantity,
                    'counted_quantity' => $countedQuantity,
                    'difference' => $difference,
                ]);

                if ($difference > 0) {
                    $positiveDifferences[] = [$variant, $difference];
                } elseif ($difference < 0) {
                    $negativeDifferences[] = [$variant, abs($difference)];
                }
            }

            $reference = 'Penyesuaian Stock Opname #' . $opname->id;
            if ($positiveDifferences) {
                $stockIn = MerchandiseStockIn::create([
                    'stock_in_date' => $opname->opname_date,
                    'notes' => $reference,
                    'created_by' => $userId,
                    'stock_opname_id' => $opname->id,
                ]);

                foreach ($positiveDifferences as [$variant, $quantity]) {
                    MerchandiseStockInItem::create([
                        'stock_in_id' => $stockIn->id,
                        'variant_id' => $variant->id,
                        'quantity' => $quantity,
                        'unit_cost' => $this->averageUnitCost($variant->id) ?? 0,
                    ]);
                }
            }

            if ($negativeDifferences) {
                $stockOut = MerchandiseStockOut::create([
                    'stock_out_at' => Carbon::parse($opname->opname_date)->startOfDay(),
                    'created_by' => $userId,
                    'stock_opname_id' => $opname->id,
                ]);

                foreach ($negativeDifferences as [$variant, $quantity]) {
                    MerchandiseStockOutItem::create([
                        'stock_out_id' => $stockOut->id,
                        'variant_id' => $variant->id,
                        'quantity' => $quantity,
                        'unit_cost' => $this->averageUnitCost($variant->id) ?? 0,
                    ]);
                }
            }

            return $opname->load('items.variant.product');
        });
    }

    public function getReports(array $filters = [])
    {
        $type = $filters['report'] ?? 'sales';
        $from = Carbon::parse($filters['date_from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($filters['date_to'] ?? now()->toDateString())->endOfDay();

        switch ($type) {
            case 'profit':
                $data = $this->profitReport($from, $to);
                break;
            case 'stock':
                $data = $this->stockReport();
                break;
            case 'movements':
                $data = $this->stockMovementReport($from, $to);
                break;
            case 'promotions':
                $data = $this->promotionReport($from, $to);
                break;
            default:
                $type = 'sales';
                $data = $this->salesReport($from, $to);
                break;
        }

        return array_merge($data, [
            'reportType' => $type,
            'dateFrom' => $from->toDateString(),
            'dateTo' => $to->toDateString(),
        ]);
    }

    private function salesReport(Carbon $from, Carbon $to): array
    {
        $paidOrders = MerchandiseOrder::where('payment_status', 'PAID')
            ->whereBetween('paid_at', [$from, $to]);
        $summary = (clone $paidOrders)
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('COALESCE(SUM(total_items), 0) as item_count')
            ->selectRaw('COALESCE(SUM(subtotal), 0) as gross_sales')
            ->selectRaw('COALESCE(SUM(discount_amount), 0) as discounts')
            ->selectRaw('COALESCE(SUM(bank_charger), 0) as admin_fees')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as collected_amount')
            ->first();

        $daily = (clone $paidOrders)
            ->selectRaw('DATE(paid_at) as report_date')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('COALESCE(SUM(subtotal - discount_amount), 0) as net_sales')
            ->selectRaw('COALESCE(SUM(bank_charger), 0) as admin_fees')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as collected_amount')
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('report_date')
            ->get();

        $topProducts = DB::table('merchandise_order_items as items')
            ->join('merchandise_orders as orders', 'orders.id', '=', 'items.order_id')
            ->where('orders.payment_status', 'PAID')
            ->whereBetween('orders.paid_at', [$from, $to])
            ->select('items.product_name', 'items.variant_name')
            ->selectRaw('SUM(items.quantity) as quantity')
            ->selectRaw('SUM(items.subtotal) as sales_amount')
            ->groupBy('items.product_name', 'items.variant_name')
            ->orderByDesc('quantity')
            ->limit(20)
            ->get();

        return [
            'title' => 'Laporan Penjualan',
            'description' => 'Hanya pesanan yang telah dibayar, dikelompokkan berdasarkan tanggal pembayaran.',
            'metrics' => [
                ['label' => 'Pesanan lunas', 'value' => number_format($summary->order_count)],
                ['label' => 'Barang terjual', 'value' => number_format($summary->item_count) . ' pcs'],
                ['label' => 'Penjualan setelah diskon', 'value' => 'Rp' . number_format($summary->gross_sales - $summary->discounts, 0, ',', '.')],
                ['label' => 'Total dibayar', 'value' => 'Rp' . number_format($summary->collected_amount, 0, ',', '.')],
            ],
            'rows' => $daily,
            'topProducts' => $topProducts,
        ];
    }

    private function profitReport(Carbon $from, Carbon $to): array
    {
        $orderCosts = DB::table('merchandise_stock_outs as stock_outs')
            ->join('merchandise_stock_out_items as stock_items', 'stock_items.stock_out_id', '=', 'stock_outs.id')
            ->whereNotNull('stock_outs.order_id')
            ->select('stock_outs.order_id')
            ->selectRaw('SUM(stock_items.quantity * stock_items.unit_cost) as cost_of_goods')
            ->groupBy('stock_outs.order_id');

        $orders = DB::table('merchandise_orders as orders')
            ->leftJoinSub($orderCosts, 'order_costs', function ($join) {
                $join->on('orders.id', '=', 'order_costs.order_id');
            })
            ->where('orders.payment_status', 'PAID')
            ->whereBetween('orders.paid_at', [$from, $to])
            ->select('orders.code', 'orders.parent_name', 'orders.paid_at')
            ->selectRaw('(orders.subtotal - orders.discount_amount) as net_sales')
            ->selectRaw('COALESCE(order_costs.cost_of_goods, 0) as cost_of_goods')
            ->selectRaw('(orders.subtotal - orders.discount_amount - COALESCE(order_costs.cost_of_goods, 0)) as gross_profit');
        $summary = DB::query()->fromSub(clone $orders, 'profit_orders')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('COALESCE(SUM(net_sales), 0) as net_sales')
            ->selectRaw('COALESCE(SUM(cost_of_goods), 0) as cost_of_goods')
            ->selectRaw('COALESCE(SUM(gross_profit), 0) as gross_profit')
            ->first();

        return [
            'title' => 'Laporan Laba Kotor',
            'description' => 'Penjualan setelah diskon dikurangi HPP yang tersimpan pada Stock Out pesanan. Biaya admin tidak dihitung sebagai penjualan merchandise.',
            'metrics' => [
                ['label' => 'Pesanan lunas', 'value' => number_format($summary->order_count)],
                ['label' => 'Penjualan setelah diskon', 'value' => 'Rp' . number_format($summary->net_sales, 0, ',', '.')],
                ['label' => 'HPP barang terjual', 'value' => 'Rp' . number_format($summary->cost_of_goods, 0, ',', '.')],
                ['label' => 'Laba kotor', 'value' => 'Rp' . number_format($summary->gross_profit, 0, ',', '.')],
            ],
            'rows' => $orders->orderByDesc('orders.paid_at')->limit(500)->get(),
        ];
    }

    private function stockReport(): array
    {
        $stockInTotals = MerchandiseStockInItem::query()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as quantity, SUM(quantity * unit_cost) as value')
            ->groupBy('variant_id');
        $stockOutTotals = $this->activeStockOutItemsQuery()
            ->select('merchandise_stock_out_items.variant_id')
            ->selectRaw('SUM(merchandise_stock_out_items.quantity) as quantity, SUM(merchandise_stock_out_items.quantity * merchandise_stock_out_items.unit_cost) as value')
            ->groupBy('merchandise_stock_out_items.variant_id');

        $variants = MerchandiseVariant::query()
            ->with('product.category')
            ->leftJoinSub($stockInTotals, 'stock_in_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_in_totals.variant_id');
            })
            ->leftJoinSub($stockOutTotals, 'stock_out_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_out_totals.variant_id');
            })
            ->select('merchandise_variants.*')
            ->selectRaw('(COALESCE(stock_in_totals.quantity, 0) - COALESCE(stock_out_totals.quantity, 0)) as current_stock')
            ->selectRaw('(COALESCE(stock_in_totals.value, 0) - COALESCE(stock_out_totals.value, 0)) as inventory_value')
            ->orderBy('product_id')
            ->orderBy('color')
            ->orderBy('name')
            ->get();

        return [
            'title' => 'Laporan Persediaan',
            'description' => 'Snapshot saldo persediaan saat ini berdasarkan Stock In dikurangi Stock Out aktif. Invoice pending yang masih berlaku ikut mengurangi stok tersedia.',
            'metrics' => [
                ['label' => 'Jumlah varian', 'value' => number_format($variants->count())],
                ['label' => 'Stok tersedia', 'value' => number_format($variants->sum('current_stock')) . ' pcs'],
                ['label' => 'Varian stok menipis', 'value' => number_format($variants->filter(function ($variant) {
                    return $variant->low_stock_threshold > 0 && $variant->current_stock <= $variant->low_stock_threshold;
                })->count())],
                ['label' => 'Nilai persediaan', 'value' => 'Rp' . number_format($variants->sum('inventory_value'), 0, ',', '.')],
            ],
            'rows' => $variants,
        ];
    }

    private function stockMovementReport(Carbon $from, Carbon $to): array
    {
        $stockInStats = DB::table('merchandise_stock_in_items as items')
            ->join('merchandise_stock_ins as stock_ins', 'stock_ins.id', '=', 'items.stock_in_id')
            ->whereBetween('stock_ins.stock_in_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('COUNT(*) as record_count, COALESCE(SUM(items.quantity), 0) as quantity')
            ->first();
        $stockOutQuery = $this->activeStockOutItemsQuery()
            ->whereBetween('merchandise_stock_outs.stock_out_at', [$from, $to]);
        $stockOutStats = (clone $stockOutQuery)
            ->selectRaw('COUNT(*) as record_count, COALESCE(SUM(merchandise_stock_out_items.quantity), 0) as quantity')
            ->first();

        $stockIns = DB::table('merchandise_stock_in_items as items')
            ->join('merchandise_stock_ins as stock_ins', 'stock_ins.id', '=', 'items.stock_in_id')
            ->join('merchandise_variants as variants', 'variants.id', '=', 'items.variant_id')
            ->join('merchandise_products as products', 'products.id', '=', 'variants.product_id')
            ->whereBetween('stock_ins.stock_in_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('stock_ins.stock_in_date as movement_date')
            ->selectRaw("CASE WHEN stock_ins.stock_opname_id IS NULL THEN 'Stock In' ELSE 'Opname Adjustment (+)' END as movement_type")
            ->addSelect('products.name as product_name', 'variants.color', 'variants.name as variant_name')
            ->selectRaw('items.quantity as quantity')
            ->addSelect('stock_ins.notes as reference')
            ->orderByDesc('stock_ins.stock_in_date')
            ->orderByDesc('items.id')
            ->limit(1000)
            ->get();

        $stockOuts = $stockOutQuery
            ->join('merchandise_variants as variants', 'variants.id', '=', 'merchandise_stock_out_items.variant_id')
            ->join('merchandise_products as products', 'products.id', '=', 'variants.product_id')
            ->selectRaw('DATE(merchandise_stock_outs.stock_out_at) as movement_date')
            ->selectRaw("CASE WHEN merchandise_stock_outs.stock_opname_id IS NOT NULL THEN 'Opname Adjustment (-)' WHEN merchandise_stock_outs.order_id IS NOT NULL THEN 'Order Stock Out' ELSE 'Stock Out' END as movement_type")
            ->addSelect('products.name as product_name', 'variants.color', 'variants.name as variant_name')
            ->selectRaw('merchandise_stock_out_items.quantity as quantity')
            ->selectRaw("COALESCE(merchandise_orders.code, CONCAT('Stock Out #', merchandise_stock_outs.id)) as reference")
            ->orderByDesc('merchandise_stock_outs.stock_out_at')
            ->orderByDesc('merchandise_stock_out_items.id')
            ->limit(1000)
            ->get();

        $rows = $stockIns->concat($stockOuts)->sortByDesc('movement_date')->values();

        return [
            'title' => 'Laporan Stock In / Out',
            'description' => 'Pergerakan stok pada rentang tanggal, termasuk koreksi dari Stock Opname. Stock Out order yang invoice-nya sudah tidak aktif tidak ditampilkan.',
            'metrics' => [
                ['label' => 'Transaksi masuk', 'value' => number_format($stockInStats->record_count)],
                ['label' => 'Unit masuk', 'value' => number_format($stockInStats->quantity) . ' pcs'],
                ['label' => 'Transaksi keluar', 'value' => number_format($stockOutStats->record_count)],
                ['label' => 'Unit keluar', 'value' => number_format($stockOutStats->quantity) . ' pcs'],
            ],
            'rows' => $rows->take(1000)->values(),
        ];
    }

    private function promotionReport(Carbon $from, Carbon $to): array
    {
        $rows = MerchandiseOrder::query()
            ->where('payment_status', 'PAID')
            ->whereNotNull('promotion_id')
            ->whereBetween('paid_at', [$from, $to])
            ->select('promotion_id', 'promotion_name', 'promotion_code')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('COALESCE(SUM(discount_amount), 0) as discount_total')
            ->selectRaw('COALESCE(SUM(subtotal - discount_amount), 0) as net_sales')
            ->groupBy('promotion_id', 'promotion_name', 'promotion_code')
            ->orderByDesc('order_count')
            ->get();

        return [
            'title' => 'Laporan Promosi',
            'description' => 'Kinerja promo berdasarkan pesanan yang sudah dibayar. Data nama/kode promo berasal dari snapshot saat transaksi.',
            'metrics' => [
                ['label' => 'Promo digunakan', 'value' => number_format($rows->count())],
                ['label' => 'Pesanan dengan promo', 'value' => number_format($rows->sum('order_count'))],
                ['label' => 'Total diskon', 'value' => 'Rp' . number_format($rows->sum('discount_total'), 0, ',', '.')],
                ['label' => 'Penjualan setelah diskon', 'value' => 'Rp' . number_format($rows->sum('net_sales'), 0, ',', '.')],
            ],
            'rows' => $rows,
        ];
    }

    private function stockOnHandForVariant($variantId): int
    {
        $stockIn = (int) MerchandiseStockInItem::where('variant_id', $variantId)->sum('quantity');
        $stockOut = (int) $this->physicalStockOutItemsQuery()
            ->where('merchandise_stock_out_items.variant_id', $variantId)
            ->sum('merchandise_stock_out_items.quantity');

        return max(0, $stockIn - $stockOut);
    }

    private function physicalStockOutItemsQuery()
    {
        return DB::table('merchandise_stock_out_items')
            ->join('merchandise_stock_outs', 'merchandise_stock_outs.id', '=', 'merchandise_stock_out_items.stock_out_id')
            ->leftJoin('merchandise_orders', 'merchandise_orders.id', '=', 'merchandise_stock_outs.order_id')
            ->where(function ($query) {
                $query->whereNull('merchandise_stock_outs.order_id')
                    ->orWhere('merchandise_orders.payment_status', 'PAID');
            });
    }

    private function stageProductImages(array &$data): array
    {
        $stored = [
            'main' => null,
            'gallery' => [],
            'all' => [],
        ];

        try {
            if (!empty($data['main_image'])) {
                $stored['main'] = $this->storeProductImage($data['main_image']);
                $stored['all'][] = $stored['main'];
            }
            foreach ($data['gallery_images'] ?? [] as $image) {
                $path = $this->storeProductImage($image);
                $stored['gallery'][] = $path;
                $stored['all'][] = $path;
            }
            foreach ($data['variants'] as $index => $variantData) {
                if (!empty($variantData['image'])) {
                    $path = $this->storeProductImage($variantData['image']);
                    $data['variants'][$index]['stored_image_path'] = $path;
                    $stored['all'][] = $path;
                }
            }
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($stored['all']);
            throw $exception;
        }

        return $stored;
    }

    private function saveGalleryImages(MerchandiseProduct $product, array $imagePaths): void
    {
        $displayOrder = (int) $product->images()->max('display_order') + 1;
        foreach ($imagePaths as $path) {
            $product->images()->create([
                'image_path' => $path,
                'display_order' => $displayOrder++,
            ]);
        }
    }

    private function saveVariants(MerchandiseProduct $product, array $variants): array
    {
        $keptIds = [];
        $obsoleteImagePaths = [];

        foreach ($variants as $variantData) {
            $variant = null;
            if (!empty($variantData['id'])) {
                $variant = $product->variants()->find($variantData['id']);
                if (!$variant) {
                    throw ValidationException::withMessages([
                        'variants' => 'Varian produk tidak valid.',
                    ]);
                }
            }

            $attributes = [
                'name' => $variantData['name'],
                'color' => trim($variantData['color'] ?? ''),
                'sku' => $variantData['sku'] ?? null,
                'image_path' => $variantData['stored_image_path'] ?? ($variant ? $variant->image_path : null),
                'price' => $variantData['price'] ?? ($variant ? $variant->price : 0),
                'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 0,
                'is_active' => $variantData['is_active'] ?? false,
            ];

            if ($variant) {
                if (!empty($variantData['stored_image_path']) && $variant->image_path) {
                    $obsoleteImagePaths[] = $variant->image_path;
                }
                $newPrice = array_key_exists('price', $variantData)
                    ? (float) $variantData['price']
                    : (float) $variant->price;
                unset($attributes['price']);
                $variant->update($attributes);
                if ($newPrice !== (float) $variant->price) {
                    $this->recordSellingPriceChange(
                        $variant,
                        $newPrice,
                        auth()->id(),
                        'Harga diperbarui melalui manajemen produk.'
                    );
                }
            } else {
                $variant = $product->variants()->create($attributes);
                $variant->sellingPriceHistories()->create([
                    'new_price' => $variant->price,
                    'changed_by' => auth()->id(),
                    'reason' => 'Harga awal varian.',
                ]);
            }
            $keptIds[] = $variant->id;
        }

        $removedVariants = $product->variants()->whereNotIn('id', $keptIds)->get();
        foreach ($removedVariants as $variant) {
            $hasHistory = $variant->stockInItems()->exists()
                || MerchandiseStockOutItem::where('variant_id', $variant->id)->exists()
                || MerchandiseOrderItem::where('variant_id', $variant->id)->exists();
            if ($hasHistory) {
                $variant->update(['is_active' => false]);
            } else {
                if ($variant->image_path) {
                    $obsoleteImagePaths[] = $variant->image_path;
                }
                $variant->delete();
            }
        }

        return $obsoleteImagePaths;
    }

    private function assertProductData(array $data): void
    {
        if (!MerchandiseCategory::where('is_active', true)->whereKey($data['category_id'])->exists()) {
            throw ValidationException::withMessages([
                'category_id' => 'Kategori produk tidak aktif atau tidak ditemukan.',
            ]);
        }

        if (empty($data['variants'])) {
            throw ValidationException::withMessages([
                'variants' => 'Produk harus memiliki setidaknya satu varian.',
            ]);
        }

        $product = !empty($data['id']) ? MerchandiseProduct::find($data['id']) : null;
        if (empty($data['main_image']) && (!$product || !$product->image_path)) {
            throw ValidationException::withMessages([
                'main_image' => 'Produk harus memiliki satu gambar utama.',
            ]);
        }
        foreach ($data['variants'] as $variantData) {
            $variant = !empty($variantData['id']) && $product
                ? $product->variants()->find($variantData['id'])
                : null;
            if (empty($variantData['image']) && (!$variant || !$variant->image_path)) {
                throw ValidationException::withMessages([
                    'variants' => 'Setiap varian wajib memiliki gambar.',
                ]);
            }
        }
    }

    private function publicProductsQuery()
    {
        return MerchandiseProduct::query()
            ->where('merchandise_products.is_active', true)
            ->where('merchandise_products.is_published', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true)->where('is_store_visible', true);
            })
            ->whereHas('variants', function ($query) {
                $this->applyPublicVariantAvailability($query);
            })
            ->where(function ($query) {
                $query->whereNull('merchandise_products.sale_start_at')
                    ->orWhere('merchandise_products.sale_start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('merchandise_products.sale_end_at')
                    ->orWhere('merchandise_products.sale_end_at', '>=', now());
            });
    }

    private function availablePublicVariants($productId)
    {
        $query = MerchandiseVariant::query()->where('product_id', $productId);
        $this->applyPublicVariantAvailability($query);

        return $query->orderBy('color')->orderBy('name')->get();
    }

    private function applyPublicVariantAvailability($query): void
    {
        $stockTotals = MerchandiseStockInItem::query()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as current_stock')
            ->groupBy('variant_id');
        $stockOutTotals = $this->activeStockOutItemsQuery()
            ->select('variant_id')
            ->selectRaw('SUM(quantity) as sold_quantity')
            ->groupBy('variant_id');
        $reservedTotals = DB::table('merchandise_order_items')
            ->join('merchandise_orders', 'merchandise_orders.id', '=', 'merchandise_order_items.order_id')
            ->where('merchandise_orders.payment_status', 'PENDING')
            ->where('merchandise_orders.expires_at', '>', now())
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('merchandise_stock_outs')
                    ->whereColumn('merchandise_stock_outs.order_id', 'merchandise_orders.id');
            })
            ->select('merchandise_order_items.variant_id')
            ->selectRaw('SUM(merchandise_order_items.quantity) as reserved_quantity')
            ->groupBy('merchandise_order_items.variant_id');

        $query->leftJoinSub($stockTotals, 'stock_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'stock_totals.variant_id');
        })->leftJoinSub($stockOutTotals, 'sold_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'sold_totals.variant_id');
        })->leftJoinSub($reservedTotals, 'reserved_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'reserved_totals.variant_id');
        });

        $netStock = '(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0))';
        $onlineStock = '(CASE WHEN merchandise_variants.online_stock_limit IS NOT NULL AND merchandise_variants.online_stock_limit < '
            . $netStock . ' THEN merchandise_variants.online_stock_limit ELSE ' . $netStock . ' END)';
        $availableStock = '(' . $onlineStock . ' - COALESCE(reserved_totals.reserved_quantity, 0))';

        $query->select('merchandise_variants.*')
            ->selectRaw('CASE WHEN ' . $availableStock . ' > 0 THEN ' . $availableStock . ' ELSE 0 END as available_stock')
            ->where('merchandise_variants.is_active', true)
            ->whereRaw($availableStock . ' > 0')
            ->where(function ($variantQuery) {
                $variantQuery->whereNull('merchandise_variants.online_stock_limit')
                    ->orWhere('merchandise_variants.online_stock_limit', '>', 0);
            });
    }

    private function availableStockForVariant($variantId, $excludeOrderId = null, bool $lockForUpdate = false): int
    {
        $variantQuery = MerchandiseVariant::whereKey($variantId);
        if ($lockForUpdate) {
            $variantQuery->lockForUpdate();
        }
        $variant = $variantQuery->first();
        if (!$variant) {
            return 0;
        }

        if ($lockForUpdate) {
            $stockIn = MerchandiseStockInItem::where('variant_id', $variantId)
                ->lockForUpdate()->get(['quantity'])->sum('quantity');
        } else {
            $stockIn = MerchandiseStockInItem::where('variant_id', $variantId)->sum('quantity');
        }
        $stockOutQuery = $this->activeStockOutItemsQuery()
            ->where('merchandise_stock_out_items.variant_id', $variantId);
        if ($lockForUpdate) {
            $stockOut = (int) $stockOutQuery->lockForUpdate()
                ->get(['merchandise_stock_out_items.quantity'])
                ->sum('quantity');
        } else {
            $stockOut = $stockOutQuery->sum('merchandise_stock_out_items.quantity');
        }
        $physicalStock = (int) $stockIn - (int) $stockOut;
        $reservationQuery = DB::table('merchandise_order_items')
            ->join('merchandise_orders', 'merchandise_orders.id', '=', 'merchandise_order_items.order_id')
            ->where('merchandise_order_items.variant_id', $variantId)
            ->where('merchandise_orders.payment_status', 'PENDING')
            ->where('merchandise_orders.expires_at', '>', now())
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('merchandise_stock_outs')
                    ->whereColumn('merchandise_stock_outs.order_id', 'merchandise_orders.id');
            })
            ->select('merchandise_order_items.quantity');
        if ($excludeOrderId) {
            $reservationQuery->where('merchandise_order_items.order_id', '!=', $excludeOrderId);
        }
        $reserved = $lockForUpdate
            ? (int) $reservationQuery->lockForUpdate()->get()->sum('quantity')
            : (int) $reservationQuery->sum('merchandise_order_items.quantity');
        $available = $variant->online_stock_limit === null
            ? $physicalStock
            : min($physicalStock, (int) $variant->online_stock_limit);

        return max(0, $available - $reserved);
    }

    private function activeStockOutItemsQuery()
    {
        return DB::table('merchandise_stock_out_items')
            ->join('merchandise_stock_outs', 'merchandise_stock_outs.id', '=', 'merchandise_stock_out_items.stock_out_id')
            ->leftJoin('merchandise_orders', 'merchandise_orders.id', '=', 'merchandise_stock_outs.order_id')
            ->where(function ($query) {
                $query->whereNull('merchandise_stock_outs.order_id')
                    ->orWhere('merchandise_orders.payment_status', 'PAID')
                    ->orWhere(function ($pendingQuery) {
                        $pendingQuery->where('merchandise_orders.payment_status', 'PENDING')
                            ->where('merchandise_orders.expires_at', '>', now());
                    });
            });
    }

    private function promotionQuote(
        $items,
        ?string $code = null,
        bool $strict = false,
        ?int $giftVariantId = null,
        ?int $promotionId = null
    ): array {
        $emptyQuote = [
            'promotion' => null,
            'discount_amount' => 0,
            'gift_items' => [],
            'gift_options' => [],
            'selected_gift_variant_id' => null,
        ];
        if ($items->isEmpty()) {
            if ($strict && $promotionId !== null) {
                throw ValidationException::withMessages([
                    'promotion_code' => 'Tambahkan barang ke keranjang sebelum memakai promo.',
                ]);
            }

            return $emptyQuote;
        }

        $options = $this->promotionOptions($items, $code);
        $selectedQuote = null;
        if ($promotionId !== null) {
            $selectedQuote = $options->first(function ($quote) use ($promotionId) {
                return (int) $quote['promotion']->id === $promotionId;
            });
            if (!$selectedQuote && $strict) {
                throw ValidationException::withMessages([
                    'promotion_id' => 'Promo yang dipilih sudah tidak tersedia atau tidak memenuhi syarat.',
                ]);
            }
        }

        if (!$selectedQuote && $options->isNotEmpty()) {
            foreach ($options as $option) {
                if (!$selectedQuote || $option['discount_amount'] > $selectedQuote['discount_amount']) {
                    $selectedQuote = $option;
                }
            }
        }
        if (!$selectedQuote) {
            return $emptyQuote;
        }

        $selectedPromotion = $selectedQuote['promotion'];
        $selectedQuote = $this->evaluatePromotion(
            $selectedPromotion,
            $items,
            false,
            $giftVariantId
        );
        if (!$selectedQuote && $giftVariantId !== null && !$strict) {
            $selectedQuote = $this->evaluatePromotion(
                $selectedPromotion,
                $items
            );
        }
        if (!$selectedQuote && $strict) {
            throw ValidationException::withMessages([
                'promotion_id' => 'Promo yang dipilih sudah tidak tersedia atau tidak memenuhi syarat.',
            ]);
        }

        return $selectedQuote ?: $emptyQuote;
    }

    private function promotionOptions($items, ?string $code = null)
    {
        $options = collect();
        if ($items->isEmpty()) {
            return $options;
        }

        $promotions = MerchandisePromotion::with([
            'applicableVariants',
            'giftOptions.variant.product',
            'giftVariant.product',
            'bundleItems.variant.product',
        ])->where('type', '!=', 'voucher')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->orderBy('id')
            ->get();

        foreach ($promotions as $promotion) {
            $quote = $this->evaluatePromotion($promotion, $items);
            if ($quote) {
                $options->push($quote);
            }
        }

        if ($code !== null && trim($code) !== '') {
            $voucher = MerchandisePromotion::with([
                'applicableVariants',
                'giftOptions.variant.product',
                'giftVariant.product',
                'bundleItems.variant.product',
            ])->where('code', Str::upper(trim($code)))
                ->where('type', 'voucher')
                ->first();
            $quote = $voucher ? $this->evaluatePromotion($voucher, $items) : null;
            if ($quote) {
                $options->push($quote);
            }
        }

        return $options;
    }

    private function evaluatePromotion(
        MerchandisePromotion $promotion,
        $items,
        bool $lockStock = false,
        ?int $giftVariantId = null
    ): ?array {
        if (!$promotion->is_active
            || ($promotion->valid_from && $promotion->valid_from->isFuture())
            || ($promotion->valid_until && $promotion->valid_until->isPast())
            || ($promotion->usage_limit && $this->promotionUsageCount($promotion->id) >= $promotion->usage_limit)) {
            return null;
        }

        $targetIds = $promotion->applicableVariants->pluck('id')->map(function ($id) {
            return (int) $id;
        });
        $eligibleItems = $items->filter(function ($item) use ($targetIds) {
            return $targetIds->isEmpty() || $targetIds->contains((int) $item->variant_id);
        });
        $eligibleSubtotal = (float) $eligibleItems->sum(function ($item) {
            return (float) $item->line_total;
        });
        $eligibleQuantity = (int) $eligibleItems->sum('quantity');
        $discount = 0;
        $giftItems = [];
        $giftOptions = [];

        if (in_array($promotion->type, ['minimum_purchase', 'voucher'], true)) {
            if ($promotion->minimum_purchase && $eligibleSubtotal < $promotion->minimum_purchase) {
                return null;
            }
            if (!$promotion->discount_type || $promotion->discount_value <= 0) {
                return null;
            }
            $discount = $this->discountValue(
                $promotion->discount_type,
                (float) $promotion->discount_value,
                $eligibleSubtotal
            );
        } elseif ($promotion->type === 'quantity_discount') {
            if ($eligibleQuantity < $promotion->min_quantity) {
                return null;
            }
            $discount = $this->discountValue(
                $promotion->discount_type,
                (float) $promotion->discount_value,
                $eligibleSubtotal
            );
        } elseif ($promotion->type === 'buy_x_get_y') {
            $rewardQuantity = intdiv($eligibleQuantity, (int) $promotion->buy_quantity)
                * (int) $promotion->get_quantity;
            if ($rewardQuantity <= 0 || $promotion->giftOptions->isEmpty()) {
                return null;
            }
            foreach ($promotion->giftOptions as $giftOption) {
                $variant = $giftOption->variant;
                $cartQuantity = (int) $items->where('variant_id', $variant->id)->sum('quantity');
                $remainingStock = max(
                    0,
                    $this->cartVariantAvailableStock($items, $variant->id, $lockStock) - $cartQuantity
                );
                $giftQuantity = min($rewardQuantity, (int) $giftOption->max_quantity, $remainingStock);
                if ($giftQuantity <= 0) {
                    continue;
                }
                $option = [
                    'variant_id' => $variant->id,
                    'quantity' => $giftQuantity,
                    'name' => $variant->product->name . ' · ' . trim(($variant->color ? $variant->color . ' / ' : '') . $variant->name),
                ];
                $giftOptions[] = $option;
                if ($giftVariantId !== null && (int) $variant->id === $giftVariantId) {
                    $giftItems[] = $option;
                }
            }
            if ($giftVariantId === null && !empty($giftOptions)) {
                $giftItems[] = $giftOptions[0];
            }
            if ($giftVariantId !== null && empty($giftItems)) {
                return null;
            }
        } elseif ($promotion->type === 'free_gift') {
            $rewardCount = intdiv($eligibleQuantity, (int) $promotion->min_quantity);
            $giftQuantity = $rewardCount * (int) $promotion->get_quantity;
            if ($giftQuantity <= 0 || !$promotion->giftVariant) {
                return null;
            }
            $giftCartQuantity = (int) $items->where('variant_id', $promotion->gift_variant_id)->sum('quantity');
            $availableGiftStock = $this->cartVariantAvailableStock($items, $promotion->gift_variant_id, $lockStock);
            if ($giftCartQuantity + $giftQuantity > $availableGiftStock) {
                return null;
            }
            $giftItems[] = [
                'variant_id' => $promotion->gift_variant_id,
                'quantity' => $giftQuantity,
                'name' => $promotion->giftVariant->product->name . ' · ' . $promotion->giftVariant->name,
            ];
        } elseif ($promotion->type === 'bundle') {
            $bundleItems = $promotion->bundleItems;
            if ($bundleItems->isEmpty()) {
                return null;
            }
            $bundleCount = null;
            $originalBundleValue = 0;
            foreach ($bundleItems as $bundleItem) {
                $cartQuantity = (int) $items->where('variant_id', $bundleItem->variant_id)->sum('quantity');
                $possibleBundles = intdiv($cartQuantity, (int) $bundleItem->quantity);
                $bundleCount = $bundleCount === null
                    ? $possibleBundles
                    : min($bundleCount, $possibleBundles);
                $originalBundleValue += (float) $bundleItem->variant->price * (int) $bundleItem->quantity;
            }
            if (!$bundleCount) {
                return null;
            }
            $discount = max(0, ($originalBundleValue - (float) $promotion->bundle_price) * $bundleCount);
        }

        if ($discount <= 0 && empty($giftItems) && empty($giftOptions)) {
            return null;
        }

        return [
            'promotion' => $promotion,
            'discount_amount' => round($discount, 2),
            'gift_items' => $giftItems,
            'gift_options' => $giftOptions,
            'selected_gift_variant_id' => $giftItems ? (int) $giftItems[0]['variant_id'] : null,
        ];
    }

    private function discountValue(string $type, float $value, float $eligibleSubtotal): float
    {
        $discount = $type === 'percentage'
            ? $eligibleSubtotal * min(100, $value) / 100
            : $value;

        return round(min($eligibleSubtotal, $discount), 2);
    }

    private function promotionUsageCount($promotionId): int
    {
        return MerchandiseOrder::where('promotion_id', $promotionId)
            ->where(function ($query) {
                $query->where('payment_status', 'PAID')
                    ->orWhere(function ($pendingQuery) {
                        $pendingQuery->where('payment_status', 'PENDING')
                            ->where('expires_at', '>', now());
                    });
            })
            ->count();
    }

    private function cartVariantAvailableStock($items, $variantId, bool $lockForUpdate = false): int
    {
        $cartItem = $items->firstWhere('variant_id', $variantId);
        if ($cartItem) {
            return (int) $cartItem->variant->available_stock;
        }

        return $this->availableStockForVariant($variantId, null, $lockForUpdate);
    }

    private function createOrderStockOut(MerchandiseOrder $order, $items, $stockOutAt = null): void
    {
        MerchandiseVariant::whereIn('id', $items->pluck('variant_id')->unique())
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $stockOut = MerchandiseStockOut::create([
            'order_id' => $order->id,
            'stock_out_at' => $stockOutAt ?: now(),
        ]);

        foreach ($items as $item) {
            $stockOut->items()->create([
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'unit_cost' => $this->averageUnitCost($item->variant_id) ?? 0,
            ]);
        }
    }

    private function assertVariantPublic(MerchandiseVariant $variant): void
    {
        $product = $variant->product;
        if (!$this->getStoreSettings()->is_open
            || !$variant->is_active
            || !$product->is_active
            || !$product->is_published
            || !$product->category->is_active
            || !$product->category->is_store_visible
            || ($product->sale_start_at && $product->sale_start_at->isFuture())
            || ($product->sale_end_at && $product->sale_end_at->isPast())) {
            throw ValidationException::withMessages([
                'variant_id' => 'Varian produk saat ini tidak tersedia di toko.',
            ]);
        }
    }

    private function cartItemCount($cartKey): int
    {
        return (int) MerchandiseCartItem::whereIn(
            'cart_id',
            MerchandiseCart::where('cart_key', $cartKey)->select('id')
        )->sum('quantity');
    }

    private function assertStockInData(array $data): void
    {
        if (empty($data['items'])) {
            throw ValidationException::withMessages([
                'items' => 'Stock In harus memiliki setidaknya satu item.',
            ]);
        }

        foreach ($data['items'] as $item) {
            if ((int) $item['quantity'] < 1 || (float) $item['unit_cost'] < 0) {
                throw ValidationException::withMessages([
                    'items' => 'Jumlah harus lebih dari nol dan harga modal tidak boleh negatif.',
                ]);
            }
        }
    }

    private function assertStockOpnameData(array $data): void
    {
        $date = $data['opname_date'] ?? '';
        $dateParts = [];
        if (!is_string($date)
            || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $dateParts)
            || !checkdate((int) $dateParts[2], (int) $dateParts[3], (int) $dateParts[1])
            || $date > now()->toDateString()) {
            throw ValidationException::withMessages([
                'opname_date' => 'Tanggal Stock Opname harus tanggal yang valid dan tidak boleh di masa depan.',
            ]);
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            throw ValidationException::withMessages([
                'items' => 'Stock Opname harus memiliki setidaknya satu varian.',
            ]);
        }

        foreach ($data['items'] as $variantId => $item) {
            $countedQuantity = $item['counted_quantity'] ?? null;
            if (!ctype_digit((string) $variantId)
                || (int) $variantId < 1
                || filter_var($countedQuantity, FILTER_VALIDATE_INT) === false
                || (int) $countedQuantity < 0
                || (int) $countedQuantity > 999999) {
                throw ValidationException::withMessages([
                    'items' => 'Jumlah fisik harus bilangan bulat antara 0 dan 999.999.',
                ]);
            }
        }
    }

    private function storeProductImage($image): string
    {
        $path = $image->store('merchandise/products', 'public');
        if (!$path) {
            throw new RuntimeException('Foto produk gagal disimpan.');
        }

        return $path;
    }

    private function averageUnitCost($variantId): ?float
    {
        $stock = MerchandiseStockInItem::where('variant_id', $variantId)
            ->selectRaw('COALESCE(SUM(quantity), 0) as quantity, COALESCE(SUM(quantity * unit_cost), 0) as total_cost')
            ->first();

        if (!$stock->quantity) {
            return null;
        }

        return round($stock->total_cost / $stock->quantity, 2);
    }

    private function recordSellingPriceChange(MerchandiseVariant $variant, float $newPrice, $userId, ?string $reason): ?MerchandiseSellingPriceHistory
    {
        $oldPrice = (float) $variant->price;
        $newPrice = round($newPrice, 2);
        if ($oldPrice === $newPrice) {
            return null;
        }

        $averageUnitCost = $this->averageUnitCost($variant->id);
        $variant->update(['price' => $newPrice]);
        return $variant->sellingPriceHistories()->create([
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'average_unit_cost' => $averageUnitCost,
            'changed_by' => $userId,
            'reason' => $reason,
        ]);
    }
}
