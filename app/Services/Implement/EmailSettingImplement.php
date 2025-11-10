<?php

namespace App\Services\Implement;

use App\Models\EmailSetting;
use App\Services\EmailSettingService;

class EmailSettingImplement implements EmailSettingService
{
    public function get($with = [])
    {
        return EmailSetting::with($with)->get();
    }

    public function show($id,$with=[])
    {
        return EmailSetting::with($with)->findOrFail($id);
    }

    public function post($data)
    {
        $email = EmailSetting::create([
            'username'         => $data['username'],
            'from_address'     => $data['from_address'],
            'from_name'        => $data['from_name'],
            'app_password'     => $data['password'],
            'mailer'           => $data['mailer'],
            'host'             => $data['host'],
            'port'             => $data['port'],
            'encryption'       => $data['encryption'],
            'branch_id'        => $data['branch'],
        ]);

        return $email;
    }

    public function put($data)
    {
        $email = EmailSetting::findOrFail($data['email_id']);

        $email->update([
            'username'         => $data['username'],
            'from_address'     => $data['from_address'],
            'from_name'        => $data['from_name'],
            'app_password'     => $data['password'],
            'mailer'           => $data['mailer'],
            'host'             => $data['host'],
            'port'             => $data['port'],
            'encryption'        => $data['encryption'],
            'branch_id'        => $data['branch'],
        ]);

        return $email;
    }

    public function delete($id)
    {
        $email = EmailSetting::findOrFail($id);
        return $email->delete();
    }
}
