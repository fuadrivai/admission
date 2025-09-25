<?php

namespace App\Services\Implement;

use App\Models\Division;
use App\Services\DivisionService;

class DivisionImplement implements DivisionService
{
    public function get()
    {
        return Division::with('levels')->get();
    }

    public function show($id)
    {
        return Division::with('levels')->findOrFail($id);
    }

    public function post($data)
    {
        // Simpan grade baru
        $divsision = Division::create([
            'name'     => $data['name'],
        ]);

        return $divsision->load('levels');
    }

    public function put($data)
    {
        $division = Division::findOrFail($data['id']);

        $division->update([
            'name'     => $data['name'],
        ]);

        return $division->load('levels');
    }

    public function delete($id)
    {
        $division = Division::findOrFail($id);
        return $division->delete();
    }
}
