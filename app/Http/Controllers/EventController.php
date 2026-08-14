<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Event;
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
                        . ' <a href="' . route('event.forms.index', $row) . '" class="btn btn-sm btn-warning text-dark"><i class="fa fa-wrench"></i> Fix Form</a>'
                        . ' <a href="' . route('event.forms.index', $row) . '" class="btn btn-sm btn-info text-white"><i class="fa fa-list"></i> Forms</a>';
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
        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug'],
            'intro_html' => ['nullable', 'string'],
            'price_question_label' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,CLOSED'],
            'active_until' => ['nullable', 'date'],
        ]);

        // Convert empty strings to null for nullable fields
        $validated['branch_id'] = empty($validated['branch_id']) ? null : $validated['branch_id'];
        $validated['intro_html'] = empty($validated['intro_html']) ? null : $validated['intro_html'];
        $validated['price_question_label'] = empty($validated['price_question_label']) ? null : $validated['price_question_label'];
        $validated['active_until'] = empty($validated['active_until']) ? null : $validated['active_until'];

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

    public function edit(Event $event)
    {
        return view('event.form', ['title' => 'Edit Event', 'event' => $event, 'branches' => Branch::orderBy('name')->get()]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug,' . $event->id],
            'intro_html' => ['nullable', 'string'],
            'price_question_label' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,CLOSED'],
            'active_until' => ['nullable', 'date'],
        ]);

        // Convert empty strings to null for nullable fields
        $validated['branch_id'] = empty($validated['branch_id']) ? null : $validated['branch_id'];
        $validated['intro_html'] = empty($validated['intro_html']) ? null : $validated['intro_html'];
        $validated['price_question_label'] = empty($validated['price_question_label']) ? null : $validated['price_question_label'];
        $validated['active_until'] = empty($validated['active_until']) ? null : $validated['active_until'];

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
}
