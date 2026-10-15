<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCheckin;
use App\Models\EventInvitation;
use App\Models\EventRegistration;
use App\Models\EventWalkInToken;
use App\Services\EventInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventCheckinController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $this->authorizeStaff();

        $microsite = $event->microsite;
        $revision = $microsite ? $microsite->publishedRevision : null;
        $studentField = $revision ? $revision->studentNameField : null;
        $parentField = $revision ? $revision->parentNameField : null;
        $emailField = $event->fields()
            ->where('type', 'email')
            ->where('is_primary_email', true)
            ->first();
        $fieldIds = array_values(array_filter([
            $studentField ? $studentField->id : null,
            $parentField ? $parentField->id : null,
            $emailField ? $emailField->id : null,
        ]));
        $search = trim((string) $request->query('q', ''));

        $registrations = $event->registrations()
            ->with([
                'checkin',
                'invitation',
                'fieldAnswers' => function ($query) use ($fieldIds) {
                    $query->whereIn('event_field_id', $fieldIds);
                },
            ])
            ->when($search !== '', function ($query) use ($search, $fieldIds) {
                $query->where(function ($query) use ($search, $fieldIds) {
                    $query->where('code', 'like', '%' . $search . '%')
                        ->orWhereHas('fieldAnswers', function ($answers) use ($search, $fieldIds) {
                            $answers->whereIn('event_field_id', $fieldIds)
                                ->where('value', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('registered_at')
            ->paginate(25)
            ->appends($request->query());

        return view('event.checkin.index', [
            'title' => 'Event Check-in',
            'event' => $event,
            'revision' => $revision,
            'studentField' => $studentField,
            'parentField' => $parentField,
            'emailField' => $emailField,
            'fieldIds' => $fieldIds,
            'registrations' => $registrations,
            'search' => $search,
        ]);
    }

    public function staffCheckin(Event $event, EventRegistration $registration)
    {
        $this->authorizeStaff();
        abort_if((int) $registration->event_id !== (int) $event->id, 404);

        DB::transaction(function () use ($event, $registration) {
            $lockedRegistration = EventRegistration::whereKey($registration->id)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedRegistration->checkin()->exists()) {
                EventCheckin::create([
                    'event_id' => $event->id,
                    'event_registration_id' => $lockedRegistration->id,
                    'event_invitation_id' => $lockedRegistration->invitation ? $lockedRegistration->invitation->id : null,
                    'checked_in_by' => auth()->id(),
                    'source' => 'STAFF',
                    'checked_in_at' => now(),
                ]);
            }
        });

        return redirect()->route('event.checkin.index', $event)
            ->with('success', 'Attendance recorded.');
    }

    public function issueFamilyCode(
        Event $event,
        EventRegistration $registration,
        EventInvitationService $invitationService
    ) {
        $this->authorizeStaff();
        abort_if((int) $registration->event_id !== (int) $event->id, 404);

        $emailField = $event->fields()
            ->where('type', 'email')
            ->where('is_primary_email', true)
            ->first();
        $emailAnswer = $emailField
            ? $registration->fieldAnswers()->where('event_field_id', $emailField->id)->first()
            : null;
        $email = $emailAnswer ? trim((string) $emailAnswer->value) : null;
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = null;
        }

        list(, $code) = $invitationService->issueCode($event, $registration, $email);

        return redirect()->route('event.checkin.index', $event)
            ->with('success', 'Family code ready to share with the verified parent.')
            ->with('family_code', $code);
    }

    public function selfCheckin(Event $event)
    {
        $invitationId = session('microsite_verified.' . $event->id);
        abort_unless($invitationId, 403);

        $result = DB::transaction(function () use ($event, $invitationId) {
            $invitation = EventInvitation::where('event_id', $event->id)
                ->whereKey($invitationId)
                ->first();
            abort_unless($invitation, 403);

            $registration = EventRegistration::whereKey($invitation->event_registration_id)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->firstOrFail();
            $existing = $registration->checkin;

            if ($existing) {
                return false;
            }

            EventCheckin::create([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'event_invitation_id' => $invitation->id,
                'source' => 'FAMILY',
                'checked_in_at' => now(),
            ]);

            return true;
        });

        return redirect()->route('microsite.checkin', $event->slug)
            ->with('checkin_message', $result ? 'Check-in complete. Your attendance has been recorded.' : 'This family has already checked in.');
    }

    public function authorizeWalkIn(Event $event)
    {
        $this->authorizeStaff();

        $revision = $event->microsite ? $event->microsite->publishedRevision : null;
        abort_unless($revision, 422, 'Publish the Microsite before registering walk-in families.');

        $studentField = $revision->studentNameField;
        $parentField = $revision->parentNameField;
        if (! $studentField || ! $parentField || $studentField->id === $parentField->id) {
            return redirect()->route('event.checkin.index', $event)
                ->withErrors(['walk_in' => 'Select different student-name and parent-name fields in Microsite settings first.']);
        }

        $token = $this->generateWalkInToken();
        EventWalkInToken::create([
            'event_id' => $event->id,
            'issued_by' => auth()->id(),
            'token_hash' => $this->walkInTokenHash($event, $token),
            'expires_at' => now()->addMinutes(10),
        ]);

        return redirect()->route('event.checkin.index', $event)
            ->with('success', 'Temporary walk-in access is ready. Give the token to the verified parent.')
            ->with('walk_in_token', $token)
            ->with('walk_in_url', route('microsite.walk-in', $event->slug));
    }

    public function walkInPage(Event $event)
    {
        $revision = $event->microsite ? $event->microsite->publishedRevision : null;
        abort_unless($revision, 404);

        return view('microsite.walk-in', [
            'event' => $event,
            'revision' => $revision,
            'studentField' => $revision->studentNameField,
            'parentField' => $revision->parentNameField,
            'emailField' => $event->fields()
                ->where('type', 'email')
                ->where('is_primary_email', true)
                ->first(),
        ]);
    }

    public function completeWalkIn(Request $request, Event $event, EventInvitationService $invitationService)
    {
        $revision = $event->microsite ? $event->microsite->publishedRevision : null;
        abort_unless($revision, 404);
        $studentField = $revision->studentNameField;
        $parentField = $revision->parentNameField;
        abort_unless($studentField && $parentField && $studentField->id !== $parentField->id, 422);

        $emailField = $event->fields()
            ->where('type', 'email')
            ->where('is_primary_email', true)
            ->first();
        $rules = [
            'token' => ['required', 'string', 'regex:/^[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{8}$/i'],
            'student_name' => ['required', 'string', 'max:255'],
            'parent_name' => ['required', 'string', 'max:255'],
        ];
        if ($emailField) {
            $rules['parent_email'] = [$emailField->is_required ? 'required' : 'nullable', 'email', 'max:255'];
        }
        $validated = $request->validate($rules);

        $invitationId = DB::transaction(function () use ($event, $revision, $studentField, $parentField, $emailField, $validated, $invitationService) {
            $walkInToken = EventWalkInToken::where('event_id', $event->id)
                ->where('token_hash', $this->walkInTokenHash($event, $validated['token']))
                ->lockForUpdate()
                ->first();

            if (! $walkInToken || $walkInToken->used_at || $walkInToken->expires_at->isPast()) {
                throw ValidationException::withMessages([
                    'token' => 'This walk-in authorization is invalid or expired. Please ask reception staff for a new one.',
                ]);
            }

            $registration = $event->registrations()->create([
                'status' => 'SUBMITTED',
                'amount' => 0,
                'registered_at' => now(),
            ]);

            $answers = [
                [$studentField, $validated['student_name']],
                [$parentField, $validated['parent_name']],
            ];
            if ($emailField && ! empty($validated['parent_email'])) {
                $answers[] = [$emailField, $validated['parent_email']];
            }

            foreach ($answers as list($field, $value)) {
                $registration->fieldAnswers()->create([
                    'event_field_id' => $field->id,
                    'value' => $value,
                ]);
            }

            list($invitation) = $invitationService->issueCode(
                $event,
                $registration,
                $validated['parent_email'] ?? null
            );

            EventCheckin::create([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'event_invitation_id' => $invitation->id,
                'checked_in_by' => auth()->id(),
                'source' => 'WALK_IN',
                'checked_in_at' => now(),
            ]);

            $walkInToken->update(['used_at' => now()]);

            return $invitation->id;
        });

        $request->session()->put('microsite_verified.' . $event->id, $invitationId);

        return redirect()->route('microsite.checkin', $event->slug)
            ->with('checkin_message', 'Your family has been registered and checked in.');
    }

    private function generateWalkInToken(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $token = '';

        for ($index = 0; $index < 8; $index++) {
            $token .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $token;
    }

    private function walkInTokenHash(Event $event, string $token): string
    {
        return hash_hmac('sha256', $event->id . ':' . strtoupper(trim($token)), (string) config('app.key'));
    }

    private function authorizeStaff(): void
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'user'], true), 403);
    }
}
