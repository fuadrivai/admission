<?php

namespace App\Services\Implement;

use App\Models\SchoolVisitMessage;
use App\Models\WhatsappCode;
use App\Services\SchoolVisitMessageService;

class SchoolVisitMessageImplement implements SchoolVisitMessageService
{
    public function get($with=[])
    {
        return SchoolVisitMessage::with($with)->get();
    }

    public function show($id,$with=[])
    {
        return SchoolVisitMessage::with($with)->findOrFail($id);
    }

    public function post($data)
    {
        $message = SchoolVisitMessage::create([
            'branch_id'     => $data['branch_id'],
            'whatsapp_message'     => $data['whatsapp_message'],
            'email_message'     => $data['email_message'],
        ]);

        return $message;
    }

    public function put($data)
    {
        $message = SchoolVisitMessage::findOrFail($data['id']);

        $message->update([
            'branch_id'     => $data['branch_id'],
            'whatsapp_message'     => $data['whatsapp_message'],
            'email_message'     => $data['email_message'],
        ]);

        return $message;
    }

    public function delete($id)
    {
        $whatsapp = WhatsappCode::findOrFail($id);
        return $whatsapp->delete();
    }
}
