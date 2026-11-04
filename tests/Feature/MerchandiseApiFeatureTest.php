<?php

namespace Tests\Feature;

use Tests\TestCase;

class MerchandiseApiFeatureTest extends TestCase
{
    public function test_mobile_cart_creation_returns_a_random_token(): void
    {
        $this->postJson('/api/v1/merchandise/carts')
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['cart_token']]);
    }

    public function test_cart_endpoints_require_a_valid_cart_token(): void
    {
        $this->getJson('/api/v1/merchandise/carts')
            ->assertStatus(422)
            ->assertJsonValidationErrors('cart_token');
    }

    public function test_mobile_api_documentation_is_publicly_available(): void
    {
        $this->get('/api/docs/merchandise')
            ->assertOk()
            ->assertSee('Merchandise Mobile API')
            ->assertSee('swagger-ui-bundle.js')
            ->assertSee('openapi.json')
            ->assertSee('does not expose order history by username');
    }

    public function test_category_endpoint_lists_public_categories(): void
    {
        $this->getJson('/api/v1/merchandise/categories')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['categories']]);
    }

    public function test_product_list_supports_pagination_and_category_filter(): void
    {
        $this->getJson('/api/v1/merchandise/products?page=1&category_id=1')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'products',
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                        'from',
                        'to',
                        'next_page_url',
                        'previous_page_url',
                    ],
                ],
            ]);
    }

    public function test_swagger_document_contains_category_and_product_filters(): void
    {
        $response = $this->getJson('/api/v1/merchandise/openapi.json');

        $response
            ->assertOk()
            ->assertJsonPath('openapi', '3.0.3')
            ->assertJsonStructure([
                'paths' => [
                    '/categories',
                    '/products',
                    '/products/{productId}',
                ],
            ]);

        $this->assertSame(
            '#/components/schemas/CartItemInput',
            $response->json('paths./carts/items.post.requestBody.content.application/json.schema.$ref')
        );
        $this->assertSame(
            '#/components/schemas/UpdateCartItemInput',
            $response->json('paths./carts/items/{itemId}.put.requestBody.content.application/json.schema.$ref')
        );
        $this->assertSame(
            '#/components/schemas/CreateOrderInput',
            $response->json('paths./orders.post.requestBody.content.application/json.schema.$ref')
        );
    }

    public function test_order_history_is_not_exposed_by_username_only(): void
    {
        $this->getJson('/api/v1/merchandise/orders/history?portal_username=someone')
            ->assertNotFound();
    }
}
