<?php

namespace Tests\Feature;

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
}
