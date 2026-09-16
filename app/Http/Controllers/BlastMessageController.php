<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlastMessageController extends Controller
{
    public function email()
    {
        return view('blast-message.email.index',["title" => 'Blast Email']);
    }
    public function emailForm()
    {
        return view('blast-message.email.form',["title" => 'Create new email form']);
    }

    public function whatsapp()
    {
        return view('blast-message.whatsapp.index',["title" => 'Blast WhatsApp']);
    }
}
