<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Services\HolidayService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
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
     * @param  \App\Http\Requests\StoreHolidayRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'date'=>'required',
            'end-date' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        $validate['date'] = Carbon::createFromFormat('d F Y', $request['date'])->format('Y-m-d');
        if (!empty($validate['end-date'])) {
            $validate['end-date'] = Carbon::parse($validate['end-date'])->format('Y-m-d');
        } else {
            $validate['end-date'] = null;
        }
        $this->holidayService->post($validate);
        return redirect()
        ->back()
        ->with('holiday-success', 'Holiday has been saved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        //
    }

    public function isHoliday($date)
    {
        $holiday =  $this->holidayService->isHoliday($date);
        return response()->json($holiday);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHolidayRequest  $request
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->holidayService->delete($id);
        return redirect()
        ->back()
        ->with('holiday-success', 'Holiday has been deteled successfully.');
    }
}
