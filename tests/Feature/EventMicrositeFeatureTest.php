<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCheckin;
use App\Mail\AdmissionEmail;
use App\Models\EventInvitation;
use App\Models\User;
use App\Services\EventInvitationService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EventMicrositeFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('user');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('intro_html')->nullable();
            $table->string('status')->default('DRAFT');
            $table->string('availability_type')->nullable();
            $table->dateTime('active_until')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('uuid')->nullable();
            $table->timestamps();
        });

        Schema::create('event_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('field_key');
            $table->string('label');
            $table->string('type');
            $table->boolean('is_primary_email')->default(false);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('code')->unique();
            $table->string('status')->default('SUBMITTED');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('event_field_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_registration_id');
            $table->unsignedBigInteger('event_field_id');
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('event_email_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('key', 50);
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
        });

        require_once base_path('database/migrations/2026_10_15_000001_create_event_microsite_tables.php');
        (new \CreateEventMicrositeTables())->up();
        require_once base_path('database/migrations/2026_10_15_000002_create_event_invitation_tables.php');
        (new \CreateEventInvitationTables())->up();
        require_once base_path('database/migrations/2026_10_15_000003_add_event_microsite_checkin.php');
        (new \AddEventMicrositeCheckin())->up();
        require_once base_path('database/migrations/2026_10_15_000004_create_event_walk_in_tokens_table.php');
        (new \CreateEventWalkInTokensTable())->up();
        require_once base_path('database/migrations/2026_10_15_000005_add_microsite_agenda_content.php');
        (new \AddMicrositeAgendaContent())->up();
    }

    public function test_microsite_is_hidden_until_a_saved_draft_is_published(): void
    {
        $event = $this->createEvent();
        $user = $this->createAdmin();

        $this->actingAs($user)->get(route('event.microsite.edit', $event))
            ->assertOk()
            ->assertSee('Student name field')
            ->assertSee('Public URL:')
            ->assertSee('action="' . route('event.microsite.publish', $event) . '"', false);
        $response = $this->actingAs($user)->put(route('event.microsite.update', $event), $this->micrositeData([
            'title' => 'Open Day Microsite',
            'rundown_items' => [
                ['title' => 'Registration', 'starts_at' => '09:00', 'ends_at' => '09:30'],
                ['title' => 'School tour', 'starts_at' => '10:00', 'ends_at' => '10:30'],
            ],
        ]));

        $response->assertRedirect(route('event.microsite.edit', $event));
        $this->get(route('microsite.show', $event->slug))->assertNotFound();

        $this->actingAs($user)->post(route('event.microsite.publish', $event))
            ->assertRedirect(route('event.microsite.edit', $event));

        $this->assertDatabaseHas('event_microsite_revisions', [
            'status' => 'PUBLISHED',
            'title' => 'Open Day Microsite',
        ]);
        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Open Day Microsite')
            ->assertSee('Registration')
            ->assertSee('09:00')
            ->assertSee('School tour')
            ->assertSee('10:30');
    }

    public function test_publishing_without_a_saved_draft_returns_to_the_editor_with_a_validation_message(): void
    {
        $event = $this->createEvent();
        $user = $this->createAdmin();

        $this->actingAs($user)
            ->from(route('event.microsite.edit', $event))
            ->post(route('event.microsite.publish', $event))
            ->assertRedirect(route('event.microsite.edit', $event))
            ->assertSessionHasErrors([
                'microsite' => 'Save a Microsite draft before publishing it.',
            ]);
    }

    public function test_staff_can_manage_structured_rundown_speakers_and_faq_content(): void
    {
        $event = $this->createEvent();
        $staff = $this->createAdmin();
        $payload = $this->micrositeData([
            'rundown_items' => [
                ['title' => 'Open Registration', 'starts_at' => '08:00', 'ends_at' => '08:30'],
                ['title' => 'Welcoming Speech', 'starts_at' => '08:30', 'ends_at' => '09:00'],
                ['title' => 'Closing', 'starts_at' => '12:00', 'ends_at' => '12:15'],
            ],
            'speakers' => [
                ['name' => 'Suwandi Soh', 'position' => 'Principal Secondary'],
                ['name' => 'Fuad Rivai', 'position' => 'Principal Primary'],
            ],
            'faq_items' => [
                [
                    'question' => 'Where is MHIS located?',
                    'answer' => "MHIS has three campuses:\nBintaro, Bangka, and Semarang.",
                ],
                [
                    'question' => 'How much is the enrolment fee?',
                    'answer' => 'Fees depend on the campus and level selected.',
                ],
            ],
        ]);

        $this->actingAs($staff)
            ->put(route('event.microsite.update', $event), $payload)
            ->assertRedirect(route('event.microsite.edit', $event));
        $this->actingAs($staff)->post(route('event.microsite.publish', $event));

        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Open Registration')
            ->assertSee('08:00')
            ->assertSee('08:30')
            ->assertSee('Welcoming Speech')
            ->assertSee('Closing')
            ->assertSee('Suwandi Soh')
            ->assertSee('Principal Secondary')
            ->assertSee('Fuad Rivai')
            ->assertSee('Principal Primary')
            ->assertSee('Where is MHIS located?')
            ->assertSee('MHIS has three campuses:')
            ->assertSee('How much is the enrolment fee?')
            ->assertSee('Fees depend on the campus and level selected.');

        $this->actingAs($staff)->get(route('event.microsite.edit', $event))
            ->assertOk()
            ->assertSee('Open Registration')
            ->assertSee('Suwandi Soh')
            ->assertSee('Where is MHIS located?');
    }

    public function test_rundown_rejects_an_end_time_before_its_start_time(): void
    {
        $event = $this->createEvent();
        $staff = $this->createAdmin();

        $this->actingAs($staff)
            ->from(route('event.microsite.edit', $event))
            ->put(route('event.microsite.update', $event), $this->micrositeData([
                'rundown_items' => [
                    ['title' => 'Invalid session', 'starts_at' => '09:00', 'ends_at' => '08:30'],
                ],
            ]))
            ->assertSessionHasErrors('rundown_items');
    }

    public function test_speaker_photo_is_stored_and_kept_when_editing_speaker_details(): void
    {
        Storage::fake('public');
        $event = $this->createEvent();
        $staff = $this->createAdmin();
        $payload = $this->micrositeData([
            'speakers' => [
                [
                    'name' => 'Suwandi Soh',
                    'position' => 'Principal Secondary',
                    'photo' => UploadedFile::fake()->image('principal.png', 120, 120),
                ],
            ],
        ]);

        $this->actingAs($staff)->put(route('event.microsite.update', $event), $payload)
            ->assertRedirect(route('event.microsite.edit', $event));

        $draft = $event->microsite->draftRevision;
        $speaker = json_decode($draft->speakers, true)[0];
        $this->assertNotEmpty($speaker['photo_path']);
        Storage::disk('public')->assertExists($speaker['photo_path']);
        $this->actingAs($staff)->post(route('event.microsite.publish', $event))
            ->assertRedirect(route('event.microsite.edit', $event));

        $this->actingAs($staff)->put(route('event.microsite.update', $event), $this->micrositeData([
            'speakers' => [
                ['name' => 'Suwandi Soh', 'position' => 'Principal and Head of School'],
            ],
        ]))->assertRedirect(route('event.microsite.edit', $event));

        $updatedSpeaker = json_decode(Event::findOrFail($event->id)->microsite->draftRevision->speakers, true)[0];
        $this->assertSame($speaker['photo_path'], $updatedSpeaker['photo_path']);
        $this->assertSame('Principal and Head of School', $updatedSpeaker['position']);
        Storage::disk('public')->assertExists($updatedSpeaker['photo_path']);

        $this->actingAs($staff)->put(route('event.microsite.update', $event), $this->micrositeData([
            'speakers' => [
                [
                    'name' => 'Suwandi Soh',
                    'position' => 'Principal and Head of School',
                    'remove_photo' => '1',
                ],
            ],
        ]))->assertRedirect(route('event.microsite.edit', $event));

        $removedSpeaker = json_decode(Event::findOrFail($event->id)->microsite->draftRevision->speakers, true)[0];
        $this->assertNull($removedSpeaker['photo_path']);
        Storage::disk('public')->assertExists($speaker['photo_path']);
        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Suwandi Soh')
            ->assertSee(Storage::disk('public')->url($speaker['photo_path']));
    }

    public function test_saving_a_new_draft_does_not_change_the_published_page_until_republished(): void
    {
        $event = $this->createEvent();
        $user = $this->createAdmin();

        $this->actingAs($user)->put(route('event.microsite.update', $event), $this->micrositeData([
            'title' => 'Published title',
        ]));
        $this->actingAs($user)->post(route('event.microsite.publish', $event));

        $this->actingAs($user)->put(route('event.microsite.update', $event), $this->micrositeData([
            'title' => 'Unpublished draft title',
        ]));

        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Published title')
            ->assertDontSee('Unpublished draft title');
    }

    public function test_existing_text_rundown_and_faq_are_still_shown_after_the_structured_content_upgrade(): void
    {
        $event = $this->createEvent();
        $staff = $this->createAdmin();

        $this->actingAs($staff)->put(route('event.microsite.update', $event), $this->micrositeData());
        $revision = $event->microsite->draftRevision;
        $revision->update([
            'rundown' => "09:15 | Existing registration\n10:00 | Existing welcome",
            'faq' => "Where is the venue?\nThe main campus.",
            'rundown_items' => null,
            'faq_items' => null,
        ]);
        $this->actingAs($staff)->post(route('event.microsite.publish', $event));

        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Existing registration')
            ->assertSee('09:15')
            ->assertSee('Existing welcome')
            ->assertSee('Where is the venue?')
            ->assertSee('The main campus.');
    }

    public function test_published_microsite_is_public_even_if_event_registration_is_not_open(): void
    {
        $event = $this->createEvent();
        $event->update(['status' => 'DRAFT']);
        $user = $this->createAdmin();

        $this->actingAs($user)->put(route('event.microsite.update', $event), $this->micrositeData());
        $this->actingAs($user)->post(route('event.microsite.publish', $event));

        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Open Day')
            ->assertDontSee('Register for this event');
    }

    public function test_public_qr_and_family_code_access_greeting_and_checkin(): void
    {
        $event = $this->createEvent();
        $studentField = $this->createNameField($event, 'student_name', 'Student name');
        $parentField = $this->createNameField($event, 'parent_name', 'Parent name');
        $registration = $this->createRegistrationWithPrimaryEmail($event, 'parent@example.com');
        $registration->fieldAnswers()->createMany([
            ['event_field_id' => $studentField->id, 'value' => 'Sam Student'],
            ['event_field_id' => $parentField->id, 'value' => 'Alex Parent'],
        ]);
        $this->publishMicrosite($event, [
            'student_name_field_id' => $studentField->id,
            'parent_name_field_id' => $parentField->id,
        ]);

        $this->get(route('microsite.qr', $event->slug))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8')
            ->assertSee('<svg', false);
        $this->get(route('microsite.show', $event->slug))->assertOk();

        list($invitation, $code) = app(EventInvitationService::class)->issueCode($event, $registration);
        $this->post(route('microsite.verify-code', $event->slug), [
            'code' => strtolower($code),
            'destination' => 'microsite',
        ])->assertRedirect(route('microsite.show', $event->slug));

        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Alex Parent')
            ->assertSee('Sam Student');

        $this->post(route('microsite.self-checkin', $event->slug))
            ->assertRedirect(route('microsite.checkin', $event->slug));
        $this->assertDatabaseHas('event_checkins', [
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'event_invitation_id' => $invitation->id,
            'source' => 'FAMILY',
        ]);

        $this->post(route('microsite.self-checkin', $event->slug))
            ->assertSessionHas('checkin_message', 'This family has already checked in.');
        $this->assertSame(1, EventCheckin::where('event_registration_id', $registration->id)->count());
    }

    public function test_staff_can_authorize_walk_in_and_parent_can_register_without_login(): void
    {
        $event = $this->createEvent();
        $studentField = $this->createNameField($event, 'student_name', 'Student name');
        $parentField = $this->createNameField($event, 'parent_name', 'Parent name');
        $this->publishMicrosite($event, [
            'student_name_field_id' => $studentField->id,
            'parent_name_field_id' => $parentField->id,
        ]);
        $staff = User::create([
            'name' => 'Check-in Staff',
            'email' => 'staff-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);
        $staff->role = 'user';
        $staff->save();

        $this->actingAs($staff)->get(route('event.checkin.index', $event))
            ->assertOk()
            ->assertSee('Authorize a walk-in family')
            ->assertSee('Student');
        $this->actingAs($staff)
            ->post(route('event.checkin.walk-in', $event))
            ->assertRedirect(route('event.checkin.index', $event))
            ->assertSessionHas('success', 'Temporary walk-in access is ready. Give the token to the verified parent.');
        $token = session('walk_in_token');
        $this->assertMatchesRegularExpression('/^[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{8}$/', $token);
        $this->get(route('microsite.walk-in', $event->slug))
            ->assertOk()
            ->assertSee('Complete your family details');

        $this->app['auth']->guard()->logout();
        $this->post(route('microsite.walk-in.complete', $event->slug), [
                'token' => $token,
                'student_name' => 'Jamie Student',
                'parent_name' => 'Taylor Parent',
            ])
            ->assertRedirect(route('microsite.checkin', $event->slug));

        $registration = $event->registrations()->with('invitation')->firstOrFail();
        $this->assertDatabaseHas('event_checkins', [
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'source' => 'WALK_IN',
            'checked_in_by' => null,
        ]);
        $this->assertDatabaseHas('event_walk_in_tokens', [
            'event_id' => $event->id,
            'issued_by' => $staff->id,
        ]);
        $this->assertNotNull(DB::table('event_walk_in_tokens')->where('event_id', $event->id)->value('used_at'));
        $this->from(route('microsite.walk-in', $event->slug))
            ->post(route('microsite.walk-in.complete', $event->slug), [
                'token' => $token,
                'student_name' => 'Jamie Student',
                'parent_name' => 'Taylor Parent',
            ])
            ->assertSessionHasErrors('token');
        $this->assertSame(1, $event->registrations()->count());
        $this->get(route('microsite.checkin', $event->slug))
            ->assertOk()
            ->assertSee('Taylor Parent')
            ->assertSee('Jamie Student')
            ->assertSee('Check-in recorded');
    }

    public function test_family_code_must_belong_to_the_published_event(): void
    {
        $event = $this->createEvent();
        $anotherEvent = $this->createEvent();
        $registration = $this->createRegistrationWithPrimaryEmail($anotherEvent, 'parent@example.com');
        $this->publishMicrosite($event);
        $this->publishMicrosite($anotherEvent);
        list($invitation, $code) = app(EventInvitationService::class)->issueCode($anotherEvent, $registration);

        $this->from(route('microsite.checkin', $event->slug))
            ->post(route('microsite.verify-code', $event->slug), [
                'code' => $code,
                'destination' => 'check-in',
            ])
            ->assertSessionHasErrors('code');
        $this->assertDatabaseMissing('event_checkins', [
            'event_invitation_id' => $invitation->id,
        ]);
    }

    public function test_microsite_management_is_restricted_to_non_user_accounts(): void
    {
        $event = $this->createEvent();
        $user = User::create([
            'name' => 'Limited User',
            'email' => 'limited-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);
        $user = $user->fresh();
        $this->assertSame('user', $user->role);

        $this->actingAs($user)->get(route('event.microsite.edit', $event))->assertForbidden();
    }

    public function test_event_invitation_uses_a_five_character_family_code_and_logs_delivery(): void
    {
        Mail::fake();

        $event = $this->createEvent();
        $user = $this->createAdmin();
        $registration = $this->createRegistrationWithPrimaryEmail($event, 'parent@example.com');
        $this->publishMicrosite($event);

        $this->actingAs($user)->from(route('event.invitations.index', $event))
            ->post(route('event.invitations.send', [$event, $registration]), [
            'type' => 'INVITATION',
        ])->assertRedirect(route('event.invitations.index', $event));

        $invitation = EventInvitation::where('event_registration_id', $registration->id)->firstOrFail();
        $code = Crypt::decryptString($invitation->code_encrypted);

        $this->assertSame('SENT', $invitation->status);
        $this->assertSame('parent@example.com', $invitation->email);
        $this->assertMatchesRegularExpression('/^[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{5}$/', $code);
        $this->assertNotSame($code, $invitation->code_hash);
        $this->assertDatabaseHas('event_email_logs', [
            'event_id' => $event->id,
            'event_invitation_id' => $invitation->id,
            'type' => 'INVITATION',
            'status' => 'SENT',
        ]);

        Mail::assertSent(AdmissionEmail::class, function ($mail) use ($code, $event) {
            return strpos($mail->data['content'], $code) !== false
                && strpos($mail->data['content'], route('microsite.show', $event->slug)) !== false;
        });
    }

    public function test_reminder_reuses_the_family_code_and_is_logged_separately(): void
    {
        Mail::fake();

        $event = $this->createEvent();
        $user = $this->createAdmin();
        $registration = $this->createRegistrationWithPrimaryEmail($event, 'parent@example.com');
        $this->publishMicrosite($event);

        $this->actingAs($user)->post(route('event.invitations.send', [$event, $registration]), [
            'type' => 'INVITATION',
        ]);
        $invitation = EventInvitation::where('event_registration_id', $registration->id)->firstOrFail();
        $originalCode = Crypt::decryptString($invitation->code_encrypted);

        $this->actingAs($user)->post(route('event.invitations.send', [$event, $registration]), [
            'type' => 'REMINDER',
        ])->assertSessionHas('success', 'Reminder sent successfully.');

        $this->assertSame($originalCode, Crypt::decryptString($invitation->fresh()->code_encrypted));
        $this->assertDatabaseHas('event_email_logs', [
            'event_invitation_id' => $invitation->id,
            'type' => 'REMINDER',
            'status' => 'SENT',
        ]);
        Mail::assertSent(AdmissionEmail::class, 2);
    }

    public function test_invitation_cannot_be_sent_for_a_registration_from_another_event(): void
    {
        $event = $this->createEvent();
        $anotherEvent = $this->createEvent();
        $user = $this->createAdmin();
        $registration = $this->createRegistrationWithPrimaryEmail($anotherEvent, 'parent@example.com');

        $this->actingAs($user)
            ->post(route('event.invitations.send', [$event, $registration]), ['type' => 'INVITATION'])
            ->assertNotFound();
    }

    public function test_staff_can_issue_or_reveal_a_family_code_after_verifying_a_registration(): void
    {
        $event = $this->createEvent();
        $studentField = $this->createNameField($event, 'student_name', 'Student name');
        $parentField = $this->createNameField($event, 'parent_name', 'Parent name');
        $registration = $this->createRegistrationWithPrimaryEmail($event, 'parent@example.com');
        $registration->fieldAnswers()->createMany([
            ['event_field_id' => $studentField->id, 'value' => 'Riley Student'],
            ['event_field_id' => $parentField->id, 'value' => 'Morgan Parent'],
        ]);
        $this->publishMicrosite($event, [
            'student_name_field_id' => $studentField->id,
            'parent_name_field_id' => $parentField->id,
        ]);
        $staff = User::create([
            'name' => 'Check-in Staff',
            'email' => 'staff-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);
        $staff->role = 'user';
        $staff->save();

        $this->actingAs($staff)
            ->post(route('event.checkin.code', [$event, $registration]))
            ->assertRedirect(route('event.checkin.index', $event));
        $code = session('family_code');
        $this->assertMatchesRegularExpression('/^[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{5}$/', $code);

        $this->post(route('microsite.verify-code', $event->slug), [
            'code' => $code,
            'destination' => 'microsite',
        ])->assertRedirect(route('microsite.show', $event->slug));
        $this->get(route('microsite.show', $event->slug))
            ->assertOk()
            ->assertSee('Morgan Parent')
            ->assertSee('Riley Student');
    }

    private function createEvent(): Event
    {
        return Event::create([
            'title' => 'Open Day 2026',
            'slug' => 'open-day-' . uniqid(),
            'status' => 'PUBLISHED',
        ]);
    }

    private function createAdmin(): User
    {
        $user = User::create([
            'name' => 'Event Admin',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);
        $user->role = 'admin';
        $user->save();

        return $user;
    }

    private function micrositeData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Open Day',
            'tagline' => 'Welcome to our school',
            'description' => 'Meet our community.',
            'starts_at' => '2026-11-01T09:00',
            'ends_at' => '2026-11-01T12:00',
            'location' => 'Main Campus',
            'map_url' => 'https://maps.google.com/?q=school',
            'primary_color' => '#174A83',
            'secondary_color' => '#D9B66F',
            'rundown_items' => [
                ['title' => 'Registration', 'starts_at' => '09:00', 'ends_at' => '09:30'],
            ],
            'speakers' => [],
            'faq_items' => [
                ['question' => 'What should I bring?', 'answer' => 'A photo ID.'],
            ],
            'student_name_field_id' => null,
            'parent_name_field_id' => null,
        ], $overrides);
    }

    private function createRegistrationWithPrimaryEmail(Event $event, string $email)
    {
        $field = $event->fields()->create([
            'field_key' => 'parent_email',
            'label' => 'Parent email',
            'type' => 'email',
            'is_primary_email' => true,
        ]);
        $registration = $event->registrations()->create([
            'code' => 'REG-' . uniqid(),
            'status' => 'SUBMITTED',
            'registered_at' => now(),
        ]);
        $registration->fieldAnswers()->create([
            'event_field_id' => $field->id,
            'value' => $email,
        ]);

        return $registration;
    }

    private function createNameField(Event $event, string $key, string $label)
    {
        return $event->fields()->create([
            'field_key' => $key,
            'label' => $label,
            'type' => 'text',
            'is_active' => true,
        ]);
    }

    private function publishMicrosite(Event $event, array $overrides = []): void
    {
        $user = $this->createAdmin();
        $this->actingAs($user)->put(route('event.microsite.update', $event), $this->micrositeData($overrides));
        $this->actingAs($user)->post(route('event.microsite.publish', $event));
    }
}
