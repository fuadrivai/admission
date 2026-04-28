<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EnrolmentService;
use Illuminate\Http\Request;


class EnrolmentApiController extends Controller
{
    private EnrolmentService $enrolmentService;

    public function __construct(EnrolmentService $enrolmentService)
    {
        $this->enrolmentService = $enrolmentService;
    }

    public function post(Request $request)
    {
        return $this->enrolmentService->postForm($request);
    }
}
