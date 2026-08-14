<?php

namespace App\Http\Controllers;

use App\Models\EventPriceOption;
use App\Http\Requests\StoreEventPriceOptionRequest;
use App\Http\Requests\UpdateEventPriceOptionRequest;

class EventPriceOptionController extends Controller
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
     * @param  \App\Http\Requests\StoreEventPriceOptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventPriceOptionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventPriceOption  $eventPriceOption
     * @return \Illuminate\Http\Response
     */
    public function show(EventPriceOption $eventPriceOption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EventPriceOption  $eventPriceOption
     * @return \Illuminate\Http\Response
     */
    public function edit(EventPriceOption $eventPriceOption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventPriceOptionRequest  $request
     * @param  \App\Models\EventPriceOption  $eventPriceOption
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventPriceOptionRequest $request, EventPriceOption $eventPriceOption)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventPriceOption  $eventPriceOption
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventPriceOption $eventPriceOption)
    {
        //
    }
}
