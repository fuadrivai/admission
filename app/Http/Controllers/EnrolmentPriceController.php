<?php

namespace App\Http\Controllers;

use App\Models\EnrolmentPrice;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Level;
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

    
    public function index(Request $request)
    {
        $filters = $request->validate([
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);
        $prices = $this->enrolmentPriceService->get(
            ['branch', 'level', 'academicYear', 'grade'],
            $filters['branch_id'] ?? null
        );

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
        $academicYears = AcademicYear::orderByDesc('name')->get();
        return view('enrolment.price-form', compact('branches', 'academicYears'));
    }

    public function getRegistrationPrice($branchId, $levelId, Request $request)
    {
        $price = $this->enrolmentPriceService->getRegistrationPrice(
            $branchId,
            $levelId,
            $request->query('academic_year_id'),
            $request->query('grade_id')
        );
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
        $price = $this->validatedPrice($request);

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
        $academicYears = AcademicYear::orderByDesc('name')->get();
        $grades = Grade::where('level_id', $price->level_id)->orderBy('name')->get();
        $title = "Enrolment Price Form";
        return view('enrolment.price-form', compact('price', 'branches', 'levels', 'academicYears', 'grades', 'title'));
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
        $price = $this->validatedPrice($request, true);
        $this->enrolmentPriceService->put($price);
        return redirect('/enrolment/setting')->with('success', 'Enrolment price updated successfully.');
    }

    private function validatedPrice(Request $request, bool $updating = false): array
    {
        $rules = [
            'branch' => 'required|integer|exists:branches,id',
            'level' => 'required|integer|exists:levels,id',
            'academic_year_id' => 'nullable|integer|exists:academic_years,id',
            'grade_id' => 'nullable|integer|exists:grades,id',
            'type' => 'required|in:form,fee',
            'name' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean|nullable',
            'items' => 'required|array|min:1',
            'items.*.type' => ['required', 'string', 'max:64', 'alpha_dash', 'regex:/^[a-z][a-z0-9_-]*$/', 'distinct'],
            'items.*.name' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:0.01|max:9999999999.99',
            'items.*.is_required' => 'sometimes|boolean',
            'items.*.is_active' => 'sometimes|boolean',
        ];
        if ($updating) {
            $rules['id'] = 'required|exists:enrolment_prices,id';
        }

        $price = $request->validate($rules);
        $level = Level::findOrFail($price['level']);
        if ((int) $level->branch_id !== (int) $price['branch']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'level' => 'The selected level does not belong to the selected branch.',
            ]);
        }
        if (!empty($price['grade_id'])) {
            $grade = Grade::findOrFail($price['grade_id']);
            if ((int) $grade->level_id !== (int) $level->id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'grade_id' => 'The selected grade does not belong to the selected level.',
                ]);
            }
        }

        foreach ($price['items'] as &$item) {
            $item['amount'] = str_replace(',', '', (string) $item['amount']);
        }
        unset($item);

        if ($price['type'] === 'form' && !collect($price['items'])->contains(function ($item) {
            return $item['type'] === 'enrolment' && !empty($item['is_required']) && !empty($item['is_active']);
        })) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'items' => 'At least one active required Enrolment Fee item is required.',
            ]);
        }

        return $price;
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
