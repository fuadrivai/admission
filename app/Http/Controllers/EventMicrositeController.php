<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\EventMicrosite;
use App\Models\EventMicrositeRevision;
use App\Services\EventInvitationService;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class EventMicrositeController extends Controller
{
    private const CONTENT_FIELDS = [
        'title',
        'tagline',
        'description',
        'starts_at',
        'ends_at',
        'location',
        'map_url',
        'primary_color',
        'secondary_color',
        'rundown',
        'faq',
        'student_name_field_id',
        'parent_name_field_id',
        'rundown_items',
        'faq_items',
        'speakers',
    ];

    public function edit(Event $event)
    {
        $this->authorizeMicrositeManagement();

        $microsite = $event->microsite;
        $revision = $microsite
            ? ($microsite->draftRevision ?: $microsite->publishedRevision)
            : null;
        $speakers = $this->storedList($revision ? $revision->speakers : null);
        foreach ($speakers as &$speaker) {
            $speaker['photo_url'] = ! empty($speaker['photo_path'])
                ? Storage::disk('public')->url($speaker['photo_path'])
                : null;
        }
        unset($speaker);

        return view('event.microsite.form', [
            'title' => 'Event Microsite',
            'event' => $event,
            'revision' => $revision,
            'publishedRevision' => $microsite ? $microsite->publishedRevision : null,
            'rundownItems' => $this->editorRundownItems($revision),
            'faqItems' => $this->editorFaqItems($revision),
            'speakers' => $speakers,
            'micrositeUrl' => route('microsite.show', $event->slug),
            'greetingFields' => $event->fields()
                ->where('is_active', true)
                ->whereIn('type', ['text', 'textarea'])
                ->orderBy('order_index')
                ->get(),
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeMicrositeManagement();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'map_url' => ['nullable', 'url', 'max:2048', 'regex:/^https:\/\/[^\s]+$/i'],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'rundown_items' => ['nullable', 'array', 'max:40'],
            'rundown_items.*.title' => ['required', 'string', 'max:255'],
            'rundown_items.*.starts_at' => ['nullable', 'date_format:H:i'],
            'rundown_items.*.ends_at' => ['nullable', 'date_format:H:i'],
            'speakers' => ['nullable', 'array', 'max:30'],
            'speakers.*.name' => ['required', 'string', 'max:255'],
            'speakers.*.position' => ['nullable', 'string', 'max:255'],
            'speakers.*.photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'speakers.*.remove_photo' => ['nullable', 'boolean'],
            'faq_items' => ['nullable', 'array', 'max:40'],
            'faq_items.*.question' => ['required', 'string', 'max:500'],
            'faq_items.*.answer' => ['required', 'string', 'max:10000'],
            'student_name_field_id' => ['nullable', 'integer', 'exists:event_fields,id'],
            'parent_name_field_id' => ['nullable', 'integer', 'different:student_name_field_id', 'exists:event_fields,id'],
        ]);

        foreach (['student_name_field_id', 'parent_name_field_id'] as $fieldName) {
            if (! empty($validated[$fieldName]) && ! $event->fields()
                ->where('is_active', true)
                ->whereIn('type', ['text', 'textarea'])
                ->whereKey($validated[$fieldName])
                ->exists()) {
                throw ValidationException::withMessages([
                    $fieldName => 'Choose an active name field from this Event.',
                ]);
            }
        }

        $sourceRevision = $event->microsite
            ? ($event->microsite->draftRevision ?: $event->microsite->publishedRevision)
            : null;
        $previousSpeakers = $this->storedList($sourceRevision ? $sourceRevision->speakers : null);
        $newSpeakerPhotos = [];
        $rundownItems = [];
        foreach ($validated['rundown_items'] ?? [] as $item) {
            if (isset($item['starts_at'], $item['ends_at']) && $item['ends_at'] < $item['starts_at']) {
                throw ValidationException::withMessages([
                    'rundown_items' => 'A rundown item end time must not be earlier than its start time.',
                ]);
            }

            $rundownItems[] = [
                'title' => trim($item['title']),
                'starts_at' => $item['starts_at'] ?? null,
                'ends_at' => $item['ends_at'] ?? null,
            ];
        }

        $speakers = [];
        try {
            foreach ($validated['speakers'] ?? [] as $key => $speaker) {
                $previousPhoto = $previousSpeakers[$key]['photo_path'] ?? null;
                $photo = $request->file("speakers.{$key}.photo");
                $newPhotoPath = $photo
                    ? $photo->store('microsites/' . $event->id . '/speakers', 'public')
                    : null;

                if ($photo && ! $newPhotoPath) {
                    throw new RuntimeException('The speaker photo could not be saved. Please try again.');
                }
                if ($newPhotoPath) {
                    $newSpeakerPhotos[] = $newPhotoPath;
                }

                $speakers[] = [
                    'name' => trim($speaker['name']),
                    'position' => isset($speaker['position']) ? trim($speaker['position']) : null,
                    'photo_path' => $newPhotoPath
                        ?: (! empty($speaker['remove_photo']) ? null : $previousPhoto),
                ];
            }
        } catch (\Throwable $exception) {
            $this->deleteSpeakerPhotos($newSpeakerPhotos, $event);
            throw $exception;
        }
        $obsoleteSpeakerPhotos = array_diff(
            array_filter(array_column($previousSpeakers, 'photo_path')),
            array_filter(array_column($speakers, 'photo_path'))
        );

        $faqItems = [];
        foreach ($validated['faq_items'] ?? [] as $item) {
            $faqItems[] = [
                'question' => trim($item['question']),
                'answer' => trim($item['answer']),
            ];
        }

        $validated['rundown_items'] = $rundownItems
            ? json_encode($rundownItems, JSON_UNESCAPED_UNICODE)
            : null;
        $validated['speakers'] = $speakers
            ? json_encode($speakers, JSON_UNESCAPED_UNICODE)
            : null;
        $validated['faq_items'] = $faqItems
            ? json_encode($faqItems, JSON_UNESCAPED_UNICODE)
            : null;
        $validated['rundown'] = null;
        $validated['faq'] = null;
        unset($validated['rundown_items.*.photo'], $validated['speakers.*.photo']);

        try {
            DB::transaction(function () use ($event, $validated) {
                $microsite = EventMicrosite::firstOrCreate(['event_id' => $event->id]);
                $microsite = EventMicrosite::whereKey($microsite->id)->lockForUpdate()->firstOrFail();
                $draft = $microsite->revisions()->where('status', 'DRAFT')->latest('version')->first();

                if (! $draft) {
                    $published = $microsite->revisions()->where('status', 'PUBLISHED')->latest('version')->first();
                    $defaults = $published
                        ? $published->only(self::CONTENT_FIELDS)
                        : array_merge([
                            'title' => $event->title,
                            'tagline' => null,
                            'description' => strip_tags((string) $event->intro_html),
                            'starts_at' => null,
                            'ends_at' => null,
                            'location' => null,
                            'map_url' => null,
                            'primary_color' => '#174A83',
                            'secondary_color' => '#D9B66F',
                            'rundown' => null,
                            'faq' => null,
                            'rundown_items' => null,
                            'faq_items' => null,
                            'speakers' => null,
                        ]);

                    $draft = $microsite->revisions()->create(array_merge($defaults, [
                        'version' => (int) $microsite->revisions()->max('version') + 1,
                        'status' => 'DRAFT',
                    ]));
                }

                $draft->update($validated);
            });
        } catch (\Throwable $exception) {
            $this->deleteSpeakerPhotos($newSpeakerPhotos, $event);
            throw $exception;
        }

        $this->deleteSpeakerPhotos($obsoleteSpeakerPhotos, $event);

        return redirect()->route('event.microsite.edit', $event)
            ->with('success', 'Microsite draft saved. The public page is unchanged until you publish it.');
    }

    public function publish(Event $event)
    {
        $this->authorizeMicrositeManagement();

        DB::transaction(function () use ($event) {
            $microsite = EventMicrosite::where('event_id', $event->id)->lockForUpdate()->first();

            if (! $microsite) {
                throw ValidationException::withMessages([
                    'microsite' => 'Save a Microsite draft before publishing it.',
                ]);
            }

            $draft = $microsite->revisions()->where('status', 'DRAFT')->latest('version')->first();

            if (! $draft) {
                throw ValidationException::withMessages([
                    'microsite' => 'Save a Microsite draft before publishing it.',
                ]);
            }

            $microsite->revisions()
                ->where('status', 'PUBLISHED')
                ->update(['status' => 'SUPERSEDED']);

            $draft->update([
                'status' => 'PUBLISHED',
                'published_at' => now(),
            ]);
        });

        return redirect()->route('event.microsite.edit', $event)
            ->with('success', 'Microsite published successfully.');
    }

    public function show(Event $event)
    {
        $microsite = $event->microsite;
        $revision = $microsite
            ? $microsite->revisions()->where('status', 'PUBLISHED')->latest('version')->first()
            : null;

        abort_unless($revision, 404);

        $invitation = $this->verifiedInvitation($event);
        $rundownItems = $this->publicRundownItems($revision);
        $faqItems = $this->publicFaqItems($revision);
        $speakers = $this->storedList($revision->speakers);
        foreach ($speakers as &$speaker) {
            $speaker['photo_url'] = ! empty($speaker['photo_path'])
                ? Storage::disk('public')->url($speaker['photo_path'])
                : null;
        }
        unset($speaker);

        return view('microsite.show', [
            'event' => $event,
            'revision' => $revision,
            'invitation' => $invitation,
            'greeting' => $this->greeting($revision, $invitation),
            'rundownItems' => $rundownItems,
            'faqItems' => $faqItems,
            'speakers' => $speakers,
        ]);
    }

    private function storedList(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $items = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($items) ? $items : [];
    }

    private function deleteSpeakerPhotos(array $paths, Event $event): void
    {
        $microsite = $event->microsite;
        $referencedPaths = [];

        if ($microsite) {
            $revisions = DB::table('event_microsite_revisions')
                ->where('event_microsite_id', $microsite->id)
                ->pluck('speakers');

            foreach ($revisions as $speakersJson) {
                foreach ($this->storedList($speakersJson) as $speaker) {
                    if (! empty($speaker['photo_path'])) {
                        $referencedPaths[$speaker['photo_path']] = true;
                    }
                }
            }
        }

        foreach (array_unique($paths) as $path) {
            if (isset($referencedPaths[$path])) {
                continue;
            }

            if (! Storage::disk('public')->delete($path)) {
                Log::warning('Could not remove an obsolete Microsite speaker photo.', [
                    'path' => $path,
                ]);
            }
        }
    }

    private function editorRundownItems(?EventMicrositeRevision $revision): array
    {
        $items = $this->storedList($revision ? $revision->rundown_items : null);
        if ($items || ! $revision || ! $revision->rundown) {
            return $items;
        }

        foreach (preg_split('/\r\n|\r|\n/', $revision->rundown) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $start = null;
            if (preg_match('/^(\d{1,2}:\d{2})\s*\|\s*(.+)$/', $line, $matches)) {
                $start = $matches[1];
                $line = $matches[2];
            }
            $items[] = ['title' => $line, 'starts_at' => $start, 'ends_at' => null];
        }

        return $items;
    }

    private function editorFaqItems(?EventMicrositeRevision $revision): array
    {
        $items = $this->storedList($revision ? $revision->faq_items : null);
        if ($items || ! $revision || ! $revision->faq) {
            return $items;
        }

        return [['question' => 'General information', 'answer' => $revision->faq]];
    }

    private function publicRundownItems(EventMicrositeRevision $revision): array
    {
        $items = $this->storedList($revision->rundown_items);
        if ($items) {
            return $items;
        }

        foreach (preg_split('/\r\n|\r|\n/', (string) $revision->rundown) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $start = null;
            if (preg_match('/^(\d{1,2}:\d{2})\s*\|\s*(.+)$/', $line, $matches)) {
                $start = $matches[1];
                $line = $matches[2];
            }
            $items[] = ['title' => $line, 'starts_at' => $start, 'ends_at' => null];
        }

        return $items;
    }

    private function publicFaqItems(EventMicrositeRevision $revision): array
    {
        $items = $this->storedList($revision->faq_items);

        return $items ?: ($revision->faq ? [['question' => 'General information', 'answer' => $revision->faq]] : []);
    }

    public function qr(Event $event)
    {
        abort_unless($event->microsite && $event->microsite->publishedRevision, 404);

        $qrCode = new QrCode(
            route('microsite.checkin', $event->slug),
            null,
            new ErrorCorrectionLevelHigh(),
            360,
            12
        );
        $result = (new SvgWriter())->write($qrCode, null, null, [
            SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true,
        ]);

        return response($result->getString(), 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function checkinPage(Event $event)
    {
        $revision = $event->microsite
            ? $event->microsite->publishedRevision
            : null;

        abort_unless($revision, 404);

        $invitation = $this->verifiedInvitation($event);

        return view('microsite.checkin', [
            'event' => $event,
            'revision' => $revision,
            'invitation' => $invitation,
            'greeting' => $this->greeting($revision, $invitation),
            'checkedIn' => $invitation ? $invitation->checkin : null,
        ]);
    }

    public function verifyCode(Request $request, Event $event, EventInvitationService $invitationService)
    {
        abort_unless($event->microsite && $event->microsite->publishedRevision, 404);

        $validated = $request->validate([
            'code' => ['required', 'string', 'regex:/^[A-HJ-NP-Z2-9]{5}$/i'],
            'destination' => ['required', 'in:microsite,check-in'],
        ]);

        $invitation = $invitationService->findByCode($event, $validated['code']);
        if (! $invitation) {
            throw ValidationException::withMessages([
                'code' => 'That family code is not valid for this event.',
            ]);
        }

        $request->session()->put('microsite_verified.' . $event->id, $invitation->id);

        return redirect()->route(
            $validated['destination'] === 'check-in' ? 'microsite.checkin' : 'microsite.show',
            $event->slug
        );
    }

    private function verifiedInvitation(Event $event): ?EventInvitation
    {
        $invitationId = session('microsite_verified.' . $event->id);
        if (! $invitationId) {
            return null;
        }

        $invitation = EventInvitation::with(['registration.fieldAnswers', 'checkin'])
            ->where('event_id', $event->id)
            ->whereKey($invitationId)
            ->first();

        if (! $invitation) {
            session()->forget('microsite_verified.' . $event->id);
        }

        return $invitation;
    }

    private function greeting(EventMicrositeRevision $revision, ?EventInvitation $invitation): array
    {
        if (! $invitation) {
            return ['student' => null, 'parent' => null];
        }

        $answers = $invitation->registration->fieldAnswers->keyBy('event_field_id');

        return [
            'student' => $revision->student_name_field_id
                ? optional($answers->get($revision->student_name_field_id))->value
                : null,
            'parent' => $revision->parent_name_field_id
                ? optional($answers->get($revision->parent_name_field_id))->value
                : null,
        ];
    }

    private function authorizeMicrositeManagement(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }
}
