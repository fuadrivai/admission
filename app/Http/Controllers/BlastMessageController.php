<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlastMessageRequest;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignAttachment;
use App\Models\EmailCampaignRecipient;
use App\Services\BlastMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlastMessageController extends Controller
{
    protected BlastMessageService $blastMessageService;

    public function __construct(BlastMessageService $blastMessageService)
    {
        $this->blastMessageService = $blastMessageService;
    }

    public function index()
    {
        return view('blast-message.email.index', ['title' => 'Blast Email']);
    }

    public function datatables(Request $request)
    {
        $query = EmailCampaign::query()->with('recipients:id,campaign_id,email')->withCount('recipients');
        $total = (clone $query)->count();

        if ($search = trim((string) $request->input('search.value'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        $filtered = (clone $query)->count();
        $orderColumns = ['name', 'subject', 'recipients_count', 'status', 'created_at'];
        $orderColumn = $orderColumns[(int) $request->input('order.0.column', 4)] ?? 'created_at';
        $direction = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', 10);

        $campaigns = $query->orderBy($orderColumn, $direction)
            ->skip($start)
            ->take($length > 0 ? $length : 10)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $campaigns->map(function (EmailCampaign $campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'subject' => $campaign->subject,
                    'total_recipient' => $campaign->total_recipient ?? $campaign->recipients_count,
                    'status' => ucfirst(strtolower((string) $campaign->status)),
                    'created_at' => $campaign->created_at ? $campaign->created_at->format('d M Y H:i') : null,
                    'emails' => $campaign->recipients->pluck('email')->values(),
                ];
            })->values(),
        ]);
    }

    public function show(EmailCampaign $campaign)
    {
        $campaign->load(['recipients', 'attachments']);

        return response()->json([
            'success' => true,
            'campaign' => $campaign,
        ]);
    }

    public function create(Request $request)
    {
        $cloneCampaign = null;

        if ($request->filled('copy_from')) {
            $cloneCampaign = EmailCampaign::with(['recipients', 'attachments'])
                ->findOrFail((int) $request->input('copy_from'));
        }

        return view('blast-message.email.form', [
            'title' => $cloneCampaign ? 'Create email campaign copy' : 'Create new email form',
            'cloneCampaign' => $cloneCampaign,
        ]);
    }

    public function recipients(EmailCampaign $campaign)
    {
        $campaign->load('attachments');

        return view('blast-message.email.recipients', [
            'title' => 'Email Campaign Recipients',
            'campaign' => $campaign,
            'summary' => [
                'total' => $campaign->recipients()->count(),
                'sent' => $campaign->recipients()->where('status', 'SENT')->count(),
                'failed' => $campaign->recipients()->where('status', 'FAILED')->count(),
                'pending' => $campaign->recipients()->whereIn('status', ['PENDING', 'PROCESSING'])->count(),
            ],
        ]);
    }

    public function recipientDatatables(Request $request, EmailCampaign $campaign)
    {
        $query = $this->blastMessageService->recipientQuery($campaign);
        $total = (clone $query)->count();

        if ($search = trim((string) $request->input('search.value'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        $filtered = (clone $query)->count();
        $orderColumns = ['name', 'email', 'status', 'sent_at'];
        $orderColumn = $orderColumns[(int) $request->input('order.0.column', 3)] ?? 'sent_at';
        $direction = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', 10);

        $recipients = $query->orderBy($orderColumn, $direction)
            ->skip($start)
            ->take($length > 0 ? $length : 10)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $recipients->map(function (EmailCampaignRecipient $recipient) {
                return [
                    'id' => $recipient->id,
                    'name' => $recipient->name,
                    'email' => $recipient->email,
                    'status' => $recipient->status,
                    'sent_at' => $recipient->sent_at ? $recipient->sent_at->format('d M Y H:i') : null,
                    'error_message' => $recipient->error_message,
                ];
            })->values(),
        ]);
    }

    public function recipientMessage(EmailCampaign $campaign, EmailCampaignRecipient $recipient)
    {
        abort_unless((int) $recipient->campaign_id === (int) $campaign->id, 404);
        $recipient->load(['campaign.attachments']);

        return response()->json([
            'success' => true,
            'recipient' => [
                'id' => $recipient->id,
                'name' => $recipient->name,
                'email' => $recipient->email,
                'data' => $recipient->data ?: [],
                'status' => $recipient->status,
                'sent_at' => $recipient->sent_at ? $recipient->sent_at->format('d M Y H:i') : null,
                'error_message' => $recipient->error_message,
            ],
            'message' => $this->blastMessageService->renderRecipientMessage($recipient),
            'body' => $this->blastMessageService->renderVariables($recipient->campaign->message, $recipient->data ?: []),
            'attachments' => $recipient->campaign->attachments->map(function (EmailCampaignAttachment $attachment) use ($campaign) {
                return [
                    'id' => $attachment->id,
                    'original_name' => $attachment->original_name,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                    'url' => route('email-campaigns.attachments.show', [$campaign, $attachment]),
                ];
            })->values(),
        ]);
    }

    public function updateMessage(Request $request, EmailCampaign $campaign)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'recipients' => ['nullable', 'array'],
            'recipients.*.email' => ['required_with:recipients', 'email'],
            'recipients.*.name' => ['nullable', 'string'],
            'recipients.*.data' => ['required_with:recipients', 'array'],
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['integer'],
            'attachments' => ['nullable', 'array', 'max:2'],
            'attachments.*' => ['file', 'max:5120'],
        ]);
        $validated['attachments'] = $request->file('attachments', []);

        $updatedCampaign = $this->blastMessageService->updateCampaignContent($campaign, $validated);

        return response()->json([
            'success' => true,
            'campaign' => $updatedCampaign,
            'message' => 'Campaign message updated successfully.',
        ]);
    }

    public function resendRecipient(EmailCampaign $campaign, EmailCampaignRecipient $recipient)
    {
        abort_unless((int) $recipient->campaign_id === (int) $campaign->id, 404);
        $this->blastMessageService->resendRecipient($recipient);

        return response()->json([
            'success' => true,
            'message' => 'Email has been queued for sending.',
        ]);
    }

    public function attachment(EmailCampaign $campaign, EmailCampaignAttachment $attachment)
    {
        abort_unless((int) $attachment->campaign_id === (int) $campaign->id, 404);

        if (! $attachment->file_path || ! Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($attachment->file_path), [
            'Content-Disposition' => 'inline; filename="' . addslashes($attachment->original_name) . '"',
        ]);
    }

    public function edit(EmailCampaign $campaign)
    {
        $campaign->load(['recipients', 'attachments']);

        return view('blast-message.email.form', ['title' => 'Edit email campaign', 'campaign' => $campaign]);
    }

    public function store(StoreBlastMessageRequest $request)
    {
        $campaign = $this->blastMessageService->createCampaign($request->validated());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'campaign' => $campaign,
                'message' => 'Email campaign created successfully.',
            ]);
        }

        return redirect()->route('email-campaigns.index')->with('success', 'Email campaign created successfully.');
    }

    public function update(StoreBlastMessageRequest $request, EmailCampaign $campaign)
    {
        $campaign = $this->blastMessageService->updateCampaign($campaign, $request->validated());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'campaign' => $campaign,
                'message' => 'Email campaign updated successfully.',
            ]);
        }

        return redirect()->route('email-campaigns.index')->with('success', 'Email campaign updated successfully.');
    }

    public function send(Request $request, EmailCampaign $campaign)
    {
        $this->blastMessageService->sendCampaign($campaign);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'campaign' => $campaign->fresh(),
                'message' => 'Campaign has been queued for sending.',
            ]);
        }

        return redirect()->back()->with('success', 'Campaign has been queued for sending.');
    }

    public function destroy(Request $request, EmailCampaign $campaign)
    {
        $campaign->load(['recipients', 'attachments']);

        foreach ($campaign->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('local')->exists($attachment->file_path)) {
                Storage::disk('local')->delete($attachment->file_path);
            }
        }

        Storage::disk('local')->deleteDirectory('email-campaigns/' . $campaign->id);
        $campaign->recipients()->delete();
        $campaign->attachments()->delete();
        $campaign->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Campaign deleted successfully.',
            ]);
        }

        return redirect()->route('email-campaigns.index')->with('success', 'Campaign deleted successfully.');
    }

    public function email()
    {
        $campaigns = $this->blastMessageService->get(['recipients', 'attachments']);

        return view('blast-message.email.index', ['title' => 'Blast Email', 'campaigns' => $campaigns]);
    }

    public function emailForm()
    {
        return view('blast-message.email.form', ['title' => 'Create new email form']);
    }

    public function whatsapp()
    {
        return view('blast-message.whatsapp.index', ['title' => 'Blast WhatsApp']);
    }
}
