<?php

namespace App\Http\Controllers;

use App\Models\EnrolmentPrice;
use App\Services\BranchService;
use App\Services\EnrolmentPriceService;
use App\Services\LevelService;
use Illuminate\Http\Request;

class EnrolmentPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    private EnrolmentPriceService $enrolmentPriceService;
    private BranchService $branchService;
    private LevelService $levelService;

    public function __construct(EnrolmentPriceService $enrolmentPriceService, BranchService $branchService, LevelService $levelService)
    {
        $this->enrolmentPriceService = $enrolmentPriceService;
        $this->branchService = $branchService;
        $this->levelService = $levelService;
    }

    
    public function index()
    {
        $prices = $this->enrolmentPriceService->get(['branch', 'level']);
        return response()->json($prices);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branches = $this->branchService->get();
        return view('enrolment.price-form', compact('branches'));
    }

    public function getRegistrationPrice($branchId,$levelId)
    {
        $price = $this->enrolmentPriceService->getRegistrationPrice($branchId,$levelId);
        return response()->json($price);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $price = $request->validate([
            'branch' => 'required',
            'level' => 'required',
            'type' => 'required|string',
            'name' => 'required|string',
            'price' => 'required',
            'is_active' => 'sometimes|boolean|nullable',
        ]);

        $price['price'] = str_replace(',', '', $price['price']);

        $this->enrolmentPriceService->post($price);
        return redirect('/enrolment/setting')->with('success', 'Enrolment price created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EnrolmentPrice  $enrolmentPrice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $price = $this->enrolmentPriceService->show($id);
        return $price;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EnrolmentPrice  $enrolmentPrice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $price = $this->enrolmentPriceService->show($id);
        $branches = $this->branchService->get();
        $levels = $this->levelService->get();
        $title = "Enrolment Price Form";
        return view('enrolment.price-form', compact('price', 'branches', 'levels', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EnrolmentPrice  $enrolmentPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $price = $request->validate([
            'id' => 'required|exists:enrolment_prices,id',
            'branch' => 'required',
            'level' => 'required',
            'type' => 'required|string',
            'name' => 'required|string',
            'price' => 'required',
            'is_active' => 'sometimes|boolean|nullable',
        ]);
        $price['price'] = str_replace(',', '', $price['price']);
        $this->enrolmentPriceService->put($price);
        return redirect('/enrolment/setting')->with('success', 'Enrolment price updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EnrolmentPrice  $enrolmentPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(EnrolmentPrice $enrolmentPrice)
    {
        //
    }
}
