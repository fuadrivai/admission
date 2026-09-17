<?php

namespace Tests\Feature;

use App\Models\EmailCampaign;
use App\Services\BlastMessageService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlastMessageTest extends TestCase
{

    public function test_it_renders_variables_safely_and_replaces_unknown_values_with_empty_strings(): void
    {
        $service = app(BlastMessageService::class);

        $content = '<p>Dear {{ nama }}, your bill is {{ tagihan }}. Unknown {{ unknown_variable }}.</p>';

        $rendered = $service->renderVariables($content, [
            'nama' => '<b>John Doe</b>',
            'tagihan' => 'Rp 4.000.000',
        ]);

        $this->assertSame(
            '<p>Dear &lt;b&gt;John Doe&lt;/b&gt;, your bill is Rp 4.000.000. Unknown .</p>',
            $rendered
        );
    }

    public function test_it_creates_one_recipient_record_per_valid_imported_row(): void
    {
        Storage::fake('local');

        $service = app(BlastMessageService::class);

        $campaign = $service->createCampaign([
            'name' => 'September Billing',
            'subject' => 'Billing for {{ nama }}',
            'message' => 'Hello {{ nama }}, your bill is {{ tagihan }}.',
            'recipients' => [
                [
                    'email' => 'parent1@example.com',
                    'name' => 'Parent 1',
                    'data' => [
                        'nama' => 'Student A',
                        'tagihan' => 'Rp 4.000.000',
                    ],
                ],
                [
                    'email' => 'parent1@example.com',
                    'name' => 'Duplicate Parent',
                    'data' => [
                        'nama' => 'Student A Duplicate',
                        'tagihan' => 'Rp 4.000.001',
                    ],
                ],
                [
                    'email' => 'invalid-email',
                    'name' => 'Bad Parent',
                    'data' => [
                        'nama' => 'Bad Student',
                    ],
                ],
                [
                    'email' => '',
                    'name' => 'Empty Parent',
                    'data' => [
                        'nama' => 'Empty Student',
                    ],
                ],
                [
                    'email' => 'parent2@example.com',
                    'name' => 'Parent 2',
                    'data' => [
                        'nama' => 'Student B',
                        'tagihan' => 'Rp 5.000.000',
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(EmailCampaign::class, $campaign);
        $this->assertSame('READY', $campaign->status);
        $this->assertSame(3, $campaign->total_recipient);
        $this->assertSame(3, $campaign->recipients()->count());
        $this->assertSame(2, $campaign->recipients()->where('email', 'parent1@example.com')->count());
        $this->assertDatabaseHas('email_campaign_recipients', [
            'campaign_id' => $campaign->id,
            'email' => 'parent1@example.com',
            'name' => 'Parent 1',
        ]);
        $this->assertDatabaseHas('email_campaign_recipients', [
            'campaign_id' => $campaign->id,
            'email' => 'parent2@example.com',
        ]);
        $this->assertDatabaseHas('email_campaign_recipients', [
            'campaign_id' => $campaign->id,
            'email' => 'parent1@example.com',
            'name' => 'Duplicate Parent',
        ]);
    }

    public function test_edit_campaign_appends_new_recipients_without_resetting_existing_records(): void
    {
        Storage::fake('local');

        $service = app(BlastMessageService::class);
        $campaign = $service->createCampaign([
            'name' => 'Open Day',
            'subject' => 'Invitation',
            'message' => 'Hello {{ student }}.',
            'recipients' => [[
                'email' => 'fuad@example.com',
                'name' => 'Fuad',
                'data' => ['student' => 'Ammar'],
            ]],
        ]);

        $existingRecipient = $campaign->recipients()->first();
        $existingRecipient->update(['status' => 'SENT', 'sent_at' => now()]);
        $campaign->update(['status' => 'COMPLETED', 'total_sent' => 1]);

        $updatedCampaign = $service->updateCampaign($campaign, [
            'name' => 'Open Day',
            'subject' => 'Invitation updated',
            'message' => 'Hello {{ student }}.',
            'recipients' => [
                [
                    'email' => 'fuad@example.com',
                    'name' => 'Fuad',
                    'data' => ['student' => 'Bilal'],
                ],
                [
                    'email' => 'ahmad@example.com',
                    'name' => 'Ahmad',
                    'data' => ['student' => 'Citra'],
                ],
            ],
        ]);

        $this->assertSame(3, $updatedCampaign->recipients()->count());
        $this->assertSame(3, $updatedCampaign->total_recipient);
        $this->assertSame('SENT', $existingRecipient->fresh()->status);
        $this->assertSame(1, $updatedCampaign->total_sent);
        $this->assertSame('PROCESSING', $updatedCampaign->fresh()->status);
        $this->assertSame(2, $updatedCampaign->recipients()->where('status', 'PENDING')->count());
    }
}
