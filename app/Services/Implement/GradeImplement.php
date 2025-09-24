<?php

namespace App\Services\Implement;

use App\Models\Grade;
use App\Services\GradeService;

class GradeImplement implements GradeService
{
    public function get()
    {
        // Ambil semua grades dengan relasi level
        return Grade::with('level')->get();
    }

    public function show($id)
    {
        // Detail grade by id
        return Grade::with('level')->findOrFail($id);
    }

    public function post($data)
    {
        // Simpan grade baru
        $grade = Grade::create([
            'level_id' => $data['level_id'],
            'name'     => $data['name'],
        ]);

        return $grade->load('level');
    }

    public function put($data)
    {
        $grade = Grade::findOrFail($data['id']);

        $grade->update([
            'level_id' => $data['level_id'],
            'name'     => $data['name'],
        ]);

        return $grade->load('level');
    }

    public function delete($id)
    {
        $grade = Grade::findOrFail($id);
        return $grade->delete();
    }
}
