<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Services\ObservationService;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class ObservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private ObservationService $observationService;

    public function __construct(ObservationService $observationService)
    {
        $this->observationService = $observationService;
    }
    public function index()
    {
        return view('observation.index', ["title" => "User Observations"]);
    }

    public function form()
    {
        return view('observation-form', ["title" => "Observation Form"]);
    }

    public function datatables(UtilitiesRequest $request)
    {

        $observations = Observation::query();
        if ($request->ajax()) {
            return datatables()->of($observations)->make(true);
        }

        return view('observation.index', ["title" => "Observation Settings"]);
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
        //code
    }
    public function post(Request $request)
    {
        $validated = $request->validate([
            'child_name' => 'required',
            'gender' => 'required',
            'level' => 'required',
            'parent_name' => 'required',
            'relationship' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'grade' => 'required',
            'level' => 'required',
            'grade_id' => 'required|integer',
            'level_id' => 'required|integer',
            'observation_time_id' => 'required|integer',
        ]);

        $observation = $this->observationService->post($validated);
        return response()->json($observation);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        return view('observation-success', ["title" => "Observation Form"]);
    }
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'observation_time_id' => 'required|integer',
        ]);

        $observation = $this->observationService->fetch($validated);
        return response()->json($observation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
