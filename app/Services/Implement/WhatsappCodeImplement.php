<?php

namespace App\Services\Implement;

use App\Models\WhatsappCode;
use App\Services\WhatsappCodeService;

class WhatsappCodeImplement implements WhatsappCodeService
{
    public function get($with=[])
    {
        return WhatsappCode::with($with)->get();
    }

    public function show($id,$with=[])
    {
        return WhatsappCode::with($with)->findOrFail($id);
    }

    public function post($data)
    {
        $whatsapp = WhatsappCode::create([
            'code'     => $data['code'],
            'branch_id'     => $data['branch_id'],
            'description'     => $data['description'],
        ]);

        return $whatsapp;
    }

    public function put($data)
    {
        $whatsapp = WhatsappCode::findOrFail($data['wa_id']);

        $whatsapp->update([
            'code'     => $data['code'],
            'branch_id'     => $data['branch_id'],
            'description'     => $data['description'],
        ]);

        return $whatsapp;
    }

    public function delete($id)
    {
        $whatsapp = WhatsappCode::findOrFail($id);
        return $whatsapp->delete();
    }
}
