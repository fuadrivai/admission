<?php

namespace App\Services\Implement;

use App\Models\Branch;
use App\Services\BranchService;

class BranchImplement implements BranchService
{
    public function get($with= [])
    {
        return Branch::with($with)->get();
    }

    public function show($id,$with=[])
    {
        return Branch::with($with)-> findOrFail($id);
    }

    public function post($data)
    {
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
