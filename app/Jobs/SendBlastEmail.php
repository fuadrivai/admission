<?php

namespace App\Jobs;

use App\Mail\BlastMessageMail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\EmailSetting;
use App\Services\BlastMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendBlastEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 120, 300];

    public $recipientId;

    public function __construct($recipientId)
    {
        $this->recipientId = $recipientId;
    }

    public function handle(BlastMessageService $blastMessageService)
{
    $recipient = EmailCampaignRecipient::with([
        'campaign.attachments'
    ])->find($this->recipientId);

    if (! $recipient) {
        return;
    }

    if ($recipient->status === 'SENT') {
        return;
    }

    $campaign = $recipient->campaign;

    if (! $campaign) {
        return;
    }

    $recipient->update([
        'status' => 'PROCESSING',
        'error_message' => null,
    ]);

    try {
        $renderedMessage = $blastMessageService->renderVariables(
            $campaign->message,
            $recipient->data ?? []
        );

        $subject = $blastMessageService->renderVariables(
            $campaign->subject,
            $recipient->data ?? []
        );

        $setting = EmailSetting::where(
            'from_address',
            config('mail.from.address')
        )->first();

        if (! $setting) {
            throw new \RuntimeException(
                'No email sender configuration found for ' . config('mail.from.address') . '.'
            );
        }

        $mail = new BlastMessageMail(
            $campaign,
            $recipient,
            $renderedMessage,
            $subject,
            $setting
        );

        Mail::to($recipient->email)->send($mail);

        $recipient->update([
            'status' => 'SENT',
            'sent_at' => now(),
            'error_message' => null,
        ]);

        $campaign->increment('total_sent');

    } catch (\Throwable $e) {

        // Jangan langsung increment total_failed.
        $recipient->update([
            'error_message' => $e->getMessage(),
        ]);

        throw $e;

    } finally {

        $this->updateCampaignStatus($campaign);
    }
}

public function failed(\Throwable $exception)
{
    $recipient = EmailCampaignRecipient::with('campaign')
        ->find($this->recipientId);

    if (! $recipient) {
        return;
    }

    $campaign = $recipient->campaign;

    if (! $campaign) {
        return;
    }

    if ($recipient->status !== 'SENT') {

        $recipient->update([
            'status' => 'FAILED',
            'error_message' => $exception->getMessage(),
        ]);

        $campaign->increment('total_failed');
    }

    $this->updateCampaignStatus($campaign);
}

    protected function updateCampaignStatus(EmailCampaign $campaign): void
    {
        $campaign->refresh();

        $allRecipients = $campaign->recipients()->count();
        $sentCount = $campaign->recipients()->where('status', 'SENT')->count();
        $failedCount = $campaign->recipients()->where('status', 'FAILED')->count();
        $pendingOrProcessingCount = $campaign->recipients()->whereIn('status', ['PENDING', 'PROCESSING'])->count();

        if ($pendingOrProcessingCount > 0) {
            $campaign->status = 'PROCESSING';
        } elseif ($allRecipients > 0 && $failedCount > 0 && $sentCount > 0) {
            $campaign->status = 'FAILED';
        } elseif ($allRecipients > 0 && $failedCount === $allRecipients) {
            $campaign->status = 'FAILED';
        } elseif ($allRecipients > 0 && $sentCount === $allRecipients) {
            $campaign->status = 'COMPLETED';
        } elseif ($allRecipients === 0) {
            $campaign->status = 'READY';
        } else {
            $campaign->status = 'PROCESSING';
        }

        $campaign->save();
    }
}
