<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use App\Services\DivisionService;
use App\Services\LevelService;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private DivisionService $divisionService;

    public function __construct(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
    }

    public function index()
    {
        return view('division.index2', ["title" => "Division"]);
    }
    public function datatables(UtilitiesRequest $request)
    {
        $division = Division::query();
        if ($request->ajax()) {
            return datatables()->of($division->with('levels'))->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('division.form', ["title" => "From Division"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDivisionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'id' => 'nullable'
        ]);
        if ($request->filled('id')) {
            $division = $this->divisionService->put($validated);
        } else {
            $division = $this->divisionService->post($validated);
        }

        return redirect('/division');
    }

    public function get()
    {
        $divisions = $this->divisionService->get();
        return response()->json($divisions);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function show(Division $division)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function edit(Division $division)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDivisionRequest  $request
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDivisionRequest $request, Division $division)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function destroy(Division $division)
    {
        //
    }
}
