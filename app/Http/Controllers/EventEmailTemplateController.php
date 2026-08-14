<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventEmailTemplate;
use Illuminate\Http\Request;

class EventEmailTemplateController extends Controller
{
    public function index(Event $event)
    {
        return view('event.email-templates.index', [
            'title' => 'Email Templates',
            'event' => $event,
            'templates' => $event->emailTemplates()->get(),
        ]);
    }

    public function create(Event $event)
    {
        return view('event.email-templates.form', [
            'title' => 'Create Email Template',
            'event' => $event,
            'template' => null,
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', 'unique:event_email_templates,key,NULL,id,event_id,' . $event->id],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $event->emailTemplates()->create($validated);

        return redirect()->route('event.email-templates.index', $event)->with('success', 'Email template created successfully.');
    }

    public function edit(Event $event, EventEmailTemplate $eventFormEmailTemplate)
    {
        return view('event.email-templates.form', [
            'title' => 'Edit Email Template',
            'event' => $event,
            'template' => $eventFormEmailTemplate,
        ]);
    }

    public function update(Request $request, Event $event, EventEmailTemplate $eventFormEmailTemplate)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', 'unique:event_email_templates,key,' . $eventFormEmailTemplate->id . ',id,event_id,' . $event->id],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $eventFormEmailTemplate->update($validated);

        return redirect()->route('event.email-templates.index', $event)->with('success', 'Email template updated successfully.');
    }

    public function destroy(Event $event, EventEmailTemplate $eventFormEmailTemplate)
    {
        $eventFormEmailTemplate->delete();

        return redirect()->route('event.email-templates.index', $event)->with('success', 'Email template deleted successfully.');
    }
}
