<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController as AppBase;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\UserService;
use App\Http\Services\ExamRegisterService;
use App\Consts;
use DB;
use Exception;
use Carbon\Carbon;

class ExamRegisterAPIController extends AppBase
{
    protected $examRegisterService;

    public function __construct(ExamRegisterService $examRegisterService)
    {
        $this->examRegisterService = $examRegisterService;
    }

    public function getSemesters(Request $request)
    {
        try {
            $data = $this->examRegisterService->getSemesters($request->all());
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getSemesterDetail(Request $request)
    {
        try {
            \Log::alert($request->id);
            $data = $this->examRegisterService->getSemesterDetail($request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }
}
