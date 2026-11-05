<?php

namespace App\Http\Controllers;

use App\Services\MerchandiseService;
use Illuminate\Http\Request;

class MerchandiseEmailTemplateController extends Controller
{
    private MerchandiseService $merchandiseService;

    public function __construct(MerchandiseService $merchandiseService)
    {
        $this->merchandiseService = $merchandiseService;
    }

    public function index()
    {
        $this->ensureAdmin();

        return view('merchandise.email-templates.index', [
            'title' => 'Merchandise Email Templates',
            'templates' => $this->merchandiseService->getEmailTemplates(),
        ]);
    }

    public function edit(string $key)
    {
        $this->ensureAdmin();
        $template = $this->merchandiseService->getEmailTemplates()->firstWhere('key', $key);
        abort_unless($template, 404);

        return view('merchandise.email-templates.edit', [
            'title' => 'Edit ' . $template->name . ' Email',
            'template' => $template,
        ]);
    }

    public function update(Request $request, string $key)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:100000'],
        ]);

        $this->merchandiseService->updateEmailTemplate($key, $data);

        return redirect()->route('merchandise.email-templates')
            ->with('success', 'Email template saved successfully.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }
}
