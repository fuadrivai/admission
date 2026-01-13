<?php

namespace App\Services\Implement;

use App\Models\Division;
use App\Models\Grade;
use App\Services\GradeService;

class GradeImplement implements GradeService
{
    public function get()
    {
        return Grade::with('level')->get();
    }

    public function show($id)
    {
        return Grade::with('level')->findOrFail($id);
    }

    public function post($data)
    {
        $division = Division::create([
            'name'     => $data['name'],
        ]);

        return $division->load('level');
    }

    public function put($data)
    {
        $division = Division::findOrFail($data['id']);

        $division->update([
            'name'     => $data['name'],
        ]);

        return $division->load('level');
    }

    public function delete($id)
    {
        $division = Division::findOrFail($id);
        return $division->delete();
    }
}
