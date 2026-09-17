<?php

namespace App\Services;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use Illuminate\Database\Eloquent\Builder;

interface BlastMessageService
{
    public function get($with = []);
    public function post($data);
    public function createCampaign(array $data): EmailCampaign;
    public function updateCampaign(EmailCampaign $campaign, array $data): EmailCampaign;
    public function sendCampaign(EmailCampaign $campaign): void;
    public function renderVariables(string $content, array $data): string;
    public function validateRecipients(array $recipients): array;
    public function storeAttachments(EmailCampaign $campaign, array $attachments): void;
    public function recipientQuery(EmailCampaign $campaign): Builder;
    public function renderRecipientMessage(EmailCampaignRecipient $recipient): string;
    public function updateCampaignContent(EmailCampaign $campaign, array $data): EmailCampaign;
    public function resendRecipient(EmailCampaignRecipient $recipient): void;
}
