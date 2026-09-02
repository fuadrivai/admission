<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

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

    public function test_attachment_field_can_be_created(): void
    {
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => 'open-house-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $response = $this->post(route('event.forms.store', $event), [
            'field_key' => 'attachment_file',
            'label' => 'Attachment',
            'type' => 'attachment',
            'is_required' => '1',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('event.forms.index', $event));
        $this->assertDatabaseHas('event_fields', [
            'event_id' => $event->id,
            'field_key' => 'attachment_file',
            'type' => 'attachment',
            'is_required' => true,
        ]);
    }

    public function test_event_registration_store_saves_attachment_file_to_event_disk(): void
    {
        Storage::fake('event');

        $slug = 'open-house-' . uniqid();
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => $slug,
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $event->fields()->create([
            'field_key' => 'attachment_file',
            'label' => 'Attachment',
            'type' => 'attachment',
            'is_required' => true,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 200, 'application/pdf');

        $response = $this->post('/events/' . $slug . '/register', [
            'attachment_file' => $file,
        ]);

        $response->assertRedirect();

        $registration = $event->registrations()->first();
        $this->assertNotNull($registration);
        $this->assertNotNull($registration->code);

        $answer = $registration->fieldAnswers()->where('event_field_id', $event->fields()->first()->id)->first();
        $this->assertNotNull($answer);
        $this->assertNotEmpty($answer->value);
        $this->assertTrue(Storage::disk('event')->exists($answer->value));
    }

    public function test_creating_field_appends_to_end_of_order(): void
    {
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => 'open-house-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $event->fields()->create([
            'field_key' => 'first_question',
            'label' => 'First Question',
            'type' => 'text',
            'is_required' => false,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $response = $this->post(route('event.forms.store', $event), [
            'field_key' => 'second_question',
            'label' => 'Second Question',
            'type' => 'text',
            'is_required' => '0',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('event.forms.index', $event));
        $this->assertSame(2, $event->fresh()->forms()->where('field_key', 'second_question')->value('order_index'));
    }

    public function test_editing_field_keeps_existing_order_index(): void
    {
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => 'open-house-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $firstField = $event->fields()->create([
            'field_key' => 'first_question',
            'label' => 'First Question',
            'type' => 'text',
            'is_required' => false,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $event->fields()->create([
            'field_key' => 'second_question',
            'label' => 'Second Question',
            'type' => 'text',
            'is_required' => false,
            'order_index' => 2,
            'is_active' => true,
        ]);

        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $response = $this->put(route('event.forms.update', [$event, $firstField]), [
            'field_key' => 'first_question',
            'label' => 'Updated First Question',
            'type' => 'text',
            'is_required' => '0',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('event.forms.index', $event));
        $this->assertSame(1, $firstField->fresh()->order_index);
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
        $this->assertNotEmpty($registration->code);

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

    public function test_event_registration_export_uses_full_attachment_url(): void
    {
        Storage::fake('event');

        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => 'open-house-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $attachmentField = $event->fields()->create([
            'field_key' => 'attachment_file',
            'label' => 'Attachment',
            'type' => 'attachment',
            'is_required' => true,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $registrationCode = 'ECODE-TEST-' . uniqid();
        $registration = $event->registrations()->create([
            'code' => $registrationCode,
            'status' => 'SUBMITTED',
            'amount' => 0,
            'registered_at' => now(),
        ]);

        $storedValue = $registrationCode . '/attachment_file_123.pdf';
        $file = UploadedFile::fake()->create('attachment_file_123.pdf', 200, 'application/pdf');
        Storage::disk('event')->put($storedValue, $file->getContent());

        $registration->fieldAnswers()->create([
            'event_field_id' => $attachmentField->id,
            'value' => $storedValue,
        ]);

        $export = new \App\Exports\EventRegistrationExport($event);
        $row = $export->map($registration);

        $this->assertStringContainsString('http', (string) $row[2]);
        $this->assertStringContainsString('attachment_file_123.pdf', (string) $row[2]);
    }
}
