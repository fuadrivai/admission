<?php

namespace App\Http\Controllers;

use App\Models\SchoolVisit;
use App\Services\BranchService;
use App\Services\HolidayService;
use App\Services\SchoolVisitService;
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

    public function __construct(SchoolVisitService $schooolVisitService,BranchService $branchService,HolidayService $holidayService)
    {
        $this->schooolVisitService = $schooolVisitService;
        $this->branchService = $branchService;
        $this->holidayService = $holidayService;
    }
    public function index()
    {
        return view('schoolvisit.index', ["title" => "School Visit List"]);
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
        return view('schoolvisit.setting', ["title" => "School Visit Setting","max"=>$max,"holidays"=>$holidays]);
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
        ]);

        $observation = $this->schooolVisitService->post($validated);
        return response()->json($observation);
    }
}
