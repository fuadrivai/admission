<?php

namespace App\Services\Implement;

use App\Models\Level;
use App\Models\Prospects;
use App\Services\ProspectService;


class ProspectImplement implements ProspectService
{
    public function get($with= [])
    {
        return Prospects::with($with)->get();
    }

    public function show($id,$with=[])
    {
        return Prospects::with($with)-> findOrFail($id);
    }
    
    public function getbyCode($code, $with=[])
    {
        $prospect = Prospects::where('code', $code)->first();

        if (!$prospect) {
            return response()->json([
                'status' => false,
                'message' => 'Code not found or invalid.'
            ], 404);
        }

        return $prospect;
    }

    public function post($data)
    {
        $prospect = Prospects::create([
            'code'          => $data['code'],
            'child_name'    => $data['child_name'],
            'date_of_birth' => $data['date_of_birth'],
            'place_of_birth'=> $data['place_of_birth'],
            'current_school'=> $data['current_school'],
            'parent_name'   => $data['parent_name'],
            'email'         => $data['email'],
            'phone_number'  => $data['phone_number'],
            'zipcode'       => $data['zipcode'],
            'address'       => $data['address'],
            'relationship'  => $data['relationship'],
            'source_module' =>  $data['source_module'],
        ]);

        return $prospect;
    }

    public function put($data)
    {
        $prospect = Prospects::findOrFail($data['id']);

        $prospect->update([
            'name'     => $data['name'],
        ]);

        return $prospect;
    }

    public function delete($id)
    {
        $prospect = Prospects::findOrFail($id);
        return $prospect->delete();
    }
}
