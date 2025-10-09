<?php

namespace App\Services\Implement;

use App\Models\Branch;
use App\Services\BranchService;

class BranchImplement implements BranchService
{
    public function get()
    {
        return Branch::all();
    }

    public function show($id)
    {
        return Branch::findOrFail($id);
    }

    public function post($data)
    {
        // Simpan grade baru
        $branch = Branch::create([
            'name'     => $data['name'],
        ]);

        return $branch;
    }

    public function put($data)
    {
        $branch = Branch::findOrFail($data['id']);

        $branch->update([
            'name'     => $data['name'],
        ]);

        return $branch;
    }

    public function delete($id)
    {
        $branch = Branch::findOrFail($id);
        return $branch->delete();
    }
}
