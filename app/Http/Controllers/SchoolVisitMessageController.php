<?php

namespace App\Http\Controllers;

use App\Models\SchoolVisitMessage;
use App\Http\Requests\StoreSchoolVisitMessageRequest;
use App\Http\Requests\UpdateSchoolVisitMessageRequest;

class SchoolVisitMessageController extends Controller
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
     * @param  \App\Http\Requests\StoreSchoolVisitMessageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchoolVisitMessageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolVisitMessage  $schoolVisitMessage
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolVisitMessage $schoolVisitMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SchoolVisitMessage  $schoolVisitMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(SchoolVisitMessage $schoolVisitMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSchoolVisitMessageRequest  $request
     * @param  \App\Models\SchoolVisitMessage  $schoolVisitMessage
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchoolVisitMessageRequest $request, SchoolVisitMessage $schoolVisitMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolVisitMessage  $schoolVisitMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolVisitMessage $schoolVisitMessage)
    {
        //
    }
}
