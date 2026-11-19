<?php

namespace Tests\Feature;

use App\Google\EventRegistrationGoogleSheetService;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventFormFeatureTest extends TestCase
{
    public function test_registration_page_can_sync_to_google_sheets(): void
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

        $sheetUrl = 'https://docs.google.com/spreadsheets/d/event-sheet-id/edit';
        $googleSheet = \Mockery::mock(EventRegistrationGoogleSheetService::class);
        $googleSheet->shouldReceive('syncEvent')->once()->with(\Mockery::on(function ($syncedEvent) use ($event) {
            return $syncedEvent->is($event);
        }))->andReturn($sheetUrl);
        $this->app->instance(EventRegistrationGoogleSheetService::class, $googleSheet);

        $this->post(route('event.registrations.google-sheet', $event))
            ->assertRedirect(route('event.registrations.index', $event))
            ->assertSessionHas('success', 'Registrations synced to Google Sheets.')
            ->assertSessionHas('google_sheet_url', $sheetUrl);
    }

    public function test_event_google_sheet_url_can_be_created_updated_and_displayed(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $eventUrl = 'https://docs.google.com/spreadsheets/d/event-sheet-id/edit';
        $eventData = [
            'title' => 'Open House 2026',
            'slug' => 'open-house-' . uniqid(),
            'status' => 'DRAFT',
            'availability_type' => 'ALWAYS',
            'google_sheet_url' => $eventUrl,
        ];

        $this->post(route('event.store'), $eventData)
            ->assertRedirect(route('event.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Event created successfully.');

        $event = \App\Models\Event::where('slug', $eventData['slug'])->firstOrFail();
        $this->assertSame($eventUrl, $event->google_sheet_url);

        $updatedUrl = 'https://docs.google.com/spreadsheets/d/updated-sheet-id/edit';
        $this->put(route('event.update', $event), array_merge($eventData, [
            'slug' => $event->slug,
            'google_sheet_url' => $updatedUrl,
        ]))->assertRedirect(route('event.edit', $event));

        $this->assertSame($updatedUrl, $event->fresh()->google_sheet_url);
        $this->get(route('event.edit', $event))
            ->assertOk()
            ->assertSee($updatedUrl)
            ->assertSee('const reloadAfterSave = true;', false)
            ->assertSee('window.location.reload();', false);
    }

    public function test_event_google_sheet_url_must_be_a_valid_url(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $this->from(route('event.create'))
            ->post(route('event.store'), [
                'title' => 'Open House 2026',
                'slug' => 'open-house-' . uniqid(),
                'status' => 'DRAFT',
                'availability_type' => 'ALWAYS',
                'google_sheet_url' => 'not-a-url',
            ])
            ->assertRedirect(route('event.create'))
            ->assertSessionHasErrors('google_sheet_url');
    }

    public function test_event_index_defaults_to_all_statuses_and_keeps_explicit_filters(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        foreach ([
            ['title' => 'Draft event', 'status' => 'DRAFT'],
            ['title' => 'Published event', 'status' => 'PUBLISHED'],
            ['title' => 'Closed event', 'status' => 'CLOSED'],
        ] as $eventData) {
            \App\Models\Event::create(array_merge($eventData, [
                'slug' => Str::slug($eventData['title']) . '-' . uniqid(),
            ]));
        }

        $this->get(route('event.index'))
            ->assertOk()
            ->assertSee('value="all" selected', false)
            ->assertSee('Draft event')
            ->assertSee('Published event')
            ->assertSee('Closed event');

        $this->get(route('event.index', ['status' => 'PUBLISHED']))
            ->assertOk()
            ->assertSee('Published event')
            ->assertDontSee('Draft event')
            ->assertDontSee('Closed event');
    }

    public function test_event_copy_includes_form_email_and_pricing_but_not_registrations(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $sourceSlug = 'open-house-2026-' . uniqid();
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => $sourceSlug,
            'status' => 'PUBLISHED',
            'availability_type' => 'ALWAYS',
            'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/source/edit',
        ]);

        $this->get(route('event.index'))->assertDontSee('Make Copy');
        $this->get(route('event.edit', $event))
            ->assertOk()
            ->assertSee('Make Copy');

        $parentField = $event->fields()->create([
            'field_key' => 'location',
            'label' => 'Location',
            'type' => 'select',
            'options_json' => ['North', 'South'],
            'order_index' => 1,
            'is_active' => true,
        ]);

        $dependentField = $event->fields()->create([
            'field_key' => 'room',
            'label' => 'Room',
            'type' => 'select',
            'options_json' => ['North' => ['Room A'], 'South' => ['Room B']],
            'depends_on_field_id' => $parentField->id,
            'order_index' => 2,
            'is_active' => true,
        ]);

        $event->emailTemplates()->create([
            'key' => 'registration_confirmation',
            'subject' => 'Registration confirmed',
            'body' => 'Hello {{name}}',
        ]);

        $event->priceOptions()->create([
            'code' => 'regular',
            'name' => 'Regular',
            'amount' => 100000,
            'currency' => 'IDR',
            'is_active' => true,
            'order_index' => 1,
            'quota' => 20,
            'sold_count' => 4,
        ]);

        $event->registrations()->create([
            'code' => 'REG-COPY-' . uniqid(),
            'status' => 'SUBMITTED',
            'amount' => 100000,
            'registered_at' => now(),
        ]);

        $response = $this->post(route('event.copy', $event));

        $copy = \App\Models\Event::where('slug', $sourceSlug . '-copy')->firstOrFail();
        $response->assertRedirect(route('event.edit', $copy))
            ->assertSessionHas('success');
        $this->assertSame('Open House 2026 (Copy)', $copy->title);
        $this->assertSame('DRAFT', $copy->status);
        $this->assertNull($copy->google_sheet_url);
        $this->assertSame(2, $copy->fields()->count());
        $this->assertSame(1, $copy->emailTemplates()->count());
        $this->assertSame(1, $copy->priceOptions()->count());
        $this->assertSame(0, $copy->registrations()->count());

        $copiedParent = $copy->fields()->where('field_key', 'location')->firstOrFail();
        $copiedDependent = $copy->fields()->where('field_key', 'room')->firstOrFail();
        $this->assertNotSame($parentField->id, $copiedParent->id);
        $this->assertSame($copiedParent->id, $copiedDependent->depends_on_field_id);
        $this->assertSame(['North' => ['Room A'], 'South' => ['Room B']], $copiedDependent->options_json);
        $this->assertSame('Hello {{name}}', $copy->emailTemplates()->first()->body);
        $this->assertSame(0, $copy->priceOptions()->first()->sold_count);
    }

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

    public function test_checkbox_selection_mode_can_be_saved_and_updated(): void
    {
        $event = \App\Models\Event::create([
            'title' => 'Checkbox Mode Event',
            'slug' => 'checkbox-mode-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $this->post(route('event.forms.store', $event), [
            'field_key' => 'declaration',
            'label' => 'Declaration',
            'type' => 'checkbox',
            'checkbox_mode' => 'all',
            'options_json' => ['Statement 1', 'Statement 2'],
        ])->assertRedirect(route('event.forms.index', $event));

        $field = $event->fields()->where('field_key', 'declaration')->firstOrFail();
        $this->assertSame('all', $field->checkbox_mode);

        $this->get(route('event.forms.edit', [$event, $field]))
            ->assertOk()
            ->assertSee('value="all" selected', false)
            ->assertSee('Checkbox Selection Rule');

        $this->put(route('event.forms.update', [$event, $field]), [
            'field_key' => 'declaration',
            'label' => 'Declaration',
            'type' => 'checkbox',
            'checkbox_mode' => 'single',
            'options_json' => ['Statement 1', 'Statement 2'],
        ])->assertRedirect(route('event.forms.index', $event));

        $this->assertSame('single', $field->fresh()->checkbox_mode);
    }

    public function test_checkbox_all_mode_requires_every_option(): void
    {
        $slug = 'checkbox-all-' . uniqid();
        $event = \App\Models\Event::create([
            'title' => 'Checkbox All Event',
            'slug' => $slug,
            'status' => 'PUBLISHED',
            'intro_html' => '',
        ]);
        $field = $event->fields()->create([
            'field_key' => 'declaration',
            'label' => 'Declaration',
            'type' => 'checkbox',
            'checkbox_mode' => 'all',
            'is_required' => true,
            'options_json' => ['Statement 1', 'Statement 2'],
            'is_active' => true,
        ]);

        $this->from('/events/' . $slug)
            ->post('/events/' . $slug . '/register', [
                'declaration' => ['Statement 1'],
            ])
            ->assertRedirect('/events/' . $slug)
            ->assertSessionHasErrors('declaration');

        $this->assertSame(0, $event->registrations()->count());

        $this->post('/events/' . $slug . '/register', [
            'declaration' => ['Statement 1', 'Statement 2'],
        ])->assertRedirect();

        $registration = $event->registrations()->firstOrFail();
        $this->assertDatabaseHas('event_field_answers', [
            'event_registration_id' => $registration->id,
            'event_field_id' => $field->id,
            'value' => json_encode(['Statement 1', 'Statement 2']),
        ]);
    }

    public function test_checkbox_single_mode_rejects_multiple_options(): void
    {
        $slug = 'checkbox-single-' . uniqid();
        $event = \App\Models\Event::create([
            'title' => 'Checkbox Single Event',
            'slug' => $slug,
            'status' => 'PUBLISHED',
            'intro_html' => '',
        ]);
        $event->fields()->create([
            'field_key' => 'favorite',
            'label' => 'Favorite',
            'type' => 'checkbox',
            'checkbox_mode' => 'single',
            'is_required' => true,
            'options_json' => ['Option 1', 'Option 2'],
            'is_active' => true,
        ]);

        $this->get('/events/' . $slug)
            ->assertOk()
            ->assertSee('data-checkbox-mode="single"', false);

        $this->from('/events/' . $slug)
            ->post('/events/' . $slug . '/register', [
                'favorite' => ['Option 1', 'Option 2'],
            ])
            ->assertRedirect('/events/' . $slug)
            ->assertSessionHasErrors('favorite');

        $this->assertSame(0, $event->registrations()->count());

        $this->post('/events/' . $slug . '/register', [
            'favorite' => ['Option 2'],
        ])->assertRedirect();

        $this->assertSame(1, $event->registrations()->count());
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

    public function test_new_registration_is_appended_to_its_google_sheet(): void
    {
        $slug = 'open-house-' . uniqid();
        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => $slug,
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
            'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/event-sheet-id/edit',
        ]);

        $event->fields()->create([
            'field_key' => 'full_name',
            'label' => 'Full Name',
            'type' => 'text',
            'is_required' => true,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $googleSheet = \Mockery::mock(EventRegistrationGoogleSheetService::class);
        $googleSheet->shouldReceive('appendRegistration')->once()->with(\Mockery::on(function ($registration) use ($event) {
            return $registration->event_id === $event->id;
        }));
        $this->app->instance(EventRegistrationGoogleSheetService::class, $googleSheet);

        $this->post('/events/' . $slug . '/register', [
            'full_name' => 'Jane Doe',
        ])->assertRedirect();
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

    public function test_event_registration_attachment_route_serves_file(): void
    {
        Storage::fake('event');

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

        $registration = $event->registrations()->create([
            'code' => 'ECODE-IMG-' . uniqid(),
            'status' => 'SUBMITTED',
            'amount' => 0,
            'registered_at' => now(),
        ]);

        $storedValue = $registration->code . '/preview.png';
        $file = UploadedFile::fake()->image('preview.png', 100, 100);
        Storage::disk('event')->put($storedValue, $file->getContent());

        $response = $this->get(route('event.registration.attachment', [$event, $registration]) . '?file=' . urlencode($storedValue));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_event_delete_many_deletes_selected_events(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $eventOne = \App\Models\Event::create([
            'title' => 'Event One 2026',
            'slug' => 'event-one-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $eventTwo = \App\Models\Event::create([
            'title' => 'Event Two 2026',
            'slug' => 'event-two-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $response = $this->post(route('event.deleteMany'), [
            'ids' => [$eventOne->id, $eventTwo->id],
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('event.index'));
        $this->assertDatabaseMissing('events', ['id' => $eventOne->id]);
        $this->assertDatabaseMissing('events', ['id' => $eventTwo->id]);
    }

    public function test_event_destroy_deletes_related_form_email_and_registration_data(): void
    {
        Storage::fake('event');

        $event = \App\Models\Event::create([
            'title' => 'Open House 2026',
            'slug' => 'open-house-' . uniqid(),
            'status' => 'PUBLISHED',
            'intro_html' => '<p>Welcome</p>',
        ]);

        $form = $event->forms()->create([
            'field_key' => 'full_name',
            'label' => 'Full Name',
            'type' => 'text',
            'is_required' => true,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $emailTemplate = $event->emailTemplates()->create([
            'key' => 'registration_confirmation',
            'subject' => 'Registration Confirmed',
            'body' => 'Hello',
        ]);

        $registration = $event->registrations()->create([
            'code' => 'ECODE-DELETE-' . uniqid(),
            'status' => 'SUBMITTED',
            'amount' => 0,
            'registered_at' => now(),
        ]);

        $attachmentPath = $registration->code . '/attachment.pdf';
        Storage::disk('event')->put($attachmentPath, 'pdf-contents');

        $registration->fieldAnswers()->create([
            'event_field_id' => $form->id,
            'value' => $attachmentPath,
        ]);

        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]));

        $response = $this->delete(route('event.destroy', $event));

        $response->assertRedirect(route('event.index'));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        $this->assertDatabaseMissing('event_fields', ['id' => $form->id]);
        $this->assertDatabaseMissing('event_email_templates', ['id' => $emailTemplate->id]);
        $this->assertDatabaseMissing('event_registrations', ['id' => $registration->id]);
        $this->assertFalse(Storage::disk('event')->exists($attachmentPath));
    }
}
