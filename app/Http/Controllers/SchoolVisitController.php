<?php

namespace App\Http\Controllers;

use App\Models\SchoolVisit;
use App\Services\BranchService;
use App\Services\HolidayService;
use App\Services\SchoolVisitMessageService;
use App\Services\SchoolVisitService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class SchoolVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private SchoolVisitService $schooolVisitService;
    private BranchService $branchService;
    private HolidayService $holidayService;
    private SchoolVisitMessageService $messageService;

    public function __construct(
        SchoolVisitService $schooolVisitService,
        BranchService $branchService,
        HolidayService $holidayService,
        SchoolVisitMessageService $messageService
    )
    {
        $this->schooolVisitService = $schooolVisitService;
        $this->branchService = $branchService;
        $this->holidayService = $holidayService;
        $this->messageService = $messageService;
    }
    public function index(Request $request)
    {
        $query = SchoolVisit::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%'.$request->search.'%')
                ->orWhere('parent_name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
                ->orWhere('child_name', 'like', '%'.$request->search.'%')
                ->orWhere('phone_number', 'like', '%'.$request->search.'%');
                });
        }

        if ($request->level && $request->level !== 'all') {
            $query->where('level_id', $request->level);
        }
        if ($request->branch && $request->branch !== 'all') {
            $query->where('branch_id', $request->branch);
        }
        if ($request->grade && $request->grade !== 'all') {
            $query->where('grade_id', $request->grade);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $visits = $query->paginate(request('perpage')??10)->withQueryString();

        if ($request->ajax()) {
            return view('schoolvisit._list', compact('visits'))->render();
        }

        return view('schoolvisit.index', ["title" => "School Visit List", "visits" => $visits]);
    }

     public function form()
    {
        $branches = $this->branchService->get();
        return view('schoolvisit-form', ["title" => "School Visit Form","branches"=>$branches]);
    }
    public function success()
    {
        return view('schoolvisit-success', ["title" => "School Visit Form"]);
    }

    public function setting()
    {
        $max = $this->schooolVisitService->maxCapacity();
        $holidays = $this->holidayService->nextHoliday();
        $messages = $this->messageService->get(['branch']);
        return view('schoolvisit.setting', ["title" => "School Visit Setting","max"=>$max,"holidays"=>$holidays,"messages"=>$messages]);
    }

    public function checkCapacity(Request $request)
    {
        $isFull =$this->schooolVisitService->getByDateTime($request);
        return response()->json($isFull);
    }

    public function postMax(Request $request)
    {
        $validated = $request->validate([
            'id'=>'required',
            'max'=>'required|numeric',
        ]);
        $max = $this->schooolVisitService->postMax($validated);
        return redirect()
        ->back()
        ->with('success', 'Maximum capacity has been updated successfully.');
    }

    public function datatables(UtilitiesRequest $request)
    {

        $schoolVisits = SchoolVisit::query();

        if ($request->code && $request->level_id != '') {
            $schoolVisits->where('code', 'like', '%' . $request->code . '%');
        }
        if ($request->name && $request->name != '') {
            $schoolVisits->where('parent_name', 'like', '%' .$request->name . '%')
                ->orWhere('child_name', 'like', '%' .$request->name . '%');
        }
        if ($request->level_id && $request->level_id != 'all') {
            $schoolVisits->where('level_id', $request->level_id);
        }
        if ($request->grade_id && $request->grade_id != 'all') {
            $schoolVisits->where('grade_id', $request->grade_id);
        }
        if ($request->branch_id && $request->branch_id != 'all') {
            $schoolVisits->where('branch_id', $request->branch_id);
        }
        if ($request->status && $request->status != 'all') {
            $schoolVisits->where('status', $request->status);
        }

        if ($request->startDate && $request->startDate != '') {
            $startDate = Carbon::createFromFormat('d F Y', $request->startDate)->format('Y-m-d');
            $endate = null;
            if ($request->endDate && $request->endDate != '') {
                $endate = Carbon::createFromFormat('d F Y', $request->endDate)->format('Y-m-d');
                $schoolVisits->whereBetween('date', [$startDate, $endate]);
            }else{
                $schoolVisits->where('date', $startDate);
            }
        }

        if ($request->ajax()) {
            return datatables()->of($schoolVisits)->make(true);
        }

        return view('schoolvisit.index', ["title" => "School Visit List"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolVisit $schoolVisit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function edit(SchoolVisit $schoolVisit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SchoolVisit $schoolVisit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolVisit $schoolVisit)
    {
        //
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'parent_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email',
            'zipcode' => 'required|string',
            'child_name' => 'required|string',
            'branch_id' => 'required|integer',
            'branch_name' => '',
            'level_id' => 'required|integer',
            'level_name' => '',
            'grade_id' => 'required|integer',
            'grade_name' => '',
            'academic_year' => 'required|string',
            'current_school' => 'required|string',
            'info_from' => 'required|string',
            'info_from_message' => '',
            'date' => 'required|date',
            'time' => 'required',
            'number_visitor' => 'required|integer|min:1',
            'already_enrol' => 'required|in:yes,no,will',
            'roles' => 'required|array',
            'roles.*.rule' => 'required|string',
            'roles.*.checked' => 'required|in:true,false,1,0',
            'roles.*.value' => 'required|string',
            'code' => 'nullable',
            'prospects_id' => 'nullable',
        ]);

        $observation = $this->schooolVisitService->post($validated);
        return response()->json($observation);
    }
}
