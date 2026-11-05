<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\MerchandiseApiController;
use App\Models\MerchandiseCartItem;
use App\Models\MerchandiseCategory;
use App\Models\MerchandiseOrder;
use App\Models\MerchandiseOrderItem;
use App\Models\MerchandiseProduct;
use App\Models\MerchandiseStockInItem;
use App\Models\MerchandiseVariant;
use App\Services\Implement\MerchandiseImplement;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class MerchandisePreorderFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Other tests cache guardable columns from the real DB schema.
        $cache = new \ReflectionProperty(\Illuminate\Database\Eloquent\Model::class, 'guardableColumns');
        $cache->setAccessible(true);
        $cache->setValue(null, []);

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);
        DB::purge('sqlite');

        $this->createPreorderTables();
    }

    public function test_preorder_and_ready_stock_cannot_be_placed_in_the_same_order(): void
    {
        $preorderItem = new MerchandiseCartItem();
        $preorderItem->setRelation('variant', new MerchandiseVariant(['is_preorder' => true]));
        $readyItem = new MerchandiseCartItem();
        $readyItem->setRelation('variant', new MerchandiseVariant(['is_preorder' => false]));

        $service = new MerchandiseImplement();
        $method = new ReflectionMethod(MerchandiseImplement::class, 'cartOrderType');
        $method->setAccessible(true);

        $this->assertSame('PREORDER', $method->invoke($service, new Collection([$preorderItem])));
        $this->expectException(ValidationException::class);
        $method->invoke($service, new Collection([$preorderItem, $readyItem]));
    }

    public function test_mobile_product_data_includes_preorder_eta(): void
    {
        $controller = (new ReflectionClass(MerchandiseApiController::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(MerchandiseApiController::class, 'productData');
        $method->setAccessible(true);

        $product = new MerchandiseProduct([
            'name' => 'School Jacket',
            'description' => 'Official merchandise',
        ]);
        $product->setRelation('category', new MerchandiseCategory(['name' => 'Uniform']));
        $product->setRelation('images', new Collection());

        $variant = new MerchandiseVariant([
            'name' => 'Medium',
            'color' => 'Navy',
            'price' => 250000,
            'available_stock' => 0,
            'is_preorder' => true,
            'preorder_expected_at' => '2026-12-01',
        ]);
        $product->setRelation('variants', new Collection([$variant]));

        $data = $method->invoke($controller, $product);

        $this->assertTrue($data['variants'][0]['is_preorder']);
        $this->assertSame('2026-12-01', $data['variants'][0]['preorder_expected_at']);
        $this->assertSame(0, $data['variants'][0]['available_stock']);
    }

    public function test_stock_in_allocates_to_paid_preorders_fifo_before_fulfillment(): void
    {
        $categoryId = DB::table('merchandise_categories')->insertGetId([
            'name' => 'Uniform',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('merchandise_products')->insertGetId([
            'category_id' => $categoryId,
            'name' => 'School Jacket',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $variantId = DB::table('merchandise_variants')->insertGetId([
            'product_id' => $productId,
            'name' => 'Medium',
            'color' => 'Navy',
            'price' => 250000,
            'is_active' => true,
            'is_preorder' => true,
            'preorder_expected_at' => '2026-12-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $paidOrder = $this->createOrder($variantId, 'PAID', 3);
        $unpaidOrder = $this->createOrder($variantId, 'PENDING', 1);
        $service = new MerchandiseImplement();

        $service->postStockIn([
            'stock_in_date' => '2026-11-05',
            'items' => [['variant_id' => $variantId, 'quantity' => 2, 'unit_cost' => 100000]],
        ], null);

        $this->assertSame('PARTIALLY_AVAILABLE', $paidOrder->fresh()->fulfillment_status);
        $this->assertSame('AWAITING_PAYMENT', $unpaidOrder->fresh()->fulfillment_status);
        $this->assertSame(2, (int) DB::table('merchandise_preorder_allocations')
            ->join('merchandise_order_items', 'merchandise_order_items.id', '=', 'merchandise_preorder_allocations.order_item_id')
            ->where('merchandise_order_items.order_id', $paidOrder->id)
            ->sum('merchandise_preorder_allocations.quantity'));

        $service->postStockIn([
            'stock_in_date' => '2026-11-06',
            'items' => [['variant_id' => $variantId, 'quantity' => 1, 'unit_cost' => 100000]],
        ], null);

        $this->assertSame('READY_FOR_FULFILLMENT', $paidOrder->fresh()->fulfillment_status);
        $this->assertSame('AWAITING_PAYMENT', $unpaidOrder->fresh()->fulfillment_status);
        $service->fulfillPreorder($paidOrder->id, null);

        $this->assertSame('FULFILLED', $paidOrder->fresh()->fulfillment_status);
        $this->assertNotNull($paidOrder->fresh()->fulfilled_at);
        $this->assertDatabaseHas('merchandise_stock_outs', ['order_id' => $paidOrder->id]);
        $this->assertDatabaseHas('merchandise_stock_out_items', [
            'variant_id' => $variantId,
            'quantity' => 3,
        ]);
        $this->assertDatabaseMissing('merchandise_stock_outs', ['order_id' => $unpaidOrder->id]);
    }

    private function createOrder(int $variantId, string $paymentStatus, int $quantity): MerchandiseOrder
    {
        $order = MerchandiseOrder::unguarded(fn () => MerchandiseOrder::create([
            'code' => 'PRE-' . uniqid(),
            'external_id' => 'PRE-' . uniqid(),
            'branch_id' => 1,
            'branch_name' => 'Bintaro',
            'parent_name' => 'Parent',
            'email' => 'parent@example.com',
            'phone' => '08123456789',
            'portal_username' => 'parent',
            'total_items' => $quantity,
            'subtotal' => $quantity * 250000,
            'bank_charger' => 0,
            'discount_amount' => 0,
            'total_amount' => $quantity * 250000,
            'payment_status' => $paymentStatus,
            'order_type' => 'PREORDER',
            'fulfillment_status' => $paymentStatus === 'PAID' ? 'WAITING_FOR_STOCK' : 'AWAITING_PAYMENT',
            'preorder_expected_at' => '2026-12-01',
        ]));
        MerchandiseOrderItem::unguarded(fn () => $order->items()->create([
            'variant_id' => $variantId,
            'product_name' => 'School Jacket',
            'variant_name' => 'Navy / Medium',
            'quantity' => $quantity,
            'unit_price' => 250000,
            'subtotal' => $quantity * 250000,
            'is_preorder' => true,
            'preorder_expected_at' => '2026-12-01',
        ]));

        return $order;
    }

    private function createPreorderTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('merchandise_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active');
            $table->timestamps();
        });
        Schema::create('merchandise_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->boolean('is_active');
            $table->timestamps();
        });
        Schema::create('merchandise_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->string('color');
            $table->decimal('price', 12, 2);
            $table->boolean('is_active');
            $table->boolean('is_preorder')->default(false);
            $table->date('preorder_expected_at')->nullable();
            $table->timestamps();
        });
        Schema::create('merchandise_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('external_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('branch_name');
            $table->string('parent_name');
            $table->string('email');
            $table->string('phone');
            $table->string('portal_username');
            $table->unsignedInteger('total_items');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('bank_charger', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_status');
            $table->string('order_type');
            $table->string('fulfillment_status');
            $table->date('preorder_expected_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('fulfilled_at')->nullable();
            $table->unsignedBigInteger('fulfilled_by')->nullable();
            $table->timestamps();
        });
        Schema::create('merchandise_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('variant_id');
            $table->string('product_name');
            $table->string('variant_name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->boolean('is_preorder');
            $table->date('preorder_expected_at')->nullable();
            $table->timestamps();
        });
        Schema::create('merchandise_stock_ins', function (Blueprint $table) {
            $table->id();
            $table->date('stock_in_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('merchandise_stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_in_id');
            $table->unsignedBigInteger('variant_id');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();
        });
        Schema::create('merchandise_preorder_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('stock_in_item_id');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('allocated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('merchandise_stock_outs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->dateTime('stock_out_at');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('merchandise_stock_out_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_out_id');
            $table->unsignedBigInteger('variant_id');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();
        });
    }
}
