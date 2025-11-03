<?php

namespace App\Http\Controllers;

use App\Models\SchoolVisit;
use Illuminate\Http\Request;

class SchoolVisitController extends Controller
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

     public function form()
    {
        return view('schoolvisit-form', ["title" => "School Visit Form"]);
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
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolVisit $schoolVisit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function edit(SchoolVisit $schoolVisit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SchoolVisit $schoolVisit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolVisit  $schoolVisit
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolVisit $schoolVisit)
    {
        //
    }
}
