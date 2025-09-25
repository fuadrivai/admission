<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Services\DivisionService;
use App\Services\LevelService;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private LevelService $levelService;
    private DivisionService $divisionService;

    public function __construct(LevelService $levelService, DivisionService $divisionService)
    {
        $this->levelService = $levelService;
        $this->divisionService = $divisionService;
    }
    public function index()
    {
        return view('level.index', ["title" => "Level"]);
    }

    public function datatables(UtilitiesRequest $request)
    {
        $level = Level::query();
        if ($request->ajax()) {
            return datatables()->of($level->with(['grades', 'division']))
                ->addColumn('division_name', function ($row) {
                    return $row->division ? $row->division->name : '-';
                })
                ->make(true);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisions = $this->divisionService->get();
        return view('level.form', ["title" => "Form Level", "divisions" => $divisions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLevelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'division' => 'required',
            'grades.*.name' => 'required',
        ]);
        $level = $this->levelService->post($validated);
        return response()->json($level);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $levels = $this->levelService->get();
        return response()->json($levels);
    }
    public function show($id)
    {
        $level = $this->levelService->show($id);
        return response()->json($level);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $level = $this->levelService->show($id);
        $divisions = $this->divisionService->get();
        return view('level.form', ["title" => "Form Level", "level" => $level, 'divisions' => $divisions]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLevelRequest  $request
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'division' => 'required',
            'grades' => 'required|array|min:1',
            'grades.*.name' => 'required',
        ]);
        $level = $this->levelService->put($validated);
        return response()->json($level);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Http\Response
     */
    public function destroy(Level $level)
    {
        //
    }
}
