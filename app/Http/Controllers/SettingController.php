<?php

namespace App\Http\Controllers;

use App\Services\BranchService;
use App\Services\EmailSettingService;
use App\Services\WhatsappCodeService;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    private BranchService $branchService;
    private WhatsappCodeService $whatsappCodeService;
    private EmailSettingService $emailSettingService;

    public function __construct(BranchService $branchService,WhatsappCodeService $whatsappCodeService,EmailSettingService $emailSettingService)
    {
        $this->branchService = $branchService;
        $this->whatsappCodeService = $whatsappCodeService;
        $this->emailSettingService = $emailSettingService;
    }
    public function index()
    {
        $branches = $this->branchService->get();
        $waCodes = $this->whatsappCodeService->get(['branch']);
        $emails = $this->emailSettingService->get(['branch']);
        return view('setting.general',['title'=>'General Setting','waCodes'=>$waCodes,'emails'=>$emails, 'branches'=>$branches]);
    }

    public function postWA(Request $request)
    {
         $validate = $request->validate([
            'branch_id'=>'required',
            'code' => 'required',
            'description' => 'nullable|string',
        ]);
        $this->whatsappCodeService->post($validate);
        return redirect()
        ->back()
        ->with('whatsapp-success', 'Whatsapp Code has been saved successfully.');
    }
    public function putWA(Request $request)
    {
         $validate = $request->validate([
            'wa_id'=>'required',
            'branch_id'=>'required',
            'code' => 'required',
            'description' => 'nullable|string',
        ]);
        $this->whatsappCodeService->put($validate);
        return redirect()
        ->back()
        ->with('whatsapp-success', 'Whatsapp Code has been updated successfully.');
    }

    public function emailCreateForm()
    {
        $branches = $this->branchService->get();
        return view('setting.email-form',['title'=>'Email Form','branches'=>$branches]);
    }
    public function emailEditForm($id)
    {
        $branches = $this->branchService->get();
        $email = $this->emailSettingService->show($id,['branch']);
        return view('setting.email-form',['title'=>'Email Form','email'=>$email, 'branches'=>$branches]);
    }

    public function postEmail(Request $request)
    {
         $validate = $request->validate([
            'branch'=>'required',
            'username' => 'required',
            'from_name' => 'required',
            'from_address' => 'required',
            'password' => 'required',
            'mailer' => 'required',
            'host' => 'required',
            'port' => 'required',
            'encryption' => 'required',
        ]);
        $this->emailSettingService->post($validate);
        return redirect()
        ->back()
        ->with('email-success', 'email has been saved successfully.');
    }
    public function putEmail(Request $request)
    {
         $validate = $request->validate([
            'email_id'=>'required',
            'branch'=>'required',
            'username' => 'required',
            'from_name' => 'required',
            'from_address' => 'required',
            'password' => 'required',
            'mailer' => 'required',
            'host' => 'required',
            'port' => 'required',
            'encryption' => 'required',
        ]);
        $this->emailSettingService->put($validate);
        return redirect()
        ->back()
        ->with('email-success', 'email has been updated successfully.');
    }
}
