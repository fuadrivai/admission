<?php

namespace App\Services\Implement;

use App\Models\AcademicYear;
use App\Services\AcademicYearService;

class AcademicYearImplement implements AcademicYearService
{
    public function get()
    {
        return AcademicYear::all();
    }
    public function getActive()
    {
        return AcademicYear::where('is_active', 1)->get();
    }
    public function show($id)
    {
        return AcademicYear::findOrFail($id);
    }

    public function post($data)
    {
        $academicYear = AcademicYear::create([
            'name' => $data['name'],
            'is_active' => $data['is_active'] == "true" ? 1 : 0,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ]);

        return $academicYear;
    }

    public function put($data)
    {
        $academicYear = AcademicYear::findOrFail($data['id']);
        $academicYear->update([
            'name'     => $data['name'],
            'is_active'     => $data['is_active'] == "true" ? 1 : 0,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ]);

        return $academicYear;
    }

    public function delete($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        return $academicYear->delete();
    }
}
