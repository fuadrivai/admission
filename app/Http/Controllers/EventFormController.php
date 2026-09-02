<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventField;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            'parentFields' => $this->parentFields($event),
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $type = $request->input('type');
        $this->prepareOptions($request, $type);

        $validated = $request->validate([
            'field_key' => ['required', 'string', 'max:100', 'unique:event_fields,field_key,NULL,id,event_id,' . $event->id],
            'label' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:text,textarea,select,radio,checkbox,email,phone,number,date,attachment'],
            'is_required' => ['nullable', 'boolean'],
            'is_primary_email' => ['nullable', 'boolean'],
            'allow_other' => ['nullable', 'boolean'],
            'depends_on_field_id' => ['nullable', 'integer'],
            'options_json' => ['nullable', 'array'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->validateDependencyConfiguration($event, $validated);

        $nextOrderIndex = array_key_exists('order_index', $validated) && $validated['order_index'] !== null
            ? (int) $validated['order_index']
            : ((int) $event->forms()->max('order_index') + 1);

        $event->forms()->create([
            'field_key' => $validated['field_key'],
            'label' => $validated['label'],
            'type' => $validated['type'],
            'is_required' => (bool) ($validated['is_required'] ?? false),
            'is_primary_email' => (bool) ($validated['is_primary_email'] ?? false),
            'allow_other' => in_array($validated['type'], ['select', 'radio', 'checkbox'], true)
                && (bool) ($validated['allow_other'] ?? false),
            'depends_on_field_id' => $validated['type'] === 'select'
                ? ($validated['depends_on_field_id'] ?? null)
                : null,
            'options_json' => $validated['options_json'] ?? null,
            'order_index' => $nextOrderIndex,
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
            'parentFields' => $this->parentFields($event, $eventForm->id),
        ]);
    }

    public function update(Request $request, Event $event, EventField $eventForm)
    {
        $type = $request->input('type');
        $this->prepareOptions($request, $type);

        $validated = $request->validate([
            'field_key' => ['required', 'string', 'max:100', 'unique:event_fields,field_key,' . $eventForm->id . ',id,event_id,' . $event->id],
            'label' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:text,textarea,select,radio,checkbox,email,phone,number,date,attachment'],
            'is_required' => ['nullable', 'boolean'],
            'is_primary_email' => ['nullable', 'boolean'],
            'allow_other' => ['nullable', 'boolean'],
            'depends_on_field_id' => ['nullable', 'integer'],
            'options_json' => ['nullable', 'array'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->validateDependencyConfiguration($event, $validated, $eventForm->id);

        $updateData = [
            'field_key' => $validated['field_key'],
            'label' => $validated['label'],
            'type' => $validated['type'],
            'is_required' => (bool) ($validated['is_required'] ?? false),
            'is_primary_email' => (bool) ($validated['is_primary_email'] ?? false),
            'allow_other' => in_array($validated['type'], ['select', 'radio', 'checkbox'], true)
                && (bool) ($validated['allow_other'] ?? false),
            'depends_on_field_id' => $validated['type'] === 'select'
                ? ($validated['depends_on_field_id'] ?? null)
                : null,
            'options_json' => $validated['options_json'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ];

        if (array_key_exists('order_index', $validated) && $validated['order_index'] !== null) {
            $updateData['order_index'] = (int) $validated['order_index'];
        }

        $eventForm->update($updateData);

        $this->syncDependentMappings($eventForm->fresh());

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

    private function parentFields(Event $event, $exceptId = null)
    {
        return $event->fields()
            ->where('is_active', true)
            ->whereIn('type', ['select', 'radio'])
            ->when($exceptId, function ($query) use ($exceptId) {
                $query->where('id', '!=', $exceptId);
            })
            ->orderBy('order_index')
            ->get();
    }

    private function prepareOptions(Request $request, $type): void
    {
        if ($type === 'select' && $request->filled('depends_on_field_id')) {
            $mapping = $request->input('dependent_options', []);
            $mapping = is_array($mapping) ? $mapping : [];
            $normalized = [];
            foreach ($mapping as $parentValue => $options) {
                $normalized[(string) $parentValue] = array_values(array_filter(array_map(function ($option) {
                    return trim((string) $option);
                }, is_array($options) ? $options : []), function ($option) {
                    return $option !== '';
                }));
            }
            $request->merge(['options_json' => $normalized]);
        } elseif (in_array($type, ['select', 'radio', 'checkbox'], true)) {
            $request->merge(['options_json' => $this->normalizeOptions($request->input('options_json'))]);
        } else {
            $request->merge(['options_json' => null]);
        }
    }

    private function validateDependencyConfiguration(Event $event, array $validated, $currentId = null): void
    {
        $dependsOnId = $validated['depends_on_field_id'] ?? null;
        if ($validated['type'] !== 'select' && $dependsOnId) {
            throw ValidationException::withMessages([
                'depends_on_field_id' => 'Only select fields can have a dependency.',
            ]);
        }

        if (! $dependsOnId) {
            return;
        }

        if ($currentId && (int) $currentId === (int) $dependsOnId) {
            throw ValidationException::withMessages([
                'depends_on_field_id' => 'A field cannot depend on itself.',
            ]);
        }

        $parent = $event->fields()->whereKey($dependsOnId)->where('is_active', true)->first();
        if (! $parent || ! in_array($parent->type, ['select', 'radio'], true)) {
            throw ValidationException::withMessages([
                'depends_on_field_id' => 'The selected parent field is invalid.',
            ]);
        }

        $selectedParent = $parent;
        $visited = $currentId ? [$currentId] : [];
        while ($parent && $parent->depends_on_field_id) {
            if (in_array($parent->depends_on_field_id, $visited, true) || $parent->depends_on_field_id == $currentId) {
                throw ValidationException::withMessages([
                    'depends_on_field_id' => 'Circular dependencies are not allowed.',
                ]);
            }
            $visited[] = $parent->id;
            $parent = $parent->dependsOnField;
        }

        $parentValues = $this->optionValues($selectedParent);
        $mapping = $validated['options_json'] ?? [];
        if (! is_array($mapping)) {
            throw ValidationException::withMessages(['options_json' => 'Dependent options must be a mapping.']);
        }

        foreach ($mapping as $key => $options) {
            if (! in_array((string) $key, $parentValues, true) || ! is_array($options)) {
                throw ValidationException::withMessages([
                    'options_json' => 'Dependent option mappings must match parent options.',
                ]);
            }
            foreach ($options as $option) {
                if (! is_scalar($option) || trim((string) $option) === '') {
                    throw ValidationException::withMessages([
                        'options_json' => 'Dependent options must contain valid values.',
                    ]);
                }
            }
        }
    }

    private function optionValues($field): array
    {
        $options = is_array($field->options_json) ? $field->options_json : [];
        if ($field->depends_on_field_id) {
            $flattened = [];
            foreach ($options as $dependentOptions) {
                foreach ((array) $dependentOptions as $dependentOption) {
                    $flattened[] = $dependentOption;
                }
            }
            $options = $flattened;
        }

        return array_values(array_unique(array_filter(array_map(function ($option) {
            return trim((string) (is_array($option) ? ($option['value'] ?? '') : $option));
        }, $options))));
    }

    private function syncDependentMappings(EventField $parent): void
    {
        $parentValues = $this->optionValues($parent);

        foreach ($parent->dependentFields as $dependent) {
            $mapping = is_array($dependent->options_json) ? $dependent->options_json : [];
            $synced = [];

            foreach ($parentValues as $parentValue) {
                $synced[$parentValue] = is_array($mapping[$parentValue] ?? null)
                    ? array_values($mapping[$parentValue])
                    : [];
            }

            $dependent->update(['options_json' => $synced]);
            $this->syncDependentMappings($dependent->fresh());
        }
    }
}
