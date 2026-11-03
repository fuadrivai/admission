<?php

namespace Tests\Feature;

use App\Services\MerchandiseService;
use Illuminate\Support\Str;
use Tests\TestCase;

class XenditCallbackFeatureTest extends TestCase
{
    public function test_callback_rejects_payload_without_external_id_or_status(): void
    {
        $this->postJson('/api/xendit/callback', [])
            ->assertStatus(400)
            ->assertJson(['message' => 'Invalid payload']);
    }

    public function test_callback_logs_unknown_transaction_payload_without_type_error(): void
    {
        $this->postJson('/api/xendit/callback', [
            'external_id' => 'UNKNOWN-123',
            'status' => 'PAID',
        ])
            ->assertStatus(404)
            ->assertJson(['message' => 'Unknown transaction']);
    }

    public function test_merchandise_callback_uses_the_standard_callback_flow_without_a_token(): void
    {
        $merchandiseService = \Mockery::mock(MerchandiseService::class);
        $externalId = 'MERCH-' . strtoupper(Str::random(16));
        $callbackData = [
            'external_id' => $externalId,
            'status' => 'PAID',
        ];

        $merchandiseService->shouldReceive('processOrderPaymentCallback')
            ->once()
            ->with($externalId, $callbackData)
            ->andReturn(true);
        $this->app->instance(MerchandiseService::class, $merchandiseService);

        $this->postJson('/api/xendit/callback', $callbackData)
            ->assertOk()
            ->assertJson(['message' => 'Callback processed']);
    }
}
