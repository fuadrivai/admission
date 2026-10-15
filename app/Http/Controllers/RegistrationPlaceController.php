<?php

namespace App\Http\Controllers;

use App\Models\RegistrationPlace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationPlaceController extends Controller
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
        return view('setting.registration-place', ['title' => 'Registration Place']);
    }

    public function datatables()
    {
        return datatables()->of(RegistrationPlace::query())->make(true);
    }

    public function store(Request $request)
    {
        return $this->save($request, new RegistrationPlace());
    }

    public function update(Request $request, RegistrationPlace $registrationPlace)
    {
        return $this->save($request, $registrationPlace);
    }

    public function destroy(RegistrationPlace $registrationPlace)
    {
        $registrationPlace->delete();

        return response()->json(['message' => 'Registration place deleted']);
    }

    private function save(Request $request, RegistrationPlace $place)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('registration_places', 'code')->ignore($place->id)],
            'is_other' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_other'] = $request->boolean('is_other');
        $data['is_active'] = $request->boolean('is_active');

        $place->fill($data)->save();

        return response()->json(['message' => 'Registration place saved', 'data' => $place]);
    }
}
