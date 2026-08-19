<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Exports\EventRegistrationExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('event.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Show event registrations list
     */
    public function registrations(Event $event)
    {
        return view('event.registrations.index', [
            'title' => 'Event Registrations',
            'event' => $event,
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
        $query = $event->registrations()
            ->with('fieldAnswers.eventField')
            ->orderBy('created_at', 'desc');

        $fieldAliases = [
            'student_name' => ['student_name', 'studentName', 'student','students_full_name','students_fullname'],
            'parent_name' => ['parent_name', 'parentName', 'parent','parents_name'],
            'name' => ['name'],
            'fullname' => ['fullname', 'full_name', 'full-name'],
            'email' => ['email', 'email_address', 'emailAddress','parents_email','parents_email_address','parents_emailAddress'],
            'phone' => ['phone', 'phone_number', 'phoneNumber', 'mobile'],
            'level' => ['level', 'level_name', 'levelName'],
            'grade' => ['grade', 'grade_name', 'gradeName'],
        ];

        $getFieldValue = function ($registration, $aliases) {
            $normalizedAliases = collect($aliases)->map(function ($alias) {
                return Str::lower(str_replace(['-', '_', ' '], '', $alias));
            });

            $answer = $registration->fieldAnswers->first(function ($answer) use ($normalizedAliases) {
                if (! $answer->eventField) {
                    return false;
                }

                $fieldKey = Str::lower(str_replace(['-', '_', ' '], '', $answer->eventField->field_key));
                $label = Str::lower(str_replace(['-', '_', ' '], '', $answer->eventField->label));

                return $normalizedAliases->contains($fieldKey) || $normalizedAliases->contains($label);
            });

            return $answer && $answer->value !== null ? $answer->value : '';
        };

        if ($request->ajax()) {
            return datatables()->of($query)
                ->addColumn('student_name', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['student_name']);
                })
                ->addColumn('parent_name', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['parent_name']);
                })
                ->addColumn('name', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['name']);
                })
                ->addColumn('fullname', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['fullname']);
                })
                ->addColumn('email', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['email']);
                })
                ->addColumn('phone', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['phone']);
                })
                ->addColumn('level', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['level']);
                })
                ->addColumn('grade', function ($row) use ($getFieldValue, $fieldAliases) {
                    return $getFieldValue($row, $fieldAliases['grade']);
                })
                ->addColumn('code', function ($row) {
                    return '<code>' . $row->code . '</code>';
                })
                ->addColumn('status', function ($row) {
                    $statusColors = [
                        'SUBMITTED' => 'info',
                        'PENDING' => 'warning',
                        'PAID' => 'success',
                        'CANCELLED' => 'danger',
                        'EXPIRED' => 'secondary',
                    ];
                    $color = $statusColors[$row->status] ?? 'secondary';
                    return '<span class="badge badge-sm registration-status-badge bg-' . $color . '"><i>' . $row->status . '</i></span>';
                })
                ->addColumn('amount', function ($row) {
                    return 'Rp ' . number_format((float) $row->amount, 0, ',', '.');
                })
                ->addColumn('registered_at', function ($row) {
                    return $row->registered_at ? $row->registered_at->format('d M Y H:i') : '';
                })
                ->addColumn('submission_date', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) use ($event) {
                    return '<a href="' . route('event.registration.show', [$event, $row]) . '" class="btn btn-sm btn-primary text-white"><i class="fa fa-eye"></i></a>'
                        . ' <form method="POST" action="' . route('event.registration.delete', [$event, $row]) . '" style="display:inline;" onsubmit="return confirm(\'Are you sure?\');">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="btn btn-sm btn-danger text-white"><i class="fa fa-trash"></i></button>'
                        . '</form>';
                })
                ->rawColumns(['code', 'status', 'action'])
                ->make(true);
        }

        return response()->json([]);
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
