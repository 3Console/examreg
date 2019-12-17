<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController as AppBase;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\UserService;
use App\Consts;
use DB;
use Exception;
use Carbon\Carbon;

class UserAPIController extends AppBase
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getProfile(Request $request)
    {
        try {
            $data = $this->userService->getProfile(Auth::id());
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getUserSchedules(Request $request)
    {
        try {
            $data = $this->userService->getUserSchedules(Auth::id(), $request->all());
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getScheduleDetail(Request $request)
    {
        try {
            $data = $this->userService->getScheduleDetail($request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }
}
