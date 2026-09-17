<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlastMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = $this->all();

        if (isset($data['recipients']) && is_string($data['recipients'])) {
            $decodedRecipients = json_decode($data['recipients'], true);
            $data['recipients'] = is_array($decodedRecipients) ? $decodedRecipients : [];
        }

        if (isset($data['attachments']) && is_string($data['attachments'])) {
            $decodedAttachments = json_decode($data['attachments'], true);
            $data['attachments'] = is_array($decodedAttachments) ? $decodedAttachments : [];
        }

        $this->replace($data);
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'recipients' => $this->route('campaign')
                ? ['nullable', 'array']
                : ['required', 'array', 'min:1'],
            'recipients.*.email' => ['required', 'email'],
            'recipients.*.name' => ['nullable', 'string'],
            'recipients.*.data' => ['required', 'array'],
            'attachments' => ['nullable', 'array', 'max:2'],
            'attachments.*' => ['file', 'max:5120'],
        ];
    }
}
