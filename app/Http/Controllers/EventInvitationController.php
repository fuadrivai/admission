<?php

namespace App\Http\Controllers;

use App\Exceptions\EventInvitationDeliveryException;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\EventInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class EventInvitationController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizeMicrositeManagement();

        $emailField = $event->fields()
            ->where('type', 'email')
            ->where('is_primary_email', true)
            ->first();

        $registrations = $event->registrations()
            ->with([
                'invitation.emailLogs' => function ($query) {
                    $query->latest();
                },
                'fieldAnswers' => function ($query) use ($emailField) {
                    if ($emailField) {
                        $query->where('event_field_id', $emailField->id);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->latest('registered_at')
            ->paginate(20);

        return view('event.invitations.index', [
            'title' => 'Event Invitations',
            'event' => $event,
            'emailField' => $emailField,
            'registrations' => $registrations,
        ]);
    }

    public function send(
        Request $request,
        Event $event,
        EventRegistration $registration,
        EventInvitationService $invitationService
    ) {
        $this->authorizeMicrositeManagement();
        abort_if((int) $registration->event_id !== (int) $event->id, 404);

        $validated = $request->validate([
            'type' => ['required', Rule::in(['INVITATION', 'REMINDER'])],
        ]);

        $emailField = $event->fields()
            ->where('type', 'email')
            ->where('is_primary_email', true)
            ->first();

        if (! $emailField) {
            return back()->with('error', 'Set a primary email field in the Event form before sending invitations.');
        }

        $emailAnswer = $registration->fieldAnswers()
            ->where('event_field_id', $emailField->id)
            ->first();
        $email = $emailAnswer ? trim((string) $emailAnswer->value) : '';

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'This registration does not contain a valid email address.');
        }

        $existingInvitation = $registration->invitation;
        if ($validated['type'] === 'REMINDER' && (! $existingInvitation || $existingInvitation->status !== 'SENT')) {
            return back()->with('error', 'Send the invitation successfully before sending a reminder.');
        }

        if ($validated['type'] === 'INVITATION' && $existingInvitation && $existingInvitation->status === 'SENT') {
            return back()->with('error', 'An invitation was already sent. Use the reminder action instead.');
        }

        try {
            $invitationService->send($event, $registration, $email, $validated['type']);
        } catch (EventInvitationDeliveryException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Unexpected event invitation error.', [
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'exception' => get_class($exception),
            ]);

            return back()->with('error', 'The invitation could not be processed. Please contact an administrator.');
        }

        $message = $validated['type'] === 'REMINDER'
            ? 'Reminder sent successfully.'
            : 'Invitation sent successfully.';

        return back()->with('success', $message);
    }

    private function authorizeMicrositeManagement(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }
}
