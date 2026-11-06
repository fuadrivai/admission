<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\MerchandiseCategory;
use App\Models\MerchandiseCart;
use App\Models\MerchandiseCartItem;
use App\Models\EmailSetting;
use App\Models\MerchandiseEmailTemplate;
use App\Models\MerchandiseOrder;
use App\Models\MerchandiseOrderItem;
use App\Models\MerchandisePreorderAllocation;
use App\Models\MerchandiseProduct;
use App\Models\MerchandiseProductImage;
use App\Models\MerchandisePromotion;
use App\Models\MerchandiseStockIn;
use App\Models\MerchandiseStockInCostCorrection;
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
use Dompdf\Dompdf;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

use function App\Helpers\createXenditInvoice;
use function App\Helpers\imageToBase64;
use function App\Helpers\setupMail;

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
        $preorderAllocatedByVariant = $this->pendingPreorderAllocationsQuery();

        $lowStockVariants = MerchandiseVariant::query()
            ->with('product')
            ->leftJoinSub($stockTotals, 'stock_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_totals.variant_id');
            })
            ->leftJoinSub($stockOutByVariant, 'sold_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'sold_totals.variant_id');
            })
            ->leftJoinSub($preorderAllocatedByVariant, 'preorder_allocated_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'preorder_allocated_totals.variant_id');
            })
            ->select('merchandise_variants.*')
            ->selectRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0) '
                . '- COALESCE(preorder_allocated_totals.allocated_quantity, 0)) as current_stock')
            ->where('merchandise_variants.is_active', true)
            ->where('merchandise_variants.is_preorder', false)
            ->whereHas('product', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('category', function ($categoryQuery) {
                        $categoryQuery->where('is_active', true);
                    });
            })
            ->where('merchandise_variants.low_stock_threshold', '>', 0)
            ->whereRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0) '
                . '- COALESCE(preorder_allocated_totals.allocated_quantity, 0)) <= merchandise_variants.low_stock_threshold')
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
        $physicalStockOutTotals = $this->physicalStockOutItemsQuery()
            ->select('merchandise_stock_out_items.variant_id')
            ->selectRaw('SUM(merchandise_stock_out_items.quantity) as physical_sold_quantity')
            ->groupBy('merchandise_stock_out_items.variant_id');
        $preorderAllocatedTotals = $this->pendingPreorderAllocationsQuery();

        return MerchandiseVariant::query()
            ->with('product.category')
            ->leftJoinSub($stockTotals, 'stock_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'stock_totals.variant_id');
            })
            ->leftJoinSub($stockOutTotals, 'sold_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'sold_totals.variant_id');
            })
            ->leftJoinSub($physicalStockOutTotals, 'physical_sold_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'physical_sold_totals.variant_id');
            })
            ->leftJoinSub($preorderAllocatedTotals, 'preorder_allocated_totals', function ($join) {
                $join->on('merchandise_variants.id', '=', 'preorder_allocated_totals.variant_id');
            })
            ->select('merchandise_variants.*')
            ->selectRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(physical_sold_totals.physical_sold_quantity, 0)) as physical_stock')
            ->selectRaw('COALESCE(preorder_allocated_totals.allocated_quantity, 0) as allocated_preorder_stock')
            ->selectRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0)) as current_stock')
            ->selectRaw('(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0) '
                . '- COALESCE(preorder_allocated_totals.allocated_quantity, 0)) as available_for_sale')
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

    public function getEmailTemplates()
    {
        return MerchandiseEmailTemplate::query()
            ->orderBy('key')
            ->get();
    }

    public function updateEmailTemplate(string $key, array $data)
    {
        if (!in_array($key, ['invoice', 'receipt'], true)) {
            throw new \InvalidArgumentException('Unknown Merchandise email template.');
        }

        $template = MerchandiseEmailTemplate::where('key', $key)->firstOrFail();
        $template->update([
            'subject' => $data['subject'],
            'body' => $data['body'],
        ]);

        return $template->fresh();
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
                ->where('merchandise_orders.order_type', '!=', 'PREORDER')
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

    public function getPreorders()
    {
        $orders = MerchandiseOrder::with([
            'items.preorderAllocations.stockInItem.stockIn',
            'fulfilledBy',
        ])
            ->where('order_type', 'PREORDER')
            ->where('payment_status', 'PAID')
            ->orderByRaw("CASE fulfillment_status
                WHEN 'READY_FOR_FULFILLMENT' THEN 0
                WHEN 'PARTIALLY_AVAILABLE' THEN 1
                WHEN 'WAITING_FOR_STOCK' THEN 2
                ELSE 3 END")
            ->orderBy('paid_at')
            ->orderBy('id')
            ->paginate(25);

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $item->setAttribute(
                    'allocated_quantity',
                    (int) $item->preorderAllocations->sum('quantity')
                );
            }
        }

        return [
            'orders' => $orders,
            'stats' => [
                'waiting' => MerchandiseOrder::where('order_type', 'PREORDER')
                    ->where('payment_status', 'PAID')
                    ->where('fulfillment_status', 'WAITING_FOR_STOCK')
                    ->count(),
                'partial' => MerchandiseOrder::where('order_type', 'PREORDER')
                    ->where('payment_status', 'PAID')
                    ->where('fulfillment_status', 'PARTIALLY_AVAILABLE')
                    ->count(),
                'ready' => MerchandiseOrder::where('order_type', 'PREORDER')
                    ->where('payment_status', 'PAID')
                    ->where('fulfillment_status', 'READY_FOR_FULFILLMENT')
                    ->count(),
                'fulfilled' => MerchandiseOrder::where('order_type', 'PREORDER')
                    ->where('payment_status', 'PAID')
                    ->where('fulfillment_status', 'FULFILLED')
                    ->count(),
            ],
        ];
    }

    public function fulfillPreorder($orderId, $userId)
    {
        return DB::transaction(function () use ($orderId, $userId) {
            $order = MerchandiseOrder::with('items')
                ->lockForUpdate()
                ->findOrFail($orderId);

            if ($order->order_type !== 'PREORDER' || $order->payment_status !== 'PAID') {
                throw ValidationException::withMessages([
                    'order' => 'Only paid pre-orders can be fulfilled.',
                ]);
            }
            if ($order->fulfillment_status === 'FULFILLED') {
                return $order;
            }

            foreach ($order->items as $item) {
                $allocated = (int) MerchandisePreorderAllocation::where('order_item_id', $item->id)
                    ->sum('quantity');
                if ($allocated < (int) $item->quantity) {
                    throw ValidationException::withMessages([
                        'order' => 'All items must be allocated from Stock In before this order can be fulfilled.',
                    ]);
                }
            }

            if (!MerchandiseStockOut::where('order_id', $order->id)->exists()) {
                $this->createOrderStockOut($order, $order->items, now());
            }

            $order->update([
                'fulfillment_status' => 'FULFILLED',
                'fulfilled_at' => now(),
                'fulfilled_by' => $userId,
            ]);

            return $order->fresh(['items', 'fulfilledBy']);
        });
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
        return $order->order_type !== 'PREORDER'
            && $order->payment_status === 'PENDING'
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

    public function getPublicStore($cartKey, array $filters = [])
    {
        $query = $this->publicProductsQuery()
            ->join('merchandise_categories as store_categories', 'store_categories.id', '=', 'merchandise_products.category_id')
            ->select('merchandise_products.*')
            ->with('category', 'images')
            ->with(['variants' => function ($query) {
                $this->applyPublicVariantAvailability($query);
            }])
            ->orderBy('store_categories.display_order')
            ->orderBy('merchandise_products.display_order')
            ->orderBy('merchandise_products.name');

        if (!empty($filters['category_id'])) {
            $query->where('merchandise_products.category_id', $filters['category_id']);
        }
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($productQuery) use ($search) {
                $productQuery->where('merchandise_products.name', 'like', '%' . $search . '%')
                    ->orWhere('merchandise_products.description', 'like', '%' . $search . '%')
                    ->orWhereHas('variants', function ($variantQuery) use ($search) {
                        $variantQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('color', 'like', '%' . $search . '%')
                            ->orWhere('sku', 'like', '%' . $search . '%');
                    });
            });
        }

        $products = $query->paginate(24);

        return [
            'settings' => $this->getStoreSettings(),
            'products' => $products,
            'cartCount' => $this->cartItemCount($cartKey),
        ];
    }

    public function getPublicCategories()
    {
        return MerchandiseCategory::query()
            ->where('is_active', true)
            ->where('is_store_visible', true)
            ->whereIn('id', $this->publicProductsQuery()->select('category_id'))
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'description']);
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
            $available = $this->purchasableStock($item->variant);
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
            $item->setAttribute(
                'is_available',
                $isPublic && $item->quantity <= $available
            );
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
                'cart' => 'Select at least one item before proceeding to payment.',
            ]);
        }
        $orderType = $this->cartOrderType($cartData['items']);
        if ($orderType === 'PREORDER') {
            $cartData['promotionOptions'] = [];
            $cartData['promotionQuote'] = [
                'promotion' => null,
                'discount_amount' => 0,
                'gift_items' => [],
                'gift_options' => [],
                'selected_gift_variant_id' => null,
            ];
            $cartData['selectedPromotionId'] = null;
        } else {
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
        }
        $cartData['branches'] = Branch::orderBy('name')->get()->filter(function ($branch) {
            return (bool) config('services.xendit.api_key_' . strtolower($branch->name));
        })->values();
        $cartData['order_type'] = $orderType;
        $cartData['preorder_expected_at'] = $cartData['items']
            ->filter(function ($item) {
                return $item->variant->preorder_expected_at !== null;
            })
            ->map(function ($item) {
                return $item->variant->preorder_expected_at;
            })
            ->sort()
            ->last();

        return $cartData;
    }

    public function previewPromotion($cartKey, string $code)
    {
        $cartData = $this->getCart($cartKey);
        $selectedItems = $cartData['items']->where('is_selected', true)->values();
        if ($this->cartOrderType($selectedItems) === 'PREORDER') {
            throw ValidationException::withMessages([
                'promotion_code' => 'Promotions are not available for pre-orders.',
            ]);
        }
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
                'code' => 'This promo code is invalid, inactive, out of uses, or your cart does not meet its requirements.',
            ]);
        }

        return $voucher;
    }

    public function previewPromotionGift($cartKey, ?string $code, int $giftVariantId)
    {
        $cartData = $this->getCart($cartKey);
        $selectedItems = $cartData['items']->where('is_selected', true)->values();
        if ($this->cartOrderType($selectedItems) === 'PREORDER') {
            throw ValidationException::withMessages([
                'promotion_id' => 'Promotions are not available for pre-orders.',
            ]);
        }
        $quote = $this->promotionQuote($selectedItems, $code, true, $giftVariantId);
        if (empty($quote['gift_items'])) {
            throw ValidationException::withMessages([
                'gift_variant_id' => 'The selected gift is unavailable or does not qualify for this promotion.',
            ]);
        }

        return $quote;
    }

    public function previewPromotionSelection($cartKey, ?string $code, int $promotionId, ?int $giftVariantId = null)
    {
        $cartData = $this->getCart($cartKey);
        $selectedItems = $cartData['items']->where('is_selected', true)->values();
        if ($this->cartOrderType($selectedItems) === 'PREORDER') {
            throw ValidationException::withMessages([
                'promotion_id' => 'Promotions are not available for pre-orders.',
            ]);
        }
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

            if ($newQuantity > $this->purchasableStock($variant, true)) {
                throw ValidationException::withMessages([
                    'quantity' => 'The selected quantity exceeds available stock.',
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
                if ($isSelected
                    && (int) $itemData['quantity'] > $this->purchasableStock($variant, true)) {
                    throw ValidationException::withMessages([
                        'items' => 'The quantity for ' . $item->variant->product->name . ' exceeds available stock.',
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
                'payment' => 'The store is currently closed and cannot accept orders.',
            ]);
        }
        $branch = Branch::findOrFail($data['branch_id']);
        if (!config('services.xendit.api_key_' . strtolower($branch->name))) {
            throw ValidationException::withMessages([
                'branch_id' => 'Xendit payments are not configured for the selected branch.',
            ]);
        }

        $cartItemIds = [];
        $order = DB::transaction(function () use ($cartKey, $data, $branch, &$cartItemIds) {
            $cart = MerchandiseCart::where('cart_key', $cartKey)->lockForUpdate()->firstOrFail();
            $cartItems = $cart->items()->where('is_selected', true)->with('variant.product')->lockForUpdate()->get();
            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Select at least one item before proceeding to payment.',
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
            $preorderFlags = [];
            foreach ($cartItems as $cartItem) {
                $variant = $variants->get($cartItem->variant_id);
                if (!$variant) {
                    throw ValidationException::withMessages([
                        'cart' => 'One of the products in your cart is no longer available.',
                    ]);
                }
                $this->assertVariantPublic($variant);
                $preorderFlags[] = (bool) $variant->is_preorder;
                if ($cartItem->quantity > $this->purchasableStock($variant, true)) {
                    throw ValidationException::withMessages([
                        'cart' => 'There is not enough stock for ' . $variant->product->name . '. Update your cart before paying.',
                    ]);
                }
                $subtotal += (float) $variant->price * $cartItem->quantity;
                $totalItems += $cartItem->quantity;
                $cartItem->setRelation('variant', $variant);
                $cartItem->setAttribute('line_total', (float) $variant->price * $cartItem->quantity);
                $variant->setAttribute('available_stock', $this->purchasableStock($variant, true));
                $cartLines->push($cartItem);
            }
            if (count(array_unique($preorderFlags)) > 1) {
                throw ValidationException::withMessages([
                    'cart' => 'Pre-order items must be checked out separately from ready-stock items.',
                ]);
            }
            $orderType = !empty($preorderFlags) && $preorderFlags[0] ? 'PREORDER' : 'READY';
            if ($orderType === 'PREORDER'
                && (!empty($data['promotion_code'])
                    || !empty($data['promotion_id'])
                    || !empty($data['promotion_gift_variant_id']))) {
                throw ValidationException::withMessages([
                    'promotion_code' => 'Promotions are not available for pre-orders.',
                ]);
            }
            if ($subtotal <= 0) {
                throw ValidationException::withMessages([
                    'cart' => 'The payment total must be greater than Rp0 to create an invoice.',
                ]);
            }

            $promotionQuote = $orderType === 'PREORDER'
                ? [
                    'promotion' => null,
                    'discount_amount' => 0,
                    'gift_items' => [],
                    'gift_options' => [],
                    'selected_gift_variant_id' => null,
                ]
                : $this->promotionQuote(
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
                        'promotion_code' => 'This promotion is no longer eligible or has reached its usage limit.',
                    ]);
                }
                if (!empty($promotionQuote['gift_options']) && empty($promotionQuote['gift_items'])) {
                    throw ValidationException::withMessages([
                        'promotion_gift_variant_id' => 'Choose a promotional gift before continuing.',
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
                'order_type' => $orderType,
                'fulfillment_status' => $orderType === 'PREORDER' ? 'AWAITING_PAYMENT' : 'READY',
                'preorder_expected_at' => $orderType === 'PREORDER'
                    ? $cartLines->pluck('variant.preorder_expected_at')->filter()->sort()->last()
                    : null,
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
                    'is_preorder' => (bool) $variant->is_preorder,
                    'preorder_expected_at' => $variant->is_preorder ? $variant->preorder_expected_at : null,
                ]);
            }
            foreach ($giftItems as $giftItem) {
                $giftVariant = MerchandiseVariant::with('product')->findOrFail($giftItem['variant_id']);
                $order->items()->create([
                    'variant_id' => $giftVariant->id,
                    'product_name' => $giftVariant->product->name,
                    'variant_name' => trim(($giftVariant->color ? $giftVariant->color . ' / ' : '') . $giftVariant->name)
                            . ' (Promotional gift)',
                    'sku' => $giftVariant->sku,
                    'image_path' => $giftVariant->image_path ?: $giftVariant->product->image_path,
                    'quantity' => $giftItem['quantity'],
                    'unit_price' => 0,
                    'subtotal' => 0,
                ]);
            }

            if ($orderType !== 'PREORDER') {
                $this->createOrderStockOut($order, $order->items);
            }

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
                'payment' => 'Unable to connect to Xendit. Please try again.',
            ]);
        }

        if (!empty($xendit['success']) && $xendit['success'] === false) {
            $order->update(['payment_status' => 'FAILED']);
            throw ValidationException::withMessages([
                'payment' => 'Failed to create the Xendit invoice: ' . ($xendit['message'] ?? 'Unknown Xendit error.'),
            ]);
        }
        if (empty($xendit['status']) || empty($xendit['invoice_url'])
            || empty($xendit['created']) || empty($xendit['expiry_date'])) {
            $order->update(['payment_status' => 'FAILED']);
            throw ValidationException::withMessages([
                'payment' => 'The Xendit response was incomplete. Please try again.',
            ]);
        }

        $order->update([
            'payment_url' => $xendit['invoice_url'],
            'expires_at' => Carbon::parse($xendit['expiry_date']),
        ]);
        $this->sendMerchandiseInvoiceEmail($order->fresh('items'));
        MerchandiseCart::where('cart_key', $cartKey)->firstOrFail()->items()->whereIn('id', $cartItemIds)->delete();

        return $order->fresh();
    }

    public function processOrderPaymentCallback(string $externalId, array $callbackData)
    {
        $status = strtoupper($callbackData['status'] ?? '');
        $mappedStatus = in_array($status, ['PAID', 'SETTLED'], true)
            ? 'PAID'
            : (in_array($status, ['EXPIRED', 'FAILED'], true) ? $status : 'PENDING');

        $result = DB::transaction(function () use ($externalId, $callbackData, $mappedStatus) {
            $order = MerchandiseOrder::where('external_id', $externalId)->lockForUpdate()->first();
            if (!$order) {
                return false;
            }
            if ($order->payment_status === 'PAID') {
                return [
                    'order_id' => $order->id,
                    'send_receipt' => !$order->receipt_email_sent_at,
                ];
            }
            if (in_array($order->payment_status, ['FAILED', 'EXPIRED'], true) && $mappedStatus === 'PENDING') {
                return [
                    'order_id' => $order->id,
                    'send_receipt' => false,
                ];
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
                'fulfillment_status' => $order->order_type === 'PREORDER'
                    ? ($mappedStatus === 'PAID' ? 'WAITING_FOR_STOCK' : 'CANCELLED')
                    : $order->fulfillment_status,
            ]);

            if ($mappedStatus === 'PAID' && $order->order_type === 'PREORDER') {
                    foreach ($orderItems->pluck('variant_id')->unique() as $variantId) {
                        $this->allocateExistingPreorderStockForVariant((int) $variantId, null);
                    }
            }

            if ($mappedStatus === 'PAID'
                && $order->order_type !== 'PREORDER'
                && !MerchandiseStockOut::where('order_id', $order->id)->exists()) {
                $this->createOrderStockOut($order, $orderItems, $order->paid_at ?: now());
            }

            return [
                'order_id' => $order->id,
                'send_receipt' => $mappedStatus === 'PAID' && !$order->receipt_email_sent_at,
            ];
        });

        if ($result === false) {
            return false;
        }

        if ($result['send_receipt']) {
            $this->sendMerchandiseReceiptEmail(
                MerchandiseOrder::with('items')->findOrFail($result['order_id']),
                $callbackData
            );
        }

        return true;
    }

    public function getStockIns($limit = null)
    {
        $query = MerchandiseStockIn::with('creator', 'items.variant.product', 'items.costCorrections.corrector')
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

                $stockInItem = MerchandiseStockInItem::create([
                    'stock_in_id' => $stockIn->id,
                    'variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
                $this->allocatePreorderStock($stockInItem, $userId);
            }

            return $stockIn->load('items.variant.product');
        });
    }

    public function correctStockInCost($stockInItemId, $unitCost, $reason, $userId)
    {
        if (!is_numeric($unitCost) || !is_finite((float) $unitCost) || (float) $unitCost < 0) {
            throw ValidationException::withMessages([
                'unit_cost' => 'Harga modal harus berupa angka nol atau lebih.',
            ]);
        }

        return DB::transaction(function () use ($stockInItemId, $unitCost, $reason, $userId) {
            $item = MerchandiseStockInItem::lockForUpdate()->findOrFail($stockInItemId);
            $oldCost = (float) $item->unit_cost;
            $newCost = round((float) $unitCost, 2);
            if ($oldCost === $newCost) {
                throw ValidationException::withMessages([
                    'unit_cost' => 'Harga modal baru sama dengan harga modal saat ini.',
                ]);
            }

            $item->update(['unit_cost' => $newCost]);

            return MerchandiseStockInCostCorrection::create([
                'stock_in_item_id' => $item->id,
                'old_unit_cost' => $oldCost,
                'new_unit_cost' => $newCost,
                'reason' => $reason,
                'corrected_by' => $userId,
            ]);
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
                'sku' => trim((string) ($variantData['sku'] ?? '')) ?: ($variant ? $variant->sku : null),
                'image_path' => $variantData['stored_image_path'] ?? ($variant ? $variant->image_path : null),
                'price' => $variantData['price'] ?? ($variant ? $variant->price : 0),
                'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 0,
                'is_active' => $variantData['is_active'] ?? false,
                'is_preorder' => $variantData['is_preorder'] ?? false,
                'preorder_expected_at' => !empty($variantData['is_preorder'])
                    ? ($variantData['preorder_expected_at'] ?? null)
                    : null,
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
            if (!$variant->sku) {
                $variant->update(['sku' => $this->generateVariantSku($product, $variant)]);
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

    private function generateVariantSku(MerchandiseProduct $product, MerchandiseVariant $variant): string
    {
        $base = sprintf('MRC-%04d-%04d', $product->id, $variant->id);
        $sku = $base;
        $suffix = 1;
        while (MerchandiseVariant::where('sku', $sku)->where('id', '!=', $variant->id)->exists()) {
            $sku = $base . '-' . $suffix++;
        }

        return $sku;
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
            if (!empty($variantData['is_preorder']) && empty($variantData['preorder_expected_at'])) {
                throw ValidationException::withMessages([
                    'variants' => 'Setiap varian pre-order wajib memiliki estimasi tanggal barang tersedia.',
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
                $query->where('merchandise_variants.is_active', true);
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
            ->where('merchandise_orders.order_type', '!=', 'PREORDER')
            ->where('merchandise_orders.expires_at', '>', now())
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('merchandise_stock_outs')
                    ->whereColumn('merchandise_stock_outs.order_id', 'merchandise_orders.id');
            })
            ->select('merchandise_order_items.variant_id')
            ->selectRaw('SUM(merchandise_order_items.quantity) as reserved_quantity')
            ->groupBy('merchandise_order_items.variant_id');
        $preorderAllocatedTotals = $this->pendingPreorderAllocationsQuery();
        $preorderDemandTotals = $this->preorderDemandQuery()
            ->select('merchandise_order_items.variant_id')
            ->selectRaw('SUM(merchandise_order_items.quantity) as demand_quantity')
            ->groupBy('merchandise_order_items.variant_id');

        $query->leftJoinSub($stockTotals, 'stock_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'stock_totals.variant_id');
        })->leftJoinSub($preorderDemandTotals, 'preorder_demand_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'preorder_demand_totals.variant_id');
        })->leftJoinSub($stockOutTotals, 'sold_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'sold_totals.variant_id');
        })->leftJoinSub($reservedTotals, 'reserved_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'reserved_totals.variant_id');
        })->leftJoinSub($preorderAllocatedTotals, 'preorder_allocated_totals', function ($join) {
            $join->on('merchandise_variants.id', '=', 'preorder_allocated_totals.variant_id');
        });

        $netStock = '(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0) '
            . '- COALESCE(preorder_allocated_totals.allocated_quantity, 0))';
        $preorderQuota = '(COALESCE(stock_totals.current_stock, 0) - COALESCE(sold_totals.sold_quantity, 0) '
            . '- COALESCE(preorder_demand_totals.demand_quantity, 0))';
        $onlineStock = '(CASE WHEN merchandise_variants.online_stock_limit IS NOT NULL AND merchandise_variants.online_stock_limit < '
            . $netStock . ' THEN merchandise_variants.online_stock_limit ELSE ' . $netStock . ' END)';
        $availableStock = '(' . $onlineStock . ' - COALESCE(reserved_totals.reserved_quantity, 0))';

        $query->select('merchandise_variants.*')
            ->selectRaw('CASE WHEN merchandise_variants.is_preorder = 1 THEN (CASE WHEN ' . $preorderQuota
                . ' > 0 THEN ' . $preorderQuota . ' ELSE 0 END) WHEN '
                . $availableStock . ' > 0 THEN ' . $availableStock . ' ELSE 0 END as available_stock')
            ->where('merchandise_variants.is_active', true);
    }

    private function purchasableStock($variant, bool $lockForUpdate = false): int
    {
        return $variant->is_preorder
            ? $this->preorderQuotaForVariant($variant->id, $lockForUpdate)
            : $this->availableStockForVariant($variant->id, null, $lockForUpdate);
    }

    private function preorderDemandQuery()
    {
        return DB::table('merchandise_order_items')
            ->join('merchandise_orders', 'merchandise_orders.id', '=', 'merchandise_order_items.order_id')
            ->where('merchandise_orders.order_type', 'PREORDER')
            ->where(function ($query) {
                $query->where(function ($paid) {
                    $paid->where('merchandise_orders.payment_status', 'PAID')
                        ->whereNotIn('merchandise_orders.fulfillment_status', ['FULFILLED', 'CANCELLED']);
                })->orWhere(function ($pending) {
                    $pending->where('merchandise_orders.payment_status', 'PENDING')
                        ->where('merchandise_orders.expires_at', '>', now());
                });
            });
    }

    private function preorderQuotaForVariant($variantId, bool $lockForUpdate = false): int
    {
        $stockIn = (int) MerchandiseStockInItem::where('variant_id', $variantId)->sum('quantity');
        $stockOut = (int) $this->activeStockOutItemsQuery()
            ->where('merchandise_stock_out_items.variant_id', $variantId)
            ->sum('merchandise_stock_out_items.quantity');
        $demandQuery = $this->preorderDemandQuery()
            ->where('merchandise_order_items.variant_id', $variantId);
        $demand = $lockForUpdate
            ? (int) $demandQuery->lockForUpdate()->get(['merchandise_order_items.quantity'])->sum('quantity')
            : (int) $demandQuery->sum('merchandise_order_items.quantity');

        return max(0, $stockIn - $stockOut - $demand);
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
        $allocatedPreorderStock = (int) DB::table('merchandise_preorder_allocations')
            ->join(
                'merchandise_order_items as allocated_items',
                'allocated_items.id',
                '=',
                'merchandise_preorder_allocations.order_item_id'
            )
            ->join('merchandise_orders as allocated_orders', 'allocated_orders.id', '=', 'allocated_items.order_id')
            ->where('allocated_items.variant_id', $variantId)
            ->where('allocated_orders.order_type', 'PREORDER')
            ->where('allocated_orders.payment_status', 'PAID')
            ->where('allocated_orders.fulfillment_status', '!=', 'FULFILLED')
            ->sum('merchandise_preorder_allocations.quantity');
        $reservationQuery = DB::table('merchandise_order_items')
            ->join('merchandise_orders', 'merchandise_orders.id', '=', 'merchandise_order_items.order_id')
            ->where('merchandise_order_items.variant_id', $variantId)
            ->where('merchandise_orders.payment_status', 'PENDING')
            ->where('merchandise_orders.order_type', '!=', 'PREORDER')
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
        $unallocatedStock = max(0, $physicalStock - $allocatedPreorderStock);
        $available = $variant->is_preorder || $variant->online_stock_limit === null
            ? $unallocatedStock
            : min($unallocatedStock, (int) $variant->online_stock_limit);

        return max(0, $available - $reserved);
    }

    private function pendingPreorderAllocationsQuery()
    {
        return DB::table('merchandise_preorder_allocations')
            ->join(
                'merchandise_order_items as allocated_items',
                'allocated_items.id',
                '=',
                'merchandise_preorder_allocations.order_item_id'
            )
            ->join('merchandise_orders as allocated_orders', 'allocated_orders.id', '=', 'allocated_items.order_id')
            ->where('allocated_orders.order_type', 'PREORDER')
            ->where('allocated_orders.payment_status', 'PAID')
            ->where('allocated_orders.fulfillment_status', '!=', 'FULFILLED')
            ->select('allocated_items.variant_id')
            ->selectRaw('SUM(merchandise_preorder_allocations.quantity) as allocated_quantity')
            ->groupBy('allocated_items.variant_id');
    }

    private function cartOrderType($items): string
    {
        $types = $items->map(function ($item) {
            return (bool) $item->variant->is_preorder;
        })->unique();

        if ($types->count() > 1) {
            throw ValidationException::withMessages([
                'cart' => 'Pre-order items must be checked out separately from ready-stock items.',
            ]);
        }

        return $types->first() ? 'PREORDER' : 'READY';
    }

    private function allocatePreorderStock(MerchandiseStockInItem $stockInItem, $userId): int
    {
        $alreadyAssigned = (int) MerchandisePreorderAllocation::where('stock_in_item_id', $stockInItem->id)
            ->sum('quantity');
        $remaining = max(0, (int) $stockInItem->quantity - $alreadyAssigned);
        if ($remaining < 1) {
            return 0;
        }

        $initialQuantity = $remaining;
        $orderItems = MerchandiseOrderItem::query()
            ->join('merchandise_orders as preorder_orders', 'preorder_orders.id', '=', 'merchandise_order_items.order_id')
            ->where('variant_id', $stockInItem->variant_id)
            ->where('preorder_orders.order_type', 'PREORDER')
            ->where('preorder_orders.payment_status', 'PAID')
            ->whereIn('preorder_orders.fulfillment_status', ['WAITING_FOR_STOCK', 'PARTIALLY_AVAILABLE'])
            ->select('merchandise_order_items.*')
            ->with('order')
            ->orderBy('preorder_orders.paid_at')
            ->orderBy('merchandise_order_items.order_id')
            ->orderBy('merchandise_order_items.id')
            ->lockForUpdate()
            ->get();
        $affectedOrderIds = [];

        foreach ($orderItems as $orderItem) {
            $alreadyAllocated = (int) MerchandisePreorderAllocation::where('order_item_id', $orderItem->id)
                ->sum('quantity');
            $needed = max(0, (int) $orderItem->quantity - $alreadyAllocated);
            $quantity = min($remaining, $needed);
            if ($quantity < 1) {
                continue;
            }

            MerchandisePreorderAllocation::create([
                'order_item_id' => $orderItem->id,
                'stock_in_item_id' => $stockInItem->id,
                'quantity' => $quantity,
                'allocated_by' => $userId,
            ]);
            $remaining -= $quantity;
            $affectedOrderIds[$orderItem->order_id] = true;
            if ($remaining === 0) {
                break;
            }
        }

        foreach (array_keys($affectedOrderIds) as $orderId) {
            $order = MerchandiseOrder::with('items')->findOrFail($orderId);
            $allocatedUnits = 0;
            $allItemsAllocated = true;
            foreach ($order->items as $item) {
                $quantity = (int) MerchandisePreorderAllocation::where('order_item_id', $item->id)
                    ->sum('quantity');
                $allocatedUnits += $quantity;
                if ($quantity < (int) $item->quantity) {
                    $allItemsAllocated = false;
                }
            }
            $order->update([
                'fulfillment_status' => $allItemsAllocated
                    ? 'READY_FOR_FULFILLMENT'
                    : ($allocatedUnits > 0 ? 'PARTIALLY_AVAILABLE' : 'WAITING_FOR_STOCK'),
            ]);
        }

        return $initialQuantity - $remaining;
    }

    private function allocateExistingPreorderStockForVariant(int $variantId, $userId): void
    {
        $remaining = $this->availableStockForVariant($variantId, null, true);
        if ($remaining < 1) {
            return;
        }

        $stockInItems = MerchandiseStockInItem::where('variant_id', $variantId)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($stockInItems as $stockInItem) {
            if ($remaining < 1) {
                break;
            }
            $allocated = $this->allocatePreorderStock($stockInItem, $userId);
            $remaining -= $allocated;
        }
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
                    'promotion_code' => 'Add items to your cart before applying a promotion.',
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
                    'promotion_id' => 'The selected promotion is no longer available or eligible.',
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
                'promotion_id' => 'The selected promotion is no longer available or eligible.',
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
                'variant_id' => 'This product variant is currently unavailable in the store.',
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

    private function sendMerchandiseInvoiceEmail(MerchandiseOrder $order): void
    {
        try {
            $template = MerchandiseEmailTemplate::where('key', 'invoice')->firstOrFail();
            $data = $this->merchandiseEmailData($order, $template, []);
            $this->configureMerchandiseMail($order->branch_id);

            Mail::to($order->email)->send(new AdmissionEmail($data));
            $order->update(['invoice_email_sent_at' => now()]);
        } catch (Throwable $exception) {
            Log::error('Merchandise invoice email could not be sent.', [
                'order_id' => $order->id,
                'order_code' => $order->code,
                'recipient' => $order->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function sendMerchandiseReceiptEmail(MerchandiseOrder $order, array $callbackData): void
    {
        $template = MerchandiseEmailTemplate::where('key', 'receipt')->firstOrFail();
        $data = $this->merchandiseEmailData($order, $template, [
            'email_title' => 'Merchandise Payment Confirmation',
            'payment_date' => !empty($callbackData['paid_at'])
                ? Carbon::parse($callbackData['paid_at'])->setTimezone('Asia/Jakarta')->format('d M Y H:i')
                : optional($order->paid_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i'),
        ]);
        $pdfPath = $this->generateMerchandiseReceiptPdf($order, $callbackData['description'] ?? null);
        $this->configureMerchandiseMail($order->branch_id);

        Mail::to($order->email)->send(
            (new AdmissionEmail($data))->attach($pdfPath, [
                'as' => 'Receipt-' . $order->code . '.pdf',
                'mime' => 'application/pdf',
            ])
        );

        $order->update(['receipt_email_sent_at' => now()]);
    }

    private function merchandiseEmailData(
        MerchandiseOrder $order,
        MerchandiseEmailTemplate $template,
        array $overrides
    ): array {
        $order->loadMissing('items');
        $itemsHtml = '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%">'
            . '<thead><tr><th align="left">Product</th><th align="right">Qty</th><th align="right">Amount</th></tr></thead><tbody>';
        $paymentRows = '';
        foreach ($order->items as $item) {
            $preorderLabel = $item->is_preorder
                ? '<br><span style="font-size:11px;color:#8a5a00">Pre-order · Expected '
                    . e($item->preorder_expected_at ? $item->preorder_expected_at->format('d M Y') : 'to be confirmed')
                    . '</span>'
                : '';
            $itemsHtml .= '<tr><td>' . e($item->product_name . ' - ' . $item->variant_name) . $preorderLabel
                . '</td><td align="right">' . (int) $item->quantity
                . '</td><td align="right">' . e($this->formatMerchandiseAmount($item->subtotal)) . '</td></tr>';
            $paymentRows .= '<tr>'
                . '<td style="padding:9px 7px;border-bottom:1px solid #f0f0f0;text-align:left;vertical-align:top;word-break:break-word">'
                . e($item->product_name) . '<br><span style="font-size:11px;color:#666">' . e($item->variant_name) . '</span>'
                . $preorderLabel . '</td>'
                . '<td style="padding:9px 5px;border-bottom:1px solid #f0f0f0;text-align:center;vertical-align:top">'
                . (int) $item->quantity . '</td>'
                . '<td style="padding:9px 6px;border-bottom:1px solid #f0f0f0;text-align:right;vertical-align:top;white-space:nowrap">'
                . e($this->formatMerchandiseAmount($item->subtotal)) . '</td></tr>';
        }
        $itemsHtml .= '</tbody></table>';
        $paymentTable = '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" '
            . 'style="width:100%;max-width:100%;table-layout:fixed;border-collapse:separate;border-spacing:0;border:1px solid #e8e4e0;border-radius:8px;overflow:hidden;font-size:13px;line-height:1.5">'
            . '<thead><tr>'
            . '<th width="56%" style="width:56%;padding:13px 10px;background-color:#faf9f7;color:#800000;text-align:left;border-bottom:1px solid #e8e4e0;font-size:13px">Product</th>'
            . '<th width="12%" style="width:12%;padding:13px 3px;background-color:#faf9f7;color:#800000;text-align:center;border-bottom:1px solid #e8e4e0;font-size:13px">Qty</th>'
            . '<th width="32%" style="width:32%;padding:13px 7px;background-color:#faf9f7;color:#800000;text-align:right;border-bottom:1px solid #e8e4e0;font-size:13px">Amount</th>'
            . '</tr></thead><tbody>' . $paymentRows
            . '<tr><td colspan="2" style="padding:10px 10px;border-top:1px solid #e8e4e0">Subtotal</td>'
            . '<td style="padding:10px 7px;text-align:right;white-space:nowrap;border-top:1px solid #e8e4e0">'
            . e($this->formatMerchandiseAmount($order->subtotal)) . '</td></tr>'
            . '<tr><td colspan="2" style="padding:9px 10px">Discount</td>'
            . '<td style="padding:9px 7px;text-align:right;white-space:nowrap">'
            . ($order->discount_amount > 0 ? '-' : '') . e($this->formatMerchandiseAmount($order->discount_amount)) . '</td></tr>'
            . '<tr><td colspan="2" style="padding:9px 10px;border-bottom:2px solid #e8e4e0">Admin fee</td>'
            . '<td style="padding:9px 7px;text-align:right;white-space:nowrap;border-bottom:2px solid #e8e4e0">'
            . e($this->formatMerchandiseAmount($order->bank_charger)) . '</td></tr>'
            . '<tr><td colspan="2" style="padding:13px 10px;background-color:#fcfaf8;color:#800000;font-weight:bold">Total payment</td>'
            . '<td style="padding:13px 7px;background-color:#fcfaf8;color:#800000;text-align:right;white-space:nowrap;font-weight:bold">'
            . e($this->formatMerchandiseAmount($order->total_amount)) . '</td></tr>'
            . '</tbody></table>';

        $values = array_merge([
            'parent_name' => e($order->parent_name),
            'order_code' => e($order->code),
            'order_date' => $this->formatMerchandiseDate($order->created_at),
            'branch_name' => e($order->branch_name),
            'portal_username' => e($order->portal_username),
            'email' => e($order->email),
            'phone' => e($order->phone),
            'order_items' => $itemsHtml,
            'payment_table' => $paymentTable,
            'subtotal' => e($this->formatMerchandiseAmount($order->subtotal)),
            'discount' => e($this->formatMerchandiseAmount($order->discount_amount)),
            'admin_fee' => e($this->formatMerchandiseAmount($order->bank_charger)),
            'total' => e($this->formatMerchandiseAmount($order->total_amount)),
            'payment_url' => e($order->payment_url ?? ''),
            'expires_at' => $this->formatMerchandiseDate($order->expires_at),
            'payment_date' => $this->formatMerchandiseDate($order->paid_at),
            'payment_status' => e($order->payment_status),
        ], $overrides);

        $body = $this->normalizeMerchandiseEmailBody($template->body);

        return [
            'subject' => $this->replaceMerchandiseEmailVariables($template->subject, $values),
            'template' => $template->key === 'receipt'
                ? 'email-template.merchandise-payment'
                : 'email-template.merchandise',
            'title' => $overrides['email_title']
                ?? $this->replaceMerchandiseEmailVariables($template->subject, $values),
            'content' => $this->styleMerchandisePaymentButton(
                $this->replaceMerchandiseEmailVariables($body, $values)
            ),
            'parent_name' => e($order->parent_name),
        ];
    }

    private function normalizeMerchandiseEmailBody(string $body): string
    {
        if (stripos($body, '{{ payment_table }}') !== false
            || stripos($body, '{{payment_table}}') !== false
            || stripos($body, '{payment_table}') !== false) {
            return $body;
        }

        $legacyReplaced = false;
        $normalized = preg_replace_callback('/<p\b[^>]*>[\s\S]*?<\/p>/i', function ($matches) use (&$legacyReplaced) {
            if ($legacyReplaced) {
                return $matches[0];
            }

            foreach (['subtotal', 'discount', 'admin_fee', 'total'] as $key) {
                if (!preg_match('/\{\{\s*' . $key . '\s*\}\}/i', $matches[0])) {
                    return $matches[0];
                }
            }

            $legacyReplaced = true;

            return '{{ payment_table }}';
        }, $body);

        if ($legacyReplaced) {
            return str_ireplace(
                ['{{ order_items }}', '{{order_items}}', '{order_items}'],
                '',
                $normalized
            );
        }

        return $body;
    }

    private function styleMerchandisePaymentButton(string $body): string
    {
        return preg_replace_callback('/<a\b([^>]*)>(.*?)<\/a>/is', function ($matches) {
            if (stripos(strip_tags($matches[2]), 'pay now') === false) {
                return $matches[0];
            }

            $attributes = preg_replace('/\sstyle\s*=\s*(["\']).*?\1/is', '', $matches[1]);
            $style = 'background-color:#800000;border:2px solid #800000;border-radius:8px;color:#ffffff;'
                . 'display:block;margin:0 auto;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;'
                . 'line-height:48px;text-align:center;text-decoration:none;width:260px;max-width:100%;'
                . 'box-sizing:border-box;-webkit-text-size-adjust:none;';

            return '<a' . $attributes . ' style="' . $style . '">' . $matches[2] . '</a>';
        }, $body);
    }

    private function replaceMerchandiseEmailVariables(string $content, array $values): string
    {
        foreach ($values as $key => $value) {
            $content = str_replace([
                '{{ ' . $key . ' }}',
                '{{' . $key . '}}',
                '{' . $key . '}',
            ], (string) $value, $content);
        }

        return $content;
    }

    private function configureMerchandiseMail($branchId): void
    {
        if (!EmailSetting::where('branch_id', $branchId)->exists()) {
            throw new RuntimeException('Email settings are not configured for the order branch.');
        }

        setupMail($branchId);
    }

    private function generateMerchandiseReceiptPdf(MerchandiseOrder $order, ?string $description): string
    {
        $html = view('pdf.merchandise-receipt', [
            'order' => $order,
            'description' => $description ?: 'Merchandise order payment',
            'logo' => imageToBase64(public_path('assets/images/Logo-all-branch.png')),
        ])->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4');
        $pdf->render();

        $path = 'merchandise/' . $order->code . '/receipt-' . $order->code . '.pdf';
        if (!Storage::disk('admission')->put($path, $pdf->output())) {
            throw new RuntimeException('The Merchandise payment receipt PDF could not be saved.');
        }

        return Storage::disk('admission')->path($path);
    }

    private function formatMerchandiseAmount($amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }

    private function formatMerchandiseDate($date): string
    {
        return $date
            ? Carbon::parse($date)->setTimezone('Asia/Jakarta')->format('d M Y H:i')
            : '';
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
