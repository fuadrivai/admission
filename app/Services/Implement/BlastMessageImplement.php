<?php

namespace App\Services\Implement;

use App\Jobs\SendBlastEmail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Services\BlastMessageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;

class BlastMessageImplement implements BlastMessageService
{
    public function get($with = [])
    {
        return EmailCampaign::with($with)->latest()->get();
    }

    public function post($data)
    {
        return $this->createCampaign($data);
    }

    public function createCampaign(array $data): EmailCampaign
    {
        $rawRecipients = $data['recipients'] ?? [];
        $recipients = $this->validateRecipients($rawRecipients);

        if (empty($recipients)) {
            throw ValidationException::withMessages([
                'recipients' => ['At least one valid recipient is required.'],
            ]);
        }

        $attachments = $data['attachments'] ?? [];
        $uploadedPaths = [];

        try {
            $campaign = DB::transaction(function () use ($data, $recipients, $attachments, &$uploadedPaths) {
                $campaign = EmailCampaign::create([
                    'name' => trim((string) ($data['name'] ?? '')),
                    'subject' => trim((string) ($data['subject'] ?? '')),
                    'message' => (string) ($data['message'] ?? ''),
                    'status' => 'READY',
                    'total_recipient' => count($recipients),
                    'created_by' => auth()->id(),
                ]);

                $campaign->recipients()->createMany(array_map(function ($recipient) {
                    return [
                        'email' => strtolower(trim((string) $recipient['email'])),
                        'name' => trim((string) ($recipient['name'] ?? '')) ?: null,
                        'data' => is_array($recipient['data'] ?? null) ? $recipient['data'] : [],
                        'status' => 'PENDING',
                    ];
                }, $recipients));

                $this->storeAttachments($campaign, $attachments, $uploadedPaths);

                return $campaign;
            });

            return $campaign;
        } catch (\Throwable $exception) {
            foreach ($uploadedPaths as $filePath) {
                if (Storage::disk('local')->exists($filePath)) {
                    Storage::disk('local')->delete($filePath);
                }
            }

            throw $exception;
        }
    }

    public function updateCampaign(EmailCampaign $campaign, array $data): EmailCampaign
    {
        $newRecipients = $this->validateRecipients($data['recipients'] ?? []);

        DB::transaction(function () use ($campaign, $data, $newRecipients) {
            $campaign->update([
                'name' => trim((string) ($data['name'] ?? '')),
                'subject' => trim((string) ($data['subject'] ?? '')),
                'message' => (string) ($data['message'] ?? ''),
            ]);

            if (! empty($newRecipients)) {
                $campaign->recipients()->createMany(array_map(function ($recipient) {
                    return [
                        'email' => strtolower(trim((string) $recipient['email'])),
                        'name' => trim((string) ($recipient['name'] ?? '')) ?: null,
                        'data' => is_array($recipient['data'] ?? null) ? $recipient['data'] : [],
                        'status' => 'PENDING',
                    ];
                }, $newRecipients));
            }

            $campaign->update([
                'total_recipient' => $campaign->recipients()->count(),
                'status' => $campaign->recipients()->whereIn('status', ['PENDING', 'PROCESSING'])->exists()
                    ? 'PROCESSING'
                    : $campaign->status,
            ]);

            if ($campaign->attachments()->count() + count($data['attachments'] ?? []) > 2) {
                throw ValidationException::withMessages([
                    'attachments' => ['A campaign can contain at most 2 attachments.'],
                ]);
            }

            $this->storeAttachments($campaign, $data['attachments'] ?? []);
        });

        return $campaign->fresh(['recipients', 'attachments']);
    }

    public function sendCampaign(EmailCampaign $campaign): void
    {
        if ($campaign->total_recipient <= 0) {
            throw ValidationException::withMessages([
                'campaign' => ['Campaign has no valid recipients to send.'],
            ]);
        }

        $campaign->update(['status' => 'QUEUED']);

        $campaign->recipients()
            ->where('status', 'PENDING')
            ->get()
            ->each(function (EmailCampaignRecipient $recipient) {
                SendBlastEmail::dispatch($recipient->id)->afterResponse();
            });
    }

    public function renderVariables(string $content, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($matches) use ($data) {
            $key = $matches[1];

            if (! array_key_exists($key, $data)) {
                return '';
            }

            return e((string) $data[$key]);
        }, $content);
    }

    public function validateRecipients(array $recipients): array
    {
        $validRecipients = [];

        foreach ($recipients as $recipient) {
            if (! is_array($recipient)) {
                continue;
            }

            $email = strtolower(trim((string) ($recipient['email'] ?? '')));
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $data = $recipient['data'] ?? [];
            if (! is_array($data)) {
                $data = [];
            }

            $validRecipients[] = [
                'email' => $email,
                'name' => trim((string) ($recipient['name'] ?? '')) ?: null,
                'data' => $data,
            ];

        }

        return $validRecipients;
    }

    public function storeAttachments(EmailCampaign $campaign, array $attachments, array &$uploadedPaths = []): void
    {
        if (count($attachments) > 2) {
            throw ValidationException::withMessages([
                'attachments' => ['A campaign can contain at most 2 attachments.'],
            ]);
        }

        foreach ($attachments as $attachment) {
            $file = $attachment instanceof UploadedFile ? $attachment : ($attachment['file'] ?? null);
            if (! $file instanceof UploadedFile) {
                continue;
            }

            if ($file->getSize() > 5 * 1024 * 1024) {
                throw ValidationException::withMessages([
                    'attachments' => ['Each attachment must be 5 MB or smaller.'],
                ]);
            }

            $originalName = $file->getClientOriginalName() ?: $file->getFilename();
            $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
            $storedPath = Storage::disk('local')->putFileAs('email-campaigns/' . $campaign->id, $file, $fileName);
            $uploadedPaths[] = $storedPath;

            $campaign->attachments()->create([
                'original_name' => $originalName,
                'file_name' => $fileName,
                'file_path' => $storedPath,
                'mime_type' => $file->getMimeType() ?: null,
                'file_size' => $file->getSize(),
            ]);
        }
    }

    public function recipientQuery(EmailCampaign $campaign): Builder
    {
        return $campaign->recipients()->select([
            'id', 'campaign_id', 'name', 'email', 'status', 'sent_at', 'error_message',
        ])->getQuery();
    }

    public function renderRecipientMessage(EmailCampaignRecipient $recipient): string
    {
        $recipient->loadMissing(['campaign.attachments']);
        $campaign = $recipient->campaign;
        $renderedMessage = $this->renderVariables($campaign->message, $recipient->data ?: []);
        $subject = $this->renderVariables($campaign->subject, $recipient->data ?: []);

        return view('email-template.event-registration-template', [
            'campaign' => $campaign,
            'recipient' => $recipient,
            'email_body' => $renderedMessage,
            'data' => [
                'title' => $subject,
                'content' => $renderedMessage,
            ],
        ])->render();
    }

    public function updateCampaignContent(EmailCampaign $campaign, array $data): EmailCampaign
    {
        $newRecipients = $this->validateRecipients($data['recipients'] ?? []);
        $removeIds = array_values(array_filter(array_map('intval', $data['remove_attachments'] ?? [])));
        $attachments = $data['attachments'] ?? [];
        $existingAttachments = $campaign->attachments()->get();
        $remainingCount = $existingAttachments->whereNotIn('id', $removeIds)->count();

        if ($remainingCount + count($attachments) > 2) {
            throw ValidationException::withMessages([
                'attachments' => ['A campaign can contain at most 2 attachments.'],
            ]);
        }

        foreach ($existingAttachments->whereIn('id', $removeIds) as $attachment) {
            if ($attachment->file_path && Storage::disk('local')->exists($attachment->file_path)) {
                Storage::disk('local')->delete($attachment->file_path);
            }
        }

        DB::transaction(function () use ($campaign, $data, $newRecipients, $removeIds, $attachments) {
            $campaign->update([
                'subject' => trim((string) ($data['subject'] ?? '')),
                'message' => (string) ($data['message'] ?? ''),
            ]);

            if (! empty($newRecipients)) {
                $campaign->recipients()->createMany(array_map(function ($recipient) {
                    return [
                        'email' => strtolower(trim((string) $recipient['email'])),
                        'name' => trim((string) ($recipient['name'] ?? '')) ?: null,
                        'data' => is_array($recipient['data'] ?? null) ? $recipient['data'] : [],
                        'status' => 'PENDING',
                    ];
                }, $newRecipients));
            }

            $campaign->update([
                'total_recipient' => $campaign->recipients()->count(),
                'status' => ! empty($newRecipients) ? 'PROCESSING' : $campaign->status,
            ]);

            if ($removeIds) {
                $campaign->attachments()->whereIn('id', $removeIds)->delete();
            }

            $this->storeAttachments($campaign, $attachments);
        });

        return $campaign->fresh(['attachments']);
    }

    public function resendRecipient(EmailCampaignRecipient $recipient): void
    {
        $recipient->update([
            'status' => 'PENDING',
            'sent_at' => null,
            'error_message' => null,
        ]);

        SendBlastEmail::dispatch($recipient->id)->afterResponse();
    }
}
