<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BlastMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $recipient;
    public $renderedMessage;
    public $subjectTitle;
    public $sender;

    public function __construct(EmailCampaign $campaign, EmailCampaignRecipient $recipient, string $renderedMessage, string $subjectTitle, EmailSetting $sender)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
        $this->renderedMessage = $renderedMessage;
        $this->subjectTitle = $subjectTitle;
        $this->sender = $sender;
    }

    public function build()
    {
        $mail = $this->from($this->sender->from_address, $this->sender->from_name)
            ->subject($this->subjectTitle)
            ->view('email-template.event-registration-template', [
                'campaign' => $this->campaign,
                'recipient' => $this->recipient,
                'email_body' => $this->renderedMessage,
                'data' => [
                    'title' => $this->subjectTitle,
                    'content' => $this->renderedMessage,
                ],
            ]);

        foreach ($this->campaign->attachments as $attachment) {
            $path = $attachment->file_path;

            if (! Storage::disk('local')->exists($path)) {
                continue;
            }

            $mail->attach(Storage::disk('local')->path($path), [
                'as' => $attachment->original_name,
                'mime' => $attachment->mime_type ?: 'application/octet-stream',
            ]);
        }

        return $mail;
    }
}
