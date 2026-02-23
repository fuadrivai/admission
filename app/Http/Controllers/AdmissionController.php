<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Services\AdmissionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private AdmissionService $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }
    public function index(Request $request)
    {
        $query = Admission::query()
                ->with([
                    'applicant',
                    'level.division',
                    'branch',
                    'grade',
                    'statement'
                ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('accademic_year', 'like', "%{$search}%")
                ->orWhereHas('applicant', function ($qa) use ($search) {
                    $qa->where('fullname', 'like', "%{$search}%");
                })
                ->orWhereHas('statement', function ($qs) use ($search) {
                    $qs->where('fullname', 'like', "%{$search}%");
                })
                ->orWhereHas('applicant.parents', function ($qp) use ($search) {
                    $qp->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                })
                ;
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
            $query->where('payment_status', $request->status);
        }

        $admissions = $query->paginate(request('perpage')??10)->withQueryString();

        if ($request->ajax()) {
            return view('document._list', compact('admissions'))->render();
        }

        return view('document.index', ["title" => "Document", "admissions" => $admissions]);
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
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function show(Admission $admission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admission = $this->admissionService->show($id);
        return view('document.detail', ['admission' => $admission, 'title' => 'Document Detail']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admission $admission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admission $admission)
    {
        //
    }

    public function checkStatus($code)
    {
        try {
            $path = $this->admissionService->checkStatus($code);
            return response()->json($path);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enrolment or Admission code not found'
            ], $e->getCode() ?: 404);
        }
    }
    public function showByCode($code)
    {
        try {
            $admission = $this->admissionService->showByCode($code);
            return response()->json($admission);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enrolment or Admission code not found'
            ], $e->getCode() ?: 404);
        }
    }

    public function getParent($child_id,$role)
    {
        $parent = $this->admissionService->getParent($child_id,$role);
        return response()->json($parent);
    }

    public function postParent(Request $request)
    {
        $data = $request->all();
        $parent = $this->admissionService->postParent($data);
        return response()->json($parent);
    }

    public function postApplicant(Request $request)
    {
        $data = $request->all();
        $admission = $this->admissionService->post($data);
        return response()->json($admission);
    }

    public function postHealth(Request $request)
    {
        $data = $request->all();
        $admission = $this->admissionService->postHealth($data);
        return response()->json($admission);
    }

    public function getApplicant($id)
    {
        $applicant = $this->admissionService->getApplicant($id);
        return response()->json($applicant);
    }
    
    public function studentForm()
    {
        return view('enrolment.form.student-enrolment');
    }
    public function success($code)
    {
        $admission = $this->admissionService->showByCode($code);
        return view('enrolment.form.student-success',['data'=>$admission]);
    }
}
