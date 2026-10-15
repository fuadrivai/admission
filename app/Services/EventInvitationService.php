<?php

namespace App\Services;

use App\Exceptions\EventInvitationDeliveryException;
use App\Mail\AdmissionEmail;
use App\Models\EmailSetting;
use App\Models\Event;
use App\Models\EventEmailLog;
use App\Models\EventInvitation;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

use function App\Helpers\setupMail;

class EventInvitationService
{
    private const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function send(Event $event, EventRegistration $registration, string $email, string $type): EventInvitation
    {
        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        $microsite = $event->microsite;
        $publishedRevision = $microsite
            ? $microsite->revisions()->where('status', 'PUBLISHED')->latest('version')->first()
            : null;

        if (! $publishedRevision) {
            throw new RuntimeException('Publish the event Microsite before sending an invitation.');
        }

        $email = trim($email);
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('The registration does not have a valid primary email address.');
        }

        list($invitation, $code) = $this->issueCode($event, $registration, $email);
        $registration->loadMissing('fieldAnswers.eventField');
        $invitation->setRelation('registration', $registration);

        try {
            $this->sendEmail($event, $publishedRevision, $invitation, $code, $type);
        } catch (Throwable $exception) {
            Log::error('Event invitation email delivery failed.', [
                'event_id' => $event->id,
                'event_invitation_id' => $invitation->id,
                'exception' => get_class($exception),
            ]);

            DB::transaction(function () use ($event, $invitation, $type, $exception) {
                $invitation->update([
                    'status' => 'FAILED',
                    'send_attempts' => $invitation->send_attempts + 1,
                ]);

                EventEmailLog::create([
                    'event_id' => $event->id,
                    'event_invitation_id' => $invitation->id,
                    'recipient' => $invitation->email,
                    'type' => $type,
                    'status' => 'FAILED',
                    'error_code' => Str::limit(get_class($exception), 255, ''),
                ]);
            });

            throw new EventInvitationDeliveryException(
                'The email could not be sent. The invitation is saved and can be retried.',
                0,
                $exception
            );
        }

        DB::transaction(function () use ($event, $invitation, $type) {
            $invitation->update([
                'status' => 'SENT',
                'send_attempts' => $invitation->send_attempts + 1,
                'last_sent_at' => now(),
            ]);

            EventEmailLog::create([
                'event_id' => $event->id,
                'event_invitation_id' => $invitation->id,
                'recipient' => $invitation->email,
                'type' => $type,
                'status' => 'SENT',
                'sent_at' => now(),
            ]);
        });

        return $invitation->fresh();
    }

    public function issueCode(Event $event, EventRegistration $registration, ?string $email = null): array
    {
        if ((int) $registration->event_id !== (int) $event->id) {
            abort(404);
        }

        $microsite = $event->microsite;
        if (! $microsite || ! $microsite->publishedRevision) {
            throw new RuntimeException('Publish the event Microsite before issuing a family code.');
        }

        return DB::transaction(function () use ($event, $registration, $email) {
            $invitation = EventInvitation::where('event_registration_id', $registration->id)
                ->lockForUpdate()
                ->first();

            if ($invitation) {
                if ($email !== null) {
                    $invitation->update(['email' => $email]);
                }

                return [$invitation->fresh(), Crypt::decryptString($invitation->code_encrypted)];
            }

            $code = $this->generateCode($event);
            $invitation = EventInvitation::create([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'email' => $email,
                'code_hash' => $this->codeHash($event, $code),
                'code_encrypted' => Crypt::encryptString($code),
                'status' => 'READY',
            ]);

            return [$invitation, $code];
        });
    }

    public function findByCode(Event $event, string $code): ?EventInvitation
    {
        return EventInvitation::where('event_id', $event->id)
            ->where('code_hash', $this->codeHash($event, $code))
            ->first();
    }

    private function sendEmail(Event $event, $revision, EventInvitation $invitation, string $code, string $type): void
    {
        if ($event->branch_id) {
            $branchSetting = EmailSetting::where('branch_id', $event->branch_id)->first();
            if ($branchSetting) {
                setupMail($event->branch_id);
            }
        }

        $templateKey = $type === 'REMINDER' ? 'REMINDER' : 'INVITATION';
        $template = $event->emailTemplates()->where('key', $templateKey)->first();
        $micrositeUrl = route('microsite.show', $event->slug);
        $values = $this->templateValues($event, $invitation, $code, $micrositeUrl);
        $subject = $template && $template->subject
            ? $this->replaceVariables($template->subject, $values, false)
            : ($type === 'REMINDER' ? 'Event reminder: ' : 'You are invited: ') . $revision->title;
        $body = $template && $template->body
            ? $this->replaceVariables($template->body, $values, true)
            : $this->defaultBody($revision->title, $code, $micrositeUrl, $type);

        Mail::to($invitation->email)->send(new AdmissionEmail([
            'subject' => $subject,
            'template' => 'email-template.event-invitation',
            'event_title' => $revision->title,
            'content' => $body,
            'primary_color' => $revision->primary_color,
            'secondary_color' => $revision->secondary_color,
        ]));
    }

    private function templateValues(Event $event, EventInvitation $invitation, string $code, string $micrositeUrl): array
    {
        $values = [
            'event_title' => $event->title,
            'event_name' => $event->title,
            'invitation_code' => $code,
            'registration_code' => $code,
            'microsite_url' => $micrositeUrl,
        ];

        foreach ($invitation->registration->fieldAnswers as $answer) {
            $field = $answer->eventField;
            if (! $field || $field->type === 'attachment') {
                continue;
            }

            $value = $answer->value;
            if ($field->type === 'checkbox') {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $value = implode(', ', array_map('strval', $decoded));
                }
            }

            $values[$field->field_key] = $value;
        }

        return $values;
    }

    private function replaceVariables(string $content, array $values, bool $escape): string
    {
        return preg_replace_callback(
            '/\{\{\s*([A-Za-z0-9_-]+)\s*\}\}|\{([A-Za-z0-9_-]+)\}/',
            function ($matches) use ($values, $escape) {
                $key = $matches[1] ?: $matches[2];
                if (! array_key_exists($key, $values) || ! is_scalar($values[$key])) {
                    return '';
                }

                $value = (string) $values[$key];

                return $escape ? e($value) : $value;
            },
            $content
        ) ?: '';
    }

    private function defaultBody(string $eventTitle, string $code, string $micrositeUrl, string $type): string
    {
        $message = $type === 'REMINDER'
            ? 'This is a reminder about your upcoming event.'
            : 'You are invited to attend this event.';

        return '<p>Dear Parent/Guardian,</p>'
            . '<p>' . e($message) . '</p>'
            . '<p><strong>' . e($eventTitle) . '</strong></p>'
            . '<p>Your family access code is <strong>' . e($code) . '</strong>.</p>'
            . '<p><a href="' . e($micrositeUrl) . '">Open the event Microsite</a> to view the rundown and event information.</p>';
    }

    private function generateCode(Event $event): string
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $code = '';
            for ($index = 0; $index < 5; $index++) {
                $code .= self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)];
            }

            $hash = $this->codeHash($event, $code);
            if (! EventInvitation::where('event_id', $event->id)->where('code_hash', $hash)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Could not allocate a unique family code for this event. Please retry.');
    }

    private function codeHash(Event $event, string $code): string
    {
        return hash_hmac('sha256', $event->id . ':' . strtoupper(trim($code)), (string) config('app.key'));
    }
}
