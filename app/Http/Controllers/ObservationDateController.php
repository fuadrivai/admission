<?php

namespace App\Http\Controllers;

use App\Models\ObservationDate;
use App\Services\ObservationDateService;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

class ObservationDateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private ObservationDateService $observationDateService;

    public function __construct(ObservationDateService $observationDateService)
    {
        $this->observationDateService = $observationDateService;
    }
    public function index()
    {
        //
    }
    public function datatables(UtilitiesRequest $request)
    {
        $datesQuery = ObservationDate::with(['times', 'times.observations'])->where(['type' => 1])
            ->orderBy('date', 'desc');

        if ($request->ajax()) {
            return datatables()->of($datesQuery)
                ->addColumn('times', function ($date) {
                    return $date->times->map(function ($t) {
                        return $t->time;
                    })->implode(',');
                })
                ->addColumn('available', function ($date) {
                    return $date->times->map(function ($t) {
                        $remaining = $t->max_quota - $t->observations->count();
                        return $remaining;
                    })->implode(',');
                })
                ->addColumn('quota', function ($date) {
                    return $date->times->map(function ($t) {
                        return $t->max_quota;
                    })->implode(',');
                })
                ->addColumn('status', function ($date) {
                    return $date->times->map(function ($t) {
                        $remaining = $t->max_quota - $t->observations->count();
                        if ($remaining > 0) {
                            return 'Available';
                        } else {
                            return 'Full';
                        }
                    })->implode(',');
                })
                ->rawColumns(['times', 'status', 'available'])
                ->make(true);
        }

        return view('observation.setting', ["title" => "Observation Settings"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('observation.form', ["title" => "Create Observation Date"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreObservationDateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'times' => 'required|array|min:1',
            'times.*.time' => 'required',
            'times.*.max_quota' => 'required|integer|min:1',
        ]);
        $date = $this->observationDateService->post($validated);
        return response()->json($date);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $observationDate = $this->observationDateService->show($id);
        return response()->json($observationDate);
    }
    public function date($date)
    {
        $observationDate = $this->observationDateService->getByDate($date);
        return response()->json($observationDate);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $observationDate = $this->observationDateService->show($id);
        return view('observation.form', ["title" => "Edit Observation Date", "observationDate" => $observationDate]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateObservationDateRequest  $request
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'date' => 'required|date',
            'times' => 'required|array|min:1',
            'times.*.time' => 'required',
            'times.*.max_quota' => 'required|integer|min:1',
        ]);
        $date = $this->observationDateService->put($validated);
        return response()->json($date);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObservationDate $observationDate)
    {
        //
    }
}
