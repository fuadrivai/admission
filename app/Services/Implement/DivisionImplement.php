<?php

namespace App\Services\Implement;

use App\Models\Division;
use App\Services\DivisionService;

class DivisionImplement implements DivisionService
{
    public function get()
    {
        // Ambil semua grades dengan relasi level
        return Division::with('level')->get();
    }

    public function show($id)
    {
        // Detail grade by id
        return Division::with('level')->findOrFail($id);
    }

    public function post($data)
    {
        // Simpan grade baru
        $grade = Division::create([
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
