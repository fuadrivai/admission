<?php

namespace Tests\Feature;

use Tests\TestCase;

class EventFormFeatureTest extends TestCase
{
    public function test_public_event_registration_route_is_available_for_published_event(): void
    {
        $slug = 'open-house-' . now()->timestamp;

        \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => $slug,
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $response = $this->get('/events/' . $slug);

        $response->assertOk();
    }

    public function test_email_field_can_store_primary_email_flag(): void
    {
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => 'open-house-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $response = $this->post(route('event.forms.store', $event), [
            'field_key' => 'email_address',
            'label' => 'Email Address',
            'type' => 'email',
            'is_required' => '1',
            'is_primary_email' => '1',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('event.forms.index', $event));
        $this->assertDatabaseHas('event_fields', [
            'event_id' => $event->id,
            'field_key' => 'email_address',
            'type' => 'email',
            'is_primary_email' => true,
        ]);
    }

    public function test_event_registration_store_creates_event_field_answers(): void
    {
        $slug = 'open-house-' . uniqid();
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => $slug,
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $nameField = $event->fields()->create([
            'field_key' => 'full_name',
            'label' => 'Full Name',
            'type' => 'text',
            'is_required' => true,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $interestField = $event->fields()->create([
            'field_key' => 'interests',
            'label' => 'Interests',
            'type' => 'checkbox',
            'is_required' => false,
            'options_json' => ['Sports', 'Music'],
            'order_index' => 2,
            'is_active' => true,
        ]);

        $response = $this->post('/events/' . $slug . '/register', [
            'full_name' => 'Jane Doe',
            'interests' => ['Sports', 'Music'],
        ]);

        $response->assertRedirect();

        // Verify registration was created
        $registration = $event->registrations()->first();
        $this->assertNotNull($registration);
        $this->assertEquals('SUBMITTED', $registration->status);
        $this->assertNotNull($registration->code);
        $this->assertTrue(str_starts_with($registration->code, 'REG-'));

        // Verify field answers are linked to the registration
        $this->assertDatabaseHas('event_field_answers', [
            'event_registration_id' => $registration->id,
            'event_field_id' => $nameField->id,
            'value' => 'Jane Doe',
        ]);
        $this->assertDatabaseHas('event_field_answers', [
            'event_registration_id' => $registration->id,
            'event_field_id' => $interestField->id,
            'value' => json_encode(['Sports', 'Music']),
        ]);
    }
}
