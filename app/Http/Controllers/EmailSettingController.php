<?php

namespace App\Http\Controllers;

use App\Models\EmailSetting;
use App\Http\Requests\StoreEmailSettingRequest;
use App\Http\Requests\UpdateEmailSettingRequest;

class EmailSettingController extends Controller
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
     * @param  \App\Http\Requests\StoreEmailSettingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmailSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailSetting  $emailSetting
     * @return \Illuminate\Http\Response
     */
    public function show(EmailSetting $emailSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailSetting  $emailSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailSetting $emailSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmailSettingRequest  $request
     * @param  \App\Models\EmailSetting  $emailSetting
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmailSettingRequest $request, EmailSetting $emailSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailSetting  $emailSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailSetting $emailSetting)
    {
        //
    }
}
