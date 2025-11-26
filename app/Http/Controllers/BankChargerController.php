<?php

namespace App\Http\Controllers;

use App\Models\BankCharger;
use App\Services\BankChargerService;
use Illuminate\Http\Request;

class BankChargerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private BankChargerService $bankChargerService;

    public function __construct(BankChargerService $bankChargerService)
    {
        $this->bankChargerService = $bankChargerService;
    }

    public function getSingle()
    {
        $bankCharger = $this->bankChargerService->get();
        return response()->json($bankCharger);
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
     * @param  \App\Models\BankCharger  $bankCharger
     * @return \Illuminate\Http\Response
     */
    public function show(BankCharger $bankCharger)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankCharger  $bankCharger
     * @return \Illuminate\Http\Response
     */
    public function edit(BankCharger $bankCharger)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankCharger  $bankCharger
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankCharger $bankCharger)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankCharger  $bankCharger
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankCharger $bankCharger)
    {
        //
    }
}
