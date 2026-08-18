<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventField;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class EventFormController extends Controller
{
    private function normalizeOptions($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $value = array_values($value);
        } else {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = preg_split('/\r\n|\r|\n/', (string) $value);
            }
        }

        $cleaned = array_values(array_filter(array_map(function ($item) {
            return trim((string) $item);
        }, (array) $value), function ($item) {
            return $item !== '';
        }));

        return $cleaned ?: null;
    }

    public function index(Event $event)
    {
        return view('event.forms.index', [
            'title' => 'Event Form Builder',
            'event' => $event,
            'forms' => $event->forms()->orderBy('order_index')->get(),
        ]);
    }

    public function create(Event $event)
    {
        return view('event.forms.form', [
            'title' => 'Create Form Field',
            'event' => $event,
            'formField' => null,
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $type = $request->input('type');
        if (in_array($type, ['select', 'radio', 'checkbox'], true)) {
            $request->merge([
                'options_json' => $this->normalizeOptions($request->input('options_json')),
            ]);
        } else {
            $request->merge([
                'options_json' => null,
            ]);
        }

        $validated = $request->validate([
            'field_key' => ['required', 'string', 'max:100', 'unique:event_fields,field_key,NULL,id,event_id,' . $event->id],
            'label' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:text,textarea,select,radio,checkbox,email,phone,number,date'],
            'is_required' => ['nullable', 'boolean'],
            'is_primary_email' => ['nullable', 'boolean'],
            'options_json' => ['nullable', 'array'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $event->forms()->create([
            'field_key' => $validated['field_key'],
            'label' => $validated['label'],
            'type' => $validated['type'],
            'is_required' => (bool) ($validated['is_required'] ?? false),
            'is_primary_email' => (bool) ($validated['is_primary_email'] ?? false),
            'options_json' => $validated['options_json'] ?? null,
            'order_index' => $validated['order_index'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('event.forms.index', $event)->with('success', 'Form field created successfully.');
    }

    public function edit(Event $event, EventField $eventForm)
    {
        return view('event.forms.form', [
            'title' => 'Edit Form Field',
            'event' => $event,
            'formField' => $eventForm,
        ]);
    }

    public function update(Request $request, Event $event, EventField $eventForm)
    {
        $type = $request->input('type');
        if (in_array($type, ['select', 'radio', 'checkbox'], true)) {
            $request->merge([
                'options_json' => $this->normalizeOptions($request->input('options_json')),
            ]);
        } else {
            $request->merge([
                'options_json' => null,
            ]);
        }

        $validated = $request->validate([
            'field_key' => ['required', 'string', 'max:100', 'unique:event_fields,field_key,' . $eventForm->id . ',id,event_id,' . $event->id],
            'label' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:text,textarea,select,radio,checkbox,email,phone,number,date'],
            'is_required' => ['nullable', 'boolean'],
            'is_primary_email' => ['nullable', 'boolean'],
            'options_json' => ['nullable', 'array'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $eventForm->update([
            'field_key' => $validated['field_key'],
            'label' => $validated['label'],
            'type' => $validated['type'],
            'is_required' => (bool) ($validated['is_required'] ?? false),
            'is_primary_email' => (bool) ($validated['is_primary_email'] ?? false),
            'options_json' => $validated['options_json'] ?? null,
            'order_index' => $validated['order_index'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('event.forms.index', $event)->with('success', 'Form field updated successfully.');
    }

    public function destroy(Event $event, EventField $eventForm)
    {
        $eventForm->delete();

        return redirect()->route('event.forms.index', $event)->with('success', 'Form field deleted successfully.');
    }

    public function reorder(Request $request, Event $event)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $fieldId) {
            $event->forms()->whereKey($fieldId)->update(['order_index' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function datatables(UtilitiesRequest $request, Event $event)
    {
        $query = $event->forms()->orderBy('order_index');

        if ($request->ajax()) {
            return datatables()->of($query)
                ->addColumn('type', fn ($row) => ucfirst($row->type))
                ->addColumn('required', fn ($row) => $row->is_required ? 'Yes' : 'No')
                ->addColumn('action', function ($row) use ($event) {
                    return '<a href="' . route('event.forms.edit', [$event, $row]) . '" class="btn btn-sm btn-primary">Edit</a>'
                        . ' <form method="POST" action="' . route('event.forms.destroy', [$event, $row]) . '" style="display:inline;">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this field?\');">Delete</button>'
                        . '</form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json([]);
    }

    public function share(Event $event, EventField $eventForm)
    {
        return response()->json([
            'event_id' => $event->id,
            'field_id' => $eventForm->id,
            'share_url' => route('event.forms.public', [$event, $eventForm]),
        ]);
    }

    public function public(Event $event, EventField $eventForm)
    {
        return view('event.forms.public', [
            'title' => 'Public Form Preview',
            'event' => $event,
            'formField' => $eventForm,
        ]);
    }
}
