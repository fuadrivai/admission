<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MerchandiseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MerchandiseApiController extends Controller
{
    private MerchandiseService $merchandiseService;

    public function __construct(MerchandiseService $merchandiseService)
    {
        $this->merchandiseService = $merchandiseService;
    }

    public function store(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:merchandise_categories,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $data = $this->merchandiseService->getPublicStore(
            $this->optionalCartToken($request),
            $filters
        );
        $products = $data['settings']->is_open
            ? $data['products']->getCollection()
            : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'store' => [
                    'name' => $data['settings']->store_name,
                    'description' => $data['settings']->description,
                    'is_open' => (bool) $data['settings']->is_open,
                    'closed_message' => $data['settings']->closed_message,
                ],
                'categories' => $this->merchandiseService->getPublicCategories()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'description' => $category->description,
                        ];
                    })->values(),
                'products' => $products
                    ->map(function ($product) {
                        return $this->productData($product);
                    })->values(),
                'cart_count' => $data['cartCount'],
                'pagination' => $this->paginationData($data['products'], $filters, $data['settings']->is_open),
            ],
        ]);
    }

    public function categories()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $this->merchandiseService->getPublicCategories()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'description' => $category->description,
                        ];
                    })->values(),
            ],
        ]);
    }

    public function products(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:merchandise_categories,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $data = $this->merchandiseService->getPublicStore(
            $this->optionalCartToken($request),
            $filters
        );
        $products = $data['settings']->is_open
            ? $data['products']->getCollection()
            : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products->map(function ($product) {
                    return $this->productData($product);
                })->values(),
                'cart_count' => $data['cartCount'],
                'pagination' => $this->paginationData(
                    $data['products'],
                    $filters,
                    $data['settings']->is_open
                ),
            ],
        ]);
    }

    public function product($productId)
    {
        $data = $this->merchandiseService->getPublicProduct($productId, '');

        return response()->json([
            'success' => true,
            'data' => [
                'store' => [
                    'name' => $data['settings']->store_name,
                    'is_open' => (bool) $data['settings']->is_open,
                    'closed_message' => $data['settings']->closed_message,
                ],
                'product' => $this->productData($data['product'], $data['variants']),
            ],
        ]);
    }

    public function createCart()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'cart_token' => Str::random(64),
            ],
        ], 201);
    }

    public function cart(Request $request)
    {
        $data = $this->merchandiseService->getCart($this->cartToken($request));

        return response()->json([
            'success' => true,
            'data' => $this->cartData($data),
        ]);
    }

    public function addCartItem(Request $request)
    {
        $cartToken = $this->cartToken($request);
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:merchandise_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);
        $this->merchandiseService->addToCart($cartToken, $data['variant_id'], $data['quantity']);

        return response()->json([
            'success' => true,
            'data' => $this->cartData($this->merchandiseService->getCart($cartToken)),
        ], 201);
    }

    public function updateCartItem(Request $request, $itemId)
    {
        $cartToken = $this->cartToken($request);
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'is_selected' => ['sometimes', 'boolean'],
        ]);
        $cart = $this->merchandiseService->getCart($cartToken);
        $item = $cart['items']->firstWhere('id', (int) $itemId);
        abort_unless($item, 404, 'Cart item not found.');

        $this->merchandiseService->updateCart($cartToken, [[
            'id' => $item->id,
            'quantity' => $data['quantity'],
            'is_selected' => array_key_exists('is_selected', $data)
                ? (bool) $data['is_selected']
                : (bool) $item->is_selected,
        ]]);

        return response()->json([
            'success' => true,
            'data' => $this->cartData($this->merchandiseService->getCart($cartToken)),
        ]);
    }

    public function removeCartItem(Request $request, $itemId)
    {
        $cartToken = $this->cartToken($request);
        $cart = $this->merchandiseService->getCart($cartToken);
        abort_unless($cart['items']->contains('id', (int) $itemId), 404, 'Cart item not found.');
        $this->merchandiseService->removeCartItem($cartToken, $itemId);

        return response()->json([
            'success' => true,
            'data' => $this->cartData($this->merchandiseService->getCart($cartToken)),
        ]);
    }

    public function checkout(Request $request)
    {
        $cartToken = $this->cartToken($request);
        $filters = $request->validate([
            'promotion_code' => ['nullable', 'string', 'max:50'],
            'promotion_id' => ['nullable', 'integer', 'exists:merchandise_promotions,id'],
            'gift_variant_id' => ['nullable', 'integer', 'exists:merchandise_variants,id'],
        ]);
        $data = $this->merchandiseService->getCheckout(
            $cartToken,
            $filters['promotion_code'] ?? null,
            isset($filters['gift_variant_id']) ? (int) $filters['gift_variant_id'] : null,
            isset($filters['promotion_id']) ? (int) $filters['promotion_id'] : null
        );

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $data['items']->map(function ($item) {
                    return $this->cartItemData($item);
                })->values(),
                'items_count' => $data['totalItems'],
                'order_type' => $data['order_type'],
                'preorder_expected_at' => $data['preorder_expected_at']
                    ? $data['preorder_expected_at']->toDateString()
                    : null,
                'subtotal' => $data['subtotal'],
                'admin_fee' => $data['bank_charger'],
                'total' => max(0, $data['subtotal'] - min(
                    $data['subtotal'],
                    $data['promotionQuote']['discount_amount']
                )) + $data['bank_charger'],
                'promotions' => collect($data['promotionOptions'])->map(function ($option) {
                    return [
                        'id' => $option['promotion']->id,
                        'name' => $option['promotion']->name,
                        'code' => $option['promotion']->code,
                        'type' => $option['promotion']->type,
                        'discount_amount' => $option['discount_amount'],
                        'gift_options' => $option['gift_options'],
                    ];
                })->values(),
                'selected_promotion' => $data['promotionQuote']['promotion'] ? [
                    'id' => $data['promotionQuote']['promotion']->id,
                    'name' => $data['promotionQuote']['promotion']->name,
                    'code' => $data['promotionQuote']['promotion']->code,
                    'discount_amount' => $data['promotionQuote']['discount_amount'],
                    'gift_items' => $data['promotionQuote']['gift_items'],
                ] : null,
                'branches' => $data['branches']->map(function ($branch) {
                    return ['id' => $branch->id, 'name' => $branch->name];
                })->values(),
            ],
        ]);
    }

    public function createOrder(Request $request)
    {
        $cartToken = $this->cartToken($request);
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'parent_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:40'],
            'portal_username' => ['required', 'string', 'max:100'],
            'promotion_code' => ['nullable', 'string', 'max:50'],
            'promotion_id' => ['nullable', 'integer', 'exists:merchandise_promotions,id'],
            'promotion_gift_variant_id' => ['nullable', 'integer', 'exists:merchandise_variants,id'],
        ]);
        $order = $this->merchandiseService->createOrderAndInvoice($cartToken, $data);

        return response()->json([
            'success' => true,
            'data' => [
                'order_reference' => $order->external_id,
                'payment_status' => $order->payment_status,
                'order_type' => $order->order_type,
                'fulfillment_status' => $order->fulfillment_status,
                'preorder_expected_at' => $order->preorder_expected_at
                    ? $order->preorder_expected_at->toDateString()
                    : null,
                'amount' => $order->total_amount,
                'expires_at' => $order->expires_at ? $order->expires_at->toIso8601String() : null,
                'payment_url' => $order->payment_url,
            ],
        ], 201);
    }

    private function productData($product, $variants = null): array
    {
        $variants = $variants ?: $product->variants;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'image_url' => $this->imageUrl($product->image_path),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
            'images' => $product->images->map(function ($image) {
                return $this->imageUrl($image->image_path);
            })->filter()->values(),
            'variants' => $variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'color' => $variant->color,
                    'sku' => $variant->sku,
                    'price' => (float) $variant->price,
                    'available_stock' => (int) ($variant->available_stock ?? 0),
                    'is_preorder' => (bool) $variant->is_preorder,
                    'preorder_expected_at' => $variant->preorder_expected_at
                        ? $variant->preorder_expected_at->toDateString()
                        : null,
                    'image_url' => $this->imageUrl($variant->image_path),
                ];
            })->values(),
        ];
    }

    private function cartData(array $data): array
    {
        return [
            'items' => $data['items']->map(function ($item) {
                return $this->cartItemData($item);
            })->values(),
            'items_count' => $data['totalItems'],
            'cart_count' => $data['cartCount'],
            'subtotal' => $data['subtotal'],
            'admin_fee' => $data['bank_charger'],
            'total' => $data['subtotal'] + $data['bank_charger'],
            'store_is_open' => (bool) $data['settings']->is_open,
        ];
    }

    private function paginationData($paginator, array $filters, bool $storeIsOpen): array
    {
        $paginator->appends($filters);

        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $storeIsOpen ? $paginator->total() : 0,
            'from' => $storeIsOpen ? $paginator->firstItem() : null,
            'to' => $storeIsOpen ? $paginator->lastItem() : null,
            'next_page_url' => $storeIsOpen ? $paginator->nextPageUrl() : null,
            'previous_page_url' => $storeIsOpen ? $paginator->previousPageUrl() : null,
        ];
    }

    private function cartItemData($item): array
    {
        $variant = $item->variant;

        return [
            'id' => $item->id,
            'product_id' => $variant->product->id,
            'product_name' => $variant->product->name,
            'variant_id' => $variant->id,
            'variant_name' => trim(($variant->color ? $variant->color . ' / ' : '') . $variant->name),
            'sku' => $variant->sku,
            'image_url' => $this->imageUrl($variant->image_path ?: $variant->product->image_path),
            'quantity' => (int) $item->quantity,
            'is_selected' => (bool) $item->is_selected,
            'is_available' => (bool) $item->is_available,
            'available_stock' => (int) $variant->available_stock,
            'is_preorder' => (bool) $variant->is_preorder,
            'preorder_expected_at' => $variant->preorder_expected_at
                ? $variant->preorder_expected_at->toDateString()
                : null,
            'unit_price' => (float) $variant->price,
            'line_total' => (float) $item->line_total,
        ];
    }

    private function imageUrl(?string $path): ?string
    {
        return $path ? asset('storage/' . ltrim($path, '/')) : null;
    }

    private function cartToken(Request $request): string
    {
        $data = Validator::make(
            ['cart_token' => $request->header('X-Cart-Token')],
            ['cart_token' => ['required', 'string', 'size:64', 'regex:/^[A-Za-z0-9]+$/']]
        )->validate();

        return $data['cart_token'];
    }

    private function optionalCartToken(Request $request): ?string
    {
        $token = $request->header('X-Cart-Token');
        if ($token === null) {
            return null;
        }

        return Validator::make(
            ['cart_token' => $token],
            ['cart_token' => ['required', 'string', 'size:64', 'regex:/^[A-Za-z0-9]+$/']]
        )->validate()['cart_token'];
    }
}
