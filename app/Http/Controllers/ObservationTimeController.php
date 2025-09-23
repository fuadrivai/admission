<?php

namespace App\Http\Controllers;

use App\Models\ObservationTime;
use App\Http\Requests\StoreObservationTimeRequest;
use App\Http\Requests\UpdateObservationTimeRequest;

class ObservationTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
     * @param  \App\Http\Requests\StoreObservationTimeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreObservationTimeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ObservationTime  $observationTime
     * @return \Illuminate\Http\Response
     */
    public function show(ObservationTime $observationTime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ObservationTime  $observationTime
     * @return \Illuminate\Http\Response
     */
    public function edit(ObservationTime $observationTime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateObservationTimeRequest  $request
     * @param  \App\Models\ObservationTime  $observationTime
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateObservationTimeRequest $request, ObservationTime $observationTime)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ObservationTime  $observationTime
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObservationTime $observationTime)
    {
        //
    }
}
