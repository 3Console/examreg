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
            $data = $this->examRegisterService->getSemesterDetail($request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getAllSemesterClass(Request $request)
    {
        try {
            $data = $this->examRegisterService->getAllSemesterClass($request->all());
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getAllUserClass(Request $request)
    {
        try {
            $data = $this->examRegisterService->getAllUserClass(Auth::id(), $request->all());
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function checkUserClass(Request $request)
    {
        try {
            $data = $this->examRegisterService->checkUserClass(Auth::id(), $request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function checkUserSchedule(Request $request)
    {
        try {
            $data = $this->examRegisterService->checkUserSchedule(Auth::id(), $request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function checkSchedule(Request $request)
    {
        try {
            $data = $this->examRegisterService->checkSchedule(Auth::id(), $request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function countSlot(Request $request)
    {
        try {
            $data = $this->examRegisterService->countSlot($request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getUserStatus(Request $request)
    {
        try {
            $data = $this->examRegisterService->getUserStatus(Auth::id(), $request->id);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getClassSchedule(Request $request)
    {
        try {
            $data = $this->examRegisterService->getClassSchedule($request->all());
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function submitSchedule(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->examRegisterService->submitSchedule(Auth::id(), $request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function cancelSchedule(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->examRegisterService->cancelSchedule(Auth::id(), $request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }
}
