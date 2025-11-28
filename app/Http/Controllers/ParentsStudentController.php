<?php

namespace App\Http\Controllers;

use App\Services\ParentsStudentService;
use Illuminate\Http\Request;

class ParentsStudentController extends Controller
{

    private ParentsStudentService $parentsServise;

    public function __construct(ParentsStudentService $parentsServise)
    {
        $this->parentsServise = $parentsServise;
    }

    public function getParentStudent(Request $request)
    {
        $validated = $request->validate([
            "username"=>"required",
            "password"=>"required",
            "branch"=>"required",
        ]);
        session(['school_branch' => $request->branch]);

        $parent = $this->parentsServise->get($validated);
        return $parent;
    }
}
