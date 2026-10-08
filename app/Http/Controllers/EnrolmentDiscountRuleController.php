<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\EnrolmentDiscountRule;
use App\Models\RegistrationPlace;
use Illuminate\Http\Request;

class EnrolmentDiscountRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_if(auth()->user()->role === 'user', 403);

            return $next($request);
        });
    }

    public function index()
    {
        return view('setting.discount-rule', [
            'title' => 'Discount Rules',
            'places' => RegistrationPlace::orderBy('name')->get(['code', 'name']),
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function datatables()
    {
        $rules = EnrolmentDiscountRule::query()->get()->map(function ($rule) {
            $row = $rule->toArray();
            $row['used'] = $rule->usedCount();

            return $row;
        });

        return datatables()->of($rules)->make(true);
    }

    public function store(Request $request)
    {
        return $this->save($request, new EnrolmentDiscountRule());
    }

    public function update(Request $request, EnrolmentDiscountRule $discountRule)
    {
        return $this->save($request, $discountRule);
    }

    public function destroy(EnrolmentDiscountRule $discountRule)
    {
        $discountRule->delete();

        return response()->json(['message' => 'Discount rule deleted']);
    }

    private function save(Request $request, EnrolmentDiscountRule $rule)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'registration_place_code' => 'required|exists:registration_places,code',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'applies_to_type' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_-]*$/'],
            'percentage' => 'required|numeric|min:0.01|max:100',
            'quota' => 'nullable|integer|min:1',
            'valid_date' => 'nullable|date',
        ]);
        $data['requires_dp'] = $request->boolean('requires_dp');
        $data['is_active'] = $request->boolean('is_active');
        $data['branch_id'] = $data['branch_id'] ?? null;
        $data['quota'] = $data['quota'] ?? null;
        $data['valid_date'] = $data['valid_date'] ?? null;

        $rule->fill($data)->save();

        return response()->json(['message' => 'Discount rule saved', 'data' => $rule]);
    }
}
