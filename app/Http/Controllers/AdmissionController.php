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
    public function index()
    {
        //
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
    public function edit(Admission $admission)
    {
        //
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

    public function showByCode($code)    {

        try {
            $admission = $this->admissionService->showByCode($code);
            return response()->json($admission);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Admission code not found'
            ], $e->getCode() ?: 404);
        }
    }

    public function postApplicant(Request $request)
    {
        $data = $request->all();
        $admission = $this->admissionService->post($data);
        return response()->json($admission);
    }
    
    public function studentForm()
    {
        return view('enrolment.form.student-enrolment');
    }
    public function studentFile()
    {
        return view('enrolment.form.student-file');
    }
    public function studentAproval()
    {
        return view('enrolment.form.student-approval');
    }
}
