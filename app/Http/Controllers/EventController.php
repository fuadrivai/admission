<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class EventController extends Controller
{
    public function index()
    {
        return view('event.index', ['title' => 'Event']);
    }

    public function datatables(UtilitiesRequest $request)
    {
        $query = Event::with('branch');

        if ($request->ajax()) {
            return datatables()->of($query)
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
                ->addColumn('form_count', function ($row) {
                    return $row->forms()->count();
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('event.edit', $row) . '" class="btn btn-sm btn-primary text-white"><i class="fa fa-pencil"></i> Edit</a>'
                        . ' <a href="' . route('events.show', $row) . '" class="btn btn-sm btn-warning text-dark"><i class="fa fa-eye"></i> Live Form</a>'
                        . ' <a href="' . route('event.registrations.index', $row) . '" class="btn btn-sm btn-info text-white"><i class="fa fa-users"></i> Registrations</a>';
                })
                ->rawColumns(['status', 'action'])
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

    /**
     * Get event registrations data for datatable
     */
    public function registrationsDatatables(UtilitiesRequest $request, Event $event)
    {
        $query = $event->registrations()
            ->with('fieldAnswers.eventField')
            ->orderBy('created_at', 'desc');

        if ($request->ajax()) {
            return datatables()->of($query)
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
                    return '<span class="badge bg-' . $color . '">' . $row->status . '</span>';
                })
                ->addColumn('amount', function ($row) {
                    return 'Rp ' . number_format((float) $row->amount, 0, ',', '.');
                })
                ->addColumn('registered_at', function ($row) {
                    return $row->registered_at ? $row->registered_at->format('d M Y H:i') : '--';
                })
                ->addColumn('submission_date', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) use ($event) {
                    return '<a href="' . route('event.registration.show', [$event, $row]) . '" class="btn btn-sm btn-primary text-white"><i class="fa fa-eye"></i> View</a>'
                        . ' <form method="POST" action="' . route('event.registration.delete', [$event, $row]) . '" style="display:inline;" onsubmit="return confirm(\'Are you sure?\');">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="btn btn-sm btn-danger text-white"><i class="fa fa-trash"></i> Delete</button>'
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
