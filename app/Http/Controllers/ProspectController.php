<?php

namespace App\Http\Controllers;

use App\Models\Prospects;
use App\Services\ProspectService;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private ProspectService $prospectService;
    public function __construct(ProspectService $prospectService)
    {
        $this->prospectService = $prospectService;
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
     * @param  \App\Models\Prospects  $prospects
     * @return \Illuminate\Http\Response
     */
    public function show(Prospects $prospects)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Prospects  $prospects
     * @return \Illuminate\Http\Response
     */
    public function edit(Prospects $prospects)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prospects  $prospects
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prospects $prospects)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Prospects  $prospects
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prospects $prospects)
    {
        //
    }

    public function getByCode($code)
    {
        $prospect = $this->prospectService->getbyCode($code);
        return $prospect;
    }
}
