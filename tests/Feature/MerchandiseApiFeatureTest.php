<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public function test_cart_endpoint_returns_json_instead_of_redirecting_when_token_is_missing(): void
    {
        $this->get('/api/v1/merchandise/carts')
            ->assertStatus(422)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonValidationErrors('cart_token');
    }

    public function test_anonymous_cart_token_allows_cart_access_without_login(): void
    {
        $response = $this->postJson('/api/v1/merchandise/carts')
            ->assertCreated()
            ->assertJsonPath('success', true);

        $cartToken = $response->json('data.cart_token');

        $this->withHeader('X-Cart-Token', $cartToken)
            ->getJson('/api/v1/merchandise/carts')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_mobile_api_documentation_is_publicly_available(): void
    {
        $this->get('/api/docs/merchandise')
            ->assertOk()
            ->assertSee('Merchandise Mobile API')
            ->assertSee('swagger-ui-bundle.js')
            ->assertSee('openapi.json')
            ->assertSee('No account login is required')
            ->assertSee('not a login credential')
            ->assertSee('username alone is not verified identity')
            ->assertSee('does not include customer contact details or payment URLs');
    }

    public function test_category_endpoint_lists_public_categories(): void
    {
        $this->getJson('/api/v1/merchandise/categories')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['categories']]);
    }

    public function test_division_endpoint_lists_divisions(): void
    {
        $this->getJson('/api/v1/merchandise/divisions')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['divisions']]);
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
            ->assertJsonPath('components.schemas.Variant.properties.is_preorder.type', 'boolean')
            ->assertJsonPath('components.schemas.Variant.properties.preorder_expected_at.format', 'date')
            ->assertJsonStructure([
                'paths' => [
                    '/categories',
                    '/divisions',
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
            ['branch_id', 'parent_name', 'child_name', 'email', 'phone', 'portal_username', 'division'],
            $response->json('components.schemas.CreateOrderInput.required')
        );
        $this->assertSame(
            '#/components/schemas/Division',
            $response->json('components.schemas.CheckoutResponse.properties.data.properties.divisions.items.$ref')
        );
        $this->assertSame(
            '#/components/schemas/DivisionsResponse',
            $response->json('paths./divisions.get.responses.200.content.application/json.schema.$ref')
        );
        $this->assertSame(
            '#/components/schemas/CreateOrderInput',
            $response->json('paths./orders.post.requestBody.content.application/json.schema.$ref')
        );
    }

    public function test_order_creation_requires_child_name_and_free_text_division(): void
    {
        $cartToken = $this->postJson('/api/v1/merchandise/carts')->json('data.cart_token');

        $this->withHeader('X-Cart-Token', $cartToken)
            ->postJson('/api/v1/merchandise/orders', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['child_name', 'division']);
    }

    public function test_order_history_requires_a_portal_username(): void
    {
        $this->getJson('/api/v1/merchandise/orders')
            ->assertStatus(422)
            ->assertJsonValidationErrors('portal_username');
    }

    public function test_order_history_filters_by_exact_portal_username_and_returns_only_summaries(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('merchandise_orders', function ($table) {
            $table->id();
            $table->string('portal_username');
            $table->string('code');
            $table->timestamps();
            $table->string('order_type');
            $table->string('payment_status');
            $table->string('fulfillment_status');
            $table->date('preorder_expected_at')->nullable();
            $table->unsignedInteger('total_items');
            $table->decimal('total_amount', 12, 2);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('payment_url')->nullable();
        });

        DB::connection('sqlite')->table('merchandise_orders')->insert([
            [
                'portal_username' => 'P100',
                'code' => 'MATCH-001',
                'created_at' => '2026-11-20 10:00:00',
                'updated_at' => '2026-11-20 10:00:00',
                'order_type' => 'READY',
                'payment_status' => 'PAID',
                'fulfillment_status' => 'READY',
                'total_items' => 2,
                'total_amount' => 250000,
                'email' => 'private@example.com',
                'phone' => '08123456789',
                'payment_url' => 'https://payments.example/invoice',
            ],
            [
                'portal_username' => 'P100-OTHER',
                'code' => 'NO-MATCH',
                'created_at' => '2026-11-21 10:00:00',
                'updated_at' => '2026-11-21 10:00:00',
                'order_type' => 'READY',
                'payment_status' => 'PENDING',
                'fulfillment_status' => 'READY',
                'total_items' => 1,
                'total_amount' => 100000,
                'email' => null,
                'phone' => null,
                'payment_url' => null,
            ],
        ]);

        $response = $this->getJson('/api/v1/merchandise/orders?portal_username=P100');
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.orders')
            ->assertJsonPath('data.orders.0.order_reference', 'MATCH-001')
            ->assertJsonPath('data.orders.0.total_items', 2)
            ->assertJsonPath('data.pagination.total', 1);

        $orderSummary = $response->json('data.orders.0');
        $this->assertArrayNotHasKey('email', $orderSummary);
        $this->assertArrayNotHasKey('phone', $orderSummary);
        $this->assertArrayNotHasKey('payment_url', $orderSummary);
    }

    public function test_order_history_endpoint_is_documented(): void
    {
        $response = $this->getJson('/api/v1/merchandise/openapi.json');

        $response->assertOk()
            ->assertJsonPath('paths./orders.get.summary', 'List orders by Portal username')
            ->assertJsonPath('paths./orders.get.parameters.0.$ref', '#/components/parameters/PortalUsername')
            ->assertJsonPath(
                'paths./orders.get.responses.200.content.application/json.schema.$ref',
                '#/components/schemas/OrderHistoryResponse'
            );
    }

    public function test_merchandise_email_templates_are_restricted_to_admins(): void
    {
        $this->get('/merchandise/email-templates')
            ->assertRedirect(route('login'));

        $user = new User();
        $user->role = 'user';

        $this->actingAs($user)
            ->get('/merchandise/email-templates')
            ->assertForbidden();
    }
}
