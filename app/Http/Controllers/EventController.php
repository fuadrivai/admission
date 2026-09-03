<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Event;
use App\Models\EventFieldAnswer;
use App\Models\EventRegistration;
use App\Exports\EventRegistrationExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class EventController extends Controller
{
    public function index()
    {
        $query = Event::with('branch')->withCount('registrations')->orderByDesc('id');

        $status = request('status', 'PUBLISHED');

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (request()->filled('availability_type') && request('availability_type') !== 'all') {
            $query->where('availability_type', request('availability_type'));
        }

        if (request()->filled('branch_id') && request('branch_id') !== 'all') {
            $query->where('branch_id', request('branch_id'));
        }

        $events = $query->paginate(10)->withQueryString();

        if (request()->ajax()) {
            return view('event._list', compact('events'));
        }

        $branches = Branch::orderBy('name')->get();

        return view('event.index', compact('events', 'branches'), [
            'title' => 'Events List',
        ]);
    }

    public function datatables(UtilitiesRequest $request)
    {
        $query = Event::with('branch')->withCount('registrations')->orderByDesc('id');

        if ($request->ajax()) {
            return datatables()->of($query)
                ->editColumn('title', function ($row) {
                    $publicUrl = route('events.show', $row);

                    return '<div class="event-title-cell">' . e($row->title) . '</div>'
                        . '<small class="event-public-url"><a href="' . e($publicUrl) . '" target="_blank">'
                        . e($publicUrl) . '</a></small>';
                })
                ->addColumn('branch_name', function ($row) {
                    return $row->branch ? $row->branch->name : '--';
                })
                ->addColumn('status', function ($row) {
                    $statusColors = [
                        'DRAFT' => 'secondary',
                        'PUBLISHED' => 'success',
                        'CLOSED' => 'danger',
                    ];
                    $color = $statusColors[$row->status] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $row->status . '</span>';
                })
                ->addColumn('availability_type', function ($row) {
                    return ucfirst(strtolower($row->availability_type ?? 'ALWAYS'));
                })
                ->addColumn('active_until', function ($row) {
                    return $row->active_until ? $row->active_until->format('d M Y H:i') : '--';
                })
                ->addColumn('registration_count', function ($row) {
                    return (int) $row->registrations_count;
                })
                ->addColumn('form_count', function ($row) {
                    return $row->forms()->count();
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('event.edit', $row) . '" class="btn btn-sm btn-primary text-white"><i class="fa fa-pencil"></i> Edit</a>'
                        . ' <a href="' . route('events.show', $row) . '" class="btn btn-sm btn-warning text-dark"><i class="fa fa-eye"></i> Live Form</a>'
                        . ' <a href="' . route('event.registrations.index', $row) . '" class="btn btn-sm btn-info text-white"><i class="fa fa-users"></i> Registrations</a>';
                })
                ->rawColumns(['title', 'status', 'action'])
                ->make(true);
        }

        return response()->json([]);
    }

    public function create()
    {
        return view('event.form', ['title' => 'Create Event', 'branches' => Branch::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $availabilityType = $request->input('availability_type', 'ALWAYS');

        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug'],
            'intro_html' => ['nullable', 'string'],
            'price_question_label' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,CLOSED'],
            'availability_type' => ['required', 'in:ALWAYS,LIMITED'],
            'active_until' => $availabilityType === 'LIMITED'
                ? ['required', 'date']
                : ['nullable', 'date'],
        ]);

        // Convert empty strings to null for nullable fields
        $validated['branch_id'] = empty($validated['branch_id']) ? null : $validated['branch_id'];
        $validated['intro_html'] = empty($validated['intro_html']) ? null : $validated['intro_html'];
        $validated['price_question_label'] = empty($validated['price_question_label']) ? null : $validated['price_question_label'];

        if (($validated['availability_type'] ?? 'ALWAYS') === 'ALWAYS') {
            $validated['active_until'] = null;
        } else {
            $validated['active_until'] = empty($validated['active_until']) ? null : $validated['active_until'];
        }

        $validated['uuid'] = (string) Str::uuid();

        if (Auth::check()) {
            $validated['created_by'] = Auth::id();
        }

        $event = Event::create($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Event created successfully.'], 201);
        }

        return redirect()->route('event.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        return view('event.form', ['title' => 'Event Detail', 'event' => $event, 'branches' => Branch::orderBy('name')->get()]);
    }

    public function preview(Event $event)
    {
        $fields = $event->fields()
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get();

        $priceOptions = $event->priceOptions()
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get();

        return view('event.preview', compact('event', 'fields', 'priceOptions'));
    }

    public function edit(Event $event)
    {
        return view('event.form', ['title' => 'Edit Event', 'event' => $event, 'branches' => Branch::orderBy('name')->get()]);
    }

    public function update(Request $request, Event $event)
    {
        $availabilityType = $request->input('availability_type', $event->availability_type ?? 'ALWAYS');

        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug,' . $event->id],
            'intro_html' => ['nullable', 'string'],
            'price_question_label' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,CLOSED'],
            'availability_type' => ['required', 'in:ALWAYS,LIMITED'],
            'active_until' => $availabilityType === 'LIMITED'
                ? ['required', 'date']
                : ['nullable', 'date'],
        ]);

        // Convert empty strings to null for nullable fields
        $validated['branch_id'] = empty($validated['branch_id']) ? null : $validated['branch_id'];
        $validated['intro_html'] = empty($validated['intro_html']) ? null : $validated['intro_html'];
        $validated['price_question_label'] = empty($validated['price_question_label']) ? null : $validated['price_question_label'];

        if (($validated['availability_type'] ?? 'ALWAYS') === 'ALWAYS') {
            $validated['active_until'] = null;
        } else {
            $validated['active_until'] = empty($validated['active_until']) ? null : $validated['active_until'];
        }

        if (Auth::check()) {
            $validated['updated_by'] = Auth::id();
        }

        $event->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Event updated successfully.']);
        }

        return redirect()->route('event.index')->with('success', 'Event updated successfully.');
    }

    public function deleteMany(Request $request)
    {
        $ids = $request->input('ids', []);
        $eventIds = collect($ids)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($eventIds)) {
            return redirect()->route('event.index')->with('error', 'Please select at least one event to delete.');
        }

        $events = Event::whereIn('id', $eventIds)->get();

        foreach ($events as $event) {
            $this->deleteEventRecord($event);
        }

        return redirect()->route('event.index')->with('success', 'Selected events deleted permanently. All related form data, email templates, registrations, and uploaded files were removed and cannot be restored.');
    }

    public function destroy(Event $event)
    {
        $this->deleteEventRecord($event);

        return redirect()->route('event.index')->with('success', 'Event deleted permanently. All related form data, email templates, registrations, and uploaded files were removed and cannot be restored.');
    }

    private function deleteEventRecord(Event $event): void
    {
        DB::transaction(function () use ($event) {
            $event->load(['registrations.fieldAnswers', 'forms', 'emailTemplates', 'priceOptions']);

            foreach ($event->registrations as $registration) {
                foreach ($registration->fieldAnswers as $answer) {
                    if (! empty($answer->value) && is_string($answer->value)) {
                        $attachmentPath = trim($answer->value);

                        if ($attachmentPath !== '' && Storage::disk('event')->exists($attachmentPath)) {
                            Storage::disk('event')->delete($attachmentPath);
                        }
                    }
                }

                if (! empty($registration->code)) {
                    Storage::disk('event')->deleteDirectory($registration->code);
                }

                $registration->fieldAnswers()->delete();
                $registration->delete();
            }

            $event->forms()->delete();
            $event->emailTemplates()->delete();
            $event->priceOptions()->delete();

            $event->delete();
        });
    }

    /**
     * Show event registrations list
     */
    public function registrations(Event $event)
    {
        $fields = $this->answeredEventFields($event);
        $levelField = $fields->first(function ($field) {
            $value = Str::lower(($field->field_key ?? '') . ' ' . ($field->label ?? ''));

            return Str::contains($value, 'level')
                || Str::contains($value, 'division')
                || Str::contains($value, 'divisi');
        });

        $levelOptions = $levelField
            ? EventFieldAnswer::query()
                ->where('event_field_id', $levelField->id)
                ->whereHas('eventRegistration', fn ($query) => $query->where('event_id', $event->id))
                ->whereNotNull('value')
                ->where('value', '<>', '')
                ->distinct()
                ->orderBy('value')
                ->pluck('value')
            : collect();

        return view('event.registrations.index', [
            'title' => 'Event Registrations',
            'event' => $event,
            'fields' => $fields,
            'levelField' => $levelField,
            'levelOptions' => $levelOptions,
        ]);
    }

    public function exportRegistrations(Event $event)
    {
        $filename = 'event-registrations-' . Str::slug($event->title) . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new EventRegistrationExport($event), $filename);
    }

    /**
     * Get event registrations data for datatable
     */
    public function registrationsDatatables(UtilitiesRequest $request, Event $event)
    {
        $fields = $this->answeredEventFields($event);
        $query = $event->registrations()
            ->with('fieldAnswers.eventField')
            ->latest('created_at');

        foreach ((array) $request->input('filters', []) as $fieldKey => $filterValue) {
            $filterValue = trim((string) $filterValue);

            if ($filterValue === '') {
                continue;
            }

            $query->whereHas('fieldAnswers', function ($answerQuery) use ($event, $fieldKey, $filterValue) {
                $answerQuery->where('value', 'like', '%' . $filterValue . '%')
                    ->whereHas('eventField', function ($fieldQuery) use ($event, $fieldKey) {
                        $fieldQuery->where('event_id', $event->id)
                            ->where('field_key', $fieldKey);
                    });
            });
        }

        if (! $request->ajax()) {
            return response()->json([]);
        }

        $dataTable = datatables()->of($query)
            ->addColumn('code', fn ($row) => '<code>' . e($row->code) . '</code>')
            ->addColumn('amount', fn ($row) => 'Rp ' . number_format((float) $row->amount, 0, ',', '.'))
            ->addColumn('registered_at', fn ($row) => $row->registered_at ? $row->registered_at->format('d M Y H:i') : '')
            ->addColumn('status', function ($row) {
                $colors = [
                    'SUBMITTED' => 'info', 'PENDING' => 'warning', 'PAID' => 'success',
                    'CANCELLED' => 'danger', 'EXPIRED' => 'secondary',
                ];
                $color = $colors[$row->status] ?? 'secondary';

                return '<span class="badge registration-status-badge bg-' . $color . '">' . e($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) use ($event) {
                return '<a href="' . route('event.registration.show', [$event, $row]) . '" class="btn btn-sm btn-primary text-white"><i class="fa fa-eye"></i></a>'
                    . ' <form method="POST" action="' . route('event.registration.delete', [$event, $row]) . '" style="display:inline" onsubmit="return confirm(\'Are you sure?\');">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-sm btn-danger text-white"><i class="fa fa-trash"></i></button></form>';
            });

        foreach ($fields as $field) {
            $dataTable->addColumn('field_' . $field->id, function ($row) use ($field) {
                $answer = $row->fieldAnswers->firstWhere('event_field_id', $field->id);
                $value = $answer ? $answer->value : '';

                if ($field->type === 'checkbox' && is_string($value)) {
                    $decoded = json_decode($value, true);
                    $value = json_last_error() === JSON_ERROR_NONE && is_array($decoded)
                        ? implode(', ', $decoded)
                        : $value;
                }

                return $value;
            });
        }

        return $dataTable->rawColumns(['code', 'status', 'action'])->make(true);
    }

    private function answeredEventFields(Event $event)
    {
        return EventFieldAnswer::query()
            ->whereHas('eventRegistration', fn ($query) => $query->where('event_id', $event->id))
            ->with('eventField')
            ->get()
            ->pluck('eventField')
            ->filter(fn ($field) => $field && $field->event_id === $event->id)
            ->unique('id')
            ->sortBy(fn ($field) => [$field->order_index, $field->id])
            ->values();
    }

    /**
     * Show single registration details
     */
    public function showRegistration(Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        $registration->load('fieldAnswers.eventField');

        return view('event.registrations.show', [
            'title' => 'Registration Details',
            'event' => $event,
            'registration' => $registration,
        ]);
    }

    public function attachment(Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        $filePath = request('file');

        if (! is_string($filePath) || $filePath === '' || ! Storage::disk('event')->exists($filePath)) {
            abort(404);
        }

        $absolutePath = Storage::disk('event')->path($filePath);
        $mimeType = Storage::disk('event')->mimeType($filePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        ]);
    }

    /**
     * Delete a registration
     */
    public function deleteRegistration(Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        $registration->delete();

        return redirect()->route('event.registrations.index', $event)
            ->with('success', 'Registration deleted successfully.');
    }
}
