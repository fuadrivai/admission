<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\XenditCallBackService;
use Illuminate\Http\Request;


class XenditCallBackApiController extends Controller
{
    private XenditCallBackService $xenditCallbackService;

    public function __construct(XenditCallBackService $xenditCallbackService)
    {
        $this->xenditCallbackService = $xenditCallbackService;
    }

    public function handleCallback(Request $request)
    {
        return $this->xenditCallbackService->post($request);
    }
}
