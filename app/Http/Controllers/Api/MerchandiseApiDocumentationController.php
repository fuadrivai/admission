<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class MerchandiseApiDocumentationController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'MHIS Merchandise Mobile API',
                'version' => '1.0.0',
                'description' => 'All endpoints are available without an account login. Cart operations use an anonymously generated X-Cart-Token to identify the cart; it is not a user login credential.',
            ],
            'servers' => [
                ['url' => url('/api/v1/merchandise'), 'description' => 'Current application'],
            ],
            'tags' => [
                ['name' => 'Store', 'description' => 'Public catalog, category, and product browsing'],
                ['name' => 'Cart', 'description' => 'Anonymous mobile cart managed with a cart token'],
                ['name' => 'Checkout', 'description' => 'Promotion preview and payment order creation'],
            ],
            'paths' => [
                '/store' => [
                    'get' => [
                        'tags' => ['Store'],
                        'summary' => 'Get store overview',
                        'description' => 'Returns store status, visible categories, paginated products, and optional cart count.',
                        'parameters' => [
                            ['$ref' => '#/components/parameters/Search'],
                            ['$ref' => '#/components/parameters/CategoryId'],
                            ['$ref' => '#/components/parameters/Page'],
                            ['$ref' => '#/components/parameters/OptionalCartToken'],
                        ],
                        'responses' => [
                            '200' => $this->response('Store overview', 'StoreOverviewResponse'),
                            '422' => $this->response('Invalid query parameters', 'ValidationError'),
                        ],
                    ],
                ],
                '/categories' => [
                    'get' => [
                        'tags' => ['Store'],
                        'summary' => 'List public categories',
                        'description' => 'Returns active, store-visible categories that have at least one currently available product.',
                        'responses' => [
                            '200' => $this->response('Category list', 'CategoriesResponse'),
                        ],
                    ],
                ],
                '/products' => [
                    'get' => [
                        'tags' => ['Store'],
                        'summary' => 'List products',
                        'description' => 'Returns available public products. Results contain at most 24 products per page.',
                        'parameters' => [
                            ['$ref' => '#/components/parameters/Search'],
                            ['$ref' => '#/components/parameters/CategoryId'],
                            ['$ref' => '#/components/parameters/Page'],
                            ['$ref' => '#/components/parameters/OptionalCartToken'],
                        ],
                        'responses' => [
                            '200' => $this->response('Paginated product list', 'ProductsResponse'),
                            '422' => $this->response('Invalid query parameters', 'ValidationError'),
                        ],
                    ],
                ],
                '/products/{productId}' => [
                    'get' => [
                        'tags' => ['Store'],
                        'summary' => 'Get product details',
                        'parameters' => [
                            [
                                'name' => 'productId',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer', 'minimum' => 1],
                            ],
                        ],
                        'responses' => [
                            '200' => $this->response('Product details', 'ProductResponse'),
                            '404' => $this->response('Product not found or unavailable', 'Error'),
                        ],
                    ],
                ],
                '/carts' => [
                    'post' => [
                        'tags' => ['Cart'],
                        'summary' => 'Create a cart',
                        'description' => 'Creates an anonymous cart token. Keep the token private; possession grants access to the cart.',
                        'responses' => [
                            '201' => $this->response('Cart created', 'CartTokenResponse'),
                        ],
                    ],
                    'get' => [
                        'tags' => ['Cart'],
                        'summary' => 'Get cart',
                        'security' => [['CartToken' => []]],
                        'responses' => [
                            '200' => $this->response('Cart details', 'CartResponse'),
                            '422' => $this->response('Missing or invalid cart token', 'ValidationError'),
                        ],
                    ],
                ],
                '/carts/items' => [
                    'post' => [
                        'tags' => ['Cart'],
                        'summary' => 'Add item to cart',
                        'security' => [['CartToken' => []]],
                        'requestBody' => $this->requestBody('CartItemInput'),
                        'responses' => [
                            '201' => $this->response('Updated cart', 'CartResponse'),
                            '422' => $this->response('Invalid item or unavailable stock', 'ValidationError'),
                        ],
                    ],
                ],
                '/carts/items/{itemId}' => [
                    'put' => [
                        'tags' => ['Cart'],
                        'summary' => 'Update cart item',
                        'security' => [['CartToken' => []]],
                        'parameters' => [$this->pathId('itemId')],
                        'requestBody' => $this->requestBody('UpdateCartItemInput'),
                        'responses' => [
                            '200' => $this->response('Updated cart', 'CartResponse'),
                            '404' => $this->response('Cart item not found', 'Error'),
                            '422' => $this->response('Invalid quantity or unavailable stock', 'ValidationError'),
                        ],
                    ],
                    'delete' => [
                        'tags' => ['Cart'],
                        'summary' => 'Remove cart item',
                        'security' => [['CartToken' => []]],
                        'parameters' => [$this->pathId('itemId')],
                        'responses' => [
                            '200' => $this->response('Updated cart', 'CartResponse'),
                            '404' => $this->response('Cart item not found', 'Error'),
                            '422' => $this->response('Missing or invalid cart token', 'ValidationError'),
                        ],
                    ],
                ],
                '/carts/checkout' => [
                    'get' => [
                        'tags' => ['Checkout'],
                        'summary' => 'Preview checkout',
                        'description' => 'Calculates current totals, promotions, and payment branches. For a pre-order cart, returns order_type=PREORDER and preorder_expected_at; pre-orders cannot be mixed with ready-stock items and promotions do not apply.',
                        'security' => [['CartToken' => []]],
                        'parameters' => [
                            [
                                'name' => 'promotion_code',
                                'in' => 'query',
                                'schema' => ['type' => 'string', 'maxLength' => 50],
                            ],
                            [
                                'name' => 'promotion_id',
                                'in' => 'query',
                                'schema' => ['type' => 'integer', 'minimum' => 1],
                            ],
                            [
                                'name' => 'gift_variant_id',
                                'in' => 'query',
                                'schema' => ['type' => 'integer', 'minimum' => 1],
                            ],
                        ],
                        'responses' => [
                            '200' => $this->response('Checkout preview', 'CheckoutResponse'),
                            '422' => $this->response('Cart or promotion validation failed', 'ValidationError'),
                        ],
                    ],
                ],
                '/orders' => [
                    'post' => [
                        'tags' => ['Checkout'],
                        'summary' => 'Create order and payment invoice',
                        'description' => 'Creates an order using server-calculated prices and stock, then creates a Xendit invoice. Pre-orders require full payment, cannot be mixed with ready-stock items, and are allocated from later Stock In records. Open payment_url to complete payment.',
                        'security' => [['CartToken' => []]],
                        'requestBody' => $this->requestBody('CreateOrderInput'),
                        'responses' => [
                            '201' => $this->response('Order and invoice created', 'OrderResponse'),
                            '422' => $this->response('Invalid checkout data or invoice creation failed', 'ValidationError'),
                        ],
                    ],
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'CartToken' => [
                        'type' => 'apiKey',
                        'in' => 'header',
                        'name' => 'X-Cart-Token',
                        'description' => 'Token returned by POST /carts.',
                    ],
                ],
                'parameters' => [
                    'Search' => [
                        'name' => 'search',
                        'in' => 'query',
                        'description' => 'Search product name, description, variant, color, or SKU.',
                        'schema' => ['type' => 'string', 'maxLength' => 100],
                    ],
                    'CategoryId' => [
                        'name' => 'category_id',
                        'in' => 'query',
                        'description' => 'Only return products in this category.',
                        'schema' => ['type' => 'integer', 'minimum' => 1],
                    ],
                    'Page' => [
                        'name' => 'page',
                        'in' => 'query',
                        'description' => 'One-based page number. Page size is 24.',
                        'schema' => ['type' => 'integer', 'minimum' => 1, 'default' => 1],
                    ],
                    'OptionalCartToken' => [
                        'name' => 'X-Cart-Token',
                        'in' => 'header',
                        'required' => false,
                        'description' => 'Optional. Include to receive the current cart item count.',
                        'schema' => ['type' => 'string', 'minLength' => 64, 'maxLength' => 64],
                    ],
                ],
                'schemas' => [
                    'Success' => [
                        'type' => 'object',
                        'required' => ['success', 'data'],
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => true],
                            'data' => ['type' => 'object'],
                        ],
                    ],
                    'Error' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string'],
                        ],
                    ],
                    'ValidationError' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string'],
                            'errors' => [
                                'type' => 'object',
                                'additionalProperties' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                    'Category' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string', 'nullable' => true],
                        ],
                    ],
                    'Variant' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'color' => ['type' => 'string'],
                            'sku' => ['type' => 'string', 'nullable' => true],
                            'price' => ['type' => 'number', 'format' => 'float'],
                            'available_stock' => ['type' => 'integer'],
                            'is_preorder' => [
                                'type' => 'boolean',
                                'description' => 'When true, the item is a pre-order. available_stock is the remaining pre-order quota (stock-in quantity minus active pre-orders).',
                            ],
                            'preorder_expected_at' => [
                                'type' => 'string',
                                'format' => 'date',
                                'nullable' => true,
                                'description' => 'Estimated date this pre-order item will be available.',
                            ],
                            'image_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                        ],
                    ],
                    'Product' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string', 'nullable' => true],
                            'image_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                            'category' => [
                                'allOf' => [['$ref' => '#/components/schemas/Category']],
                                'nullable' => true,
                            ],
                            'images' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'uri']],
                            'variants' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Variant']],
                        ],
                    ],
                    'CartItem' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'product_id' => ['type' => 'integer'],
                            'product_name' => ['type' => 'string'],
                            'variant_id' => ['type' => 'integer'],
                            'variant_name' => ['type' => 'string'],
                            'sku' => ['type' => 'string', 'nullable' => true],
                            'image_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                            'quantity' => ['type' => 'integer'],
                            'is_selected' => ['type' => 'boolean'],
                            'is_available' => ['type' => 'boolean'],
                            'available_stock' => ['type' => 'integer'],
                            'is_preorder' => ['type' => 'boolean'],
                            'preorder_expected_at' => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                            'unit_price' => ['type' => 'number'],
                            'line_total' => ['type' => 'number'],
                        ],
                    ],
                    'Pagination' => [
                        'type' => 'object',
                        'properties' => [
                            'current_page' => ['type' => 'integer'],
                            'last_page' => ['type' => 'integer'],
                            'per_page' => ['type' => 'integer', 'example' => 24],
                            'total' => ['type' => 'integer'],
                            'from' => ['type' => 'integer', 'nullable' => true],
                            'to' => ['type' => 'integer', 'nullable' => true],
                            'next_page_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                            'previous_page_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                        ],
                    ],
                    'CartItemInput' => [
                        'type' => 'object',
                        'required' => ['variant_id', 'quantity'],
                        'properties' => [
                            'variant_id' => ['type' => 'integer', 'example' => 45],
                            'quantity' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 999],
                        ],
                    ],
                    'UpdateCartItemInput' => [
                        'type' => 'object',
                        'required' => ['quantity'],
                        'properties' => [
                            'quantity' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 999],
                            'is_selected' => ['type' => 'boolean'],
                        ],
                    ],
                    'CreateOrderInput' => [
                        'type' => 'object',
                        'required' => ['branch_id', 'parent_name', 'email', 'phone', 'portal_username'],
                        'properties' => [
                            'branch_id' => ['type' => 'integer'],
                            'parent_name' => ['type' => 'string', 'maxLength' => 150],
                            'email' => ['type' => 'string', 'format' => 'email'],
                            'phone' => ['type' => 'string', 'maxLength' => 40],
                            'portal_username' => ['type' => 'string', 'maxLength' => 100],
                            'promotion_code' => ['type' => 'string', 'maxLength' => 50],
                            'promotion_id' => ['type' => 'integer'],
                            'promotion_gift_variant_id' => ['type' => 'integer'],
                        ],
                    ],
                    'StoreOverviewResponse' => $this->successSchema([
                        'store' => ['type' => 'object', 'properties' => [
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string', 'nullable' => true],
                            'is_open' => ['type' => 'boolean'],
                            'closed_message' => ['type' => 'string', 'nullable' => true],
                        ]],
                        'categories' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Category']],
                        'products' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Product']],
                        'cart_count' => ['type' => 'integer'],
                        'pagination' => ['$ref' => '#/components/schemas/Pagination'],
                    ]),
                    'CategoriesResponse' => $this->successSchema([
                        'categories' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Category']],
                    ]),
                    'ProductsResponse' => $this->successSchema([
                        'products' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Product']],
                        'cart_count' => ['type' => 'integer'],
                        'pagination' => ['$ref' => '#/components/schemas/Pagination'],
                    ]),
                    'ProductResponse' => $this->successSchema([
                        'store' => ['type' => 'object'],
                        'product' => ['$ref' => '#/components/schemas/Product'],
                    ]),
                    'CartTokenResponse' => $this->successSchema([
                        'cart_token' => ['type' => 'string', 'minLength' => 64, 'maxLength' => 64],
                    ]),
                    'CartResponse' => $this->successSchema([
                        'items' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/CartItem']],
                        'items_count' => ['type' => 'integer'],
                        'cart_count' => ['type' => 'integer'],
                        'subtotal' => ['type' => 'number'],
                        'admin_fee' => ['type' => 'number'],
                        'total' => ['type' => 'number'],
                        'store_is_open' => ['type' => 'boolean'],
                    ]),
                    'CheckoutResponse' => $this->successSchema([
                        'items' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/CartItem']],
                        'items_count' => ['type' => 'integer'],
                        'order_type' => ['type' => 'string', 'enum' => ['READY', 'PREORDER']],
                        'preorder_expected_at' => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                        'subtotal' => ['type' => 'number'],
                        'admin_fee' => ['type' => 'number'],
                        'total' => ['type' => 'number'],
                        'promotions' => ['type' => 'array', 'items' => ['type' => 'object']],
                        'selected_promotion' => ['type' => 'object', 'nullable' => true],
                        'branches' => ['type' => 'array', 'items' => ['type' => 'object']],
                    ]),
                    'OrderResponse' => $this->successSchema([
                        'order_reference' => ['type' => 'string'],
                        'payment_status' => ['type' => 'string', 'example' => 'PENDING'],
                        'order_type' => ['type' => 'string', 'enum' => ['READY', 'PREORDER']],
                        'fulfillment_status' => ['type' => 'string'],
                        'preorder_expected_at' => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                        'amount' => ['type' => 'number'],
                        'expires_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                        'payment_url' => ['type' => 'string', 'format' => 'uri'],
                    ]),
                ],
            ],
        ]);
    }

    private function response(string $description, string $schema): array
    {
        return [
            'description' => $description,
            'content' => [
                'application/json' => [
                    'schema' => ['$ref' => '#/components/schemas/' . $schema],
                ],
            ],
        ];
    }

    private function requestBody(string $schema): array
    {
        return [
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => ['$ref' => '#/components/schemas/' . $schema],
                ],
            ],
        ];
    }

    private function pathId(string $name): array
    {
        return [
            'name' => $name,
            'in' => 'path',
            'required' => true,
            'schema' => ['type' => 'integer', 'minimum' => 1],
        ];
    }

    private function successSchema(array $properties): array
    {
        return [
            'type' => 'object',
            'required' => ['success', 'data'],
            'properties' => [
                'success' => ['type' => 'boolean', 'example' => true],
                'data' => [
                    'type' => 'object',
                    'properties' => $properties,
                ],
            ],
        ];
    }
}
