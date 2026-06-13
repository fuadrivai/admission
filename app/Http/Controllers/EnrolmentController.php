<?php

namespace App\Http\Controllers;

use App\Exports\EnrolmentExport;
use App\Models\Enrolment;
use App\Services\BranchService;
use App\Services\EnrolmentService;
use App\Services\ProspectService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;

class EnrolmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private BranchService $branchService;
    private EnrolmentService $enrolmentService;
    private ProspectService $prospectService;
    public function __construct(BranchService $branchService, EnrolmentService $enrolmentService, ProspectService $prospectService)
    {
        $this->branchService = $branchService;
        $this->enrolmentService = $enrolmentService;
        $this->prospectService = $prospectService;
    }
    public function index(Request $request)
    {
        $query = $this->enrolmentService->search($request);
        $summary = $this->enrolmentService->summary($query);
        $enrolments = $query->paginate(request('perpage')??10)->withQueryString();
        if ($request->ajax()) {
            return view('enrolment._list', compact('enrolments', 'summary'))->render();
        }

        return view('enrolment.index', ["title" => "Enrolment", "enrolments" => $enrolments, "summary" => $summary]);
    }

   public function datatables(UtilitiesRequest $request)
    {

        $enrolment = Enrolment::query();
        if ($request->ajax()) {
            return datatables()->of($enrolment->with(['branch','grade', 'level']))
                ->addColumn('branch_name', function ($row) {
                    return $row->branch ? $row->branch->name : '-';
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level ? $row->level->name : '-';
                })
                ->addColumn('grade_name', function ($row) {
                    return $row->grade ? $row->grade->name : '-';
                })
                ->make(true);
        }

        return view('enrolment.index', ["title" => "Enrolment"]);
    }
    public function setting()
    {
        return view('enrolment.setting');
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
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function showByCode($code)
    {
        try {
            $enrolment = $this->enrolmentService->showByCode($code,['branch', 'grade', 'level','prospect']);
            return response()->json($enrolment);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enrollment code not found'
            ], $e->getCode() ?: 404);
        }
    }

    public function show(Enrolment $enrolment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function edit(Enrolment $enrolment)
    {
        $enrolment = $this->enrolmentService->show($enrolment->id);
        return view('enrolment.detail', ["title" => "Enrolment Detail", "enrolment" => $enrolment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Enrolment $enrolment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrolment $enrolment)
    {
        //
    }
    

    public function form()
    {
        $branches = $this->branchService->get();
        return view('enrolment.form.external', compact('branches'));
    }

    public function post(Request $request)
    {
        
        $rules = [
            'alreadyVisit'          => 'required',
            'code'                  => 'nullable|string',
            'prospectsId'           => 'nullable|string',
            'isCurrentStudent'      =>'required',
            'studentBranch'         => 'nullable|string',
            'mhisPortalUsername'    => 'nullable|string',
            'branch'                => 'required',
            'level'                 => 'required',
            'grade'                 => 'required',
            'academicYearId'        => 'required|integer',
            'academicYear'          => 'required|string',
            'parentName'            => 'required|string|max:255',
            'email'                 => 'required',
            'phone'                 => 'required|string|max:20',
            'relationship'          => 'required|string',
            'zipCode'               => 'nullable|string|max:20',
            'address'               => 'required|string',
            'childName'             => 'required|string|max:255',
            'childNickname'         => 'required|string|max:255',
            'placeOfBirth'          => 'required|string|max:120',
            'dateOfBirth'           => 'required',
            'currentSchool'         => 'nullable|string|max:255',
            'childSosmed'           => 'nullable|string|max:255',
            'opendayVisited'        => 'required',
            'knowledgeAboutProgram' => 'required|in:yes,no,maybe',
            'infoFrom'              => 'required|string',
            'infoFromMessage'       => 'nullable|string',
            'reasonForEnrolment'    => 'required|string',
            'prefferedProgram'      => 'required|string',
            'expectationMhisImpact' => 'required|string',
            'recommenderName'       => 'nullable|string|max:255',
            'recommenderPhone'      => 'nullable|string|max:20',
            'recommenderChildName'  => 'nullable|string|max:255',
            'recommenderChildClass' => 'nullable|string|max:120',
        ];

        if ($request->alreadyVisit === "true") {
            $rules['code'] = 'required|string';
        }

        $validated = $request->validate($rules);
        $enrolment = $this->enrolmentService->post((object)$validated);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Enrolment form submitted successfully.',
            'data'      => $enrolment,
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->enrolmentService->search($request);
        $timestamps = Carbon::now()->format('Ymd_His');
        return Excel::download(
            new EnrolmentExport($query->get()),
            'Enrolment_Report_' . $timestamps . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function history($id)
    {
        $prospect = $this->prospectService->show($id);
        return view('schoolvisit._history', compact('prospect'))->render();
    }

    public function updateSourceData(Request $request, Enrolment $enrolment)
    {
        $validated = $request->validate([
            'source_data' => 'required|in:internal,external',
        ]);

        $enrolment->source_data = $validated['source_data'];
        $enrolment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Source data updated successfully.',
            'data' => [
                'id' => $enrolment->id,
                'source_data' => $enrolment->source_data,
            ],
        ]);
    }

}
