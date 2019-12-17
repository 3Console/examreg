<?php

namespace App\Http\Controllers\Admin;

use App\Consts;
use App\Http\Controllers\AppBaseController;
use App\Http\Services\AdminService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SemesterController extends AppBaseController
{
    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function getSemesters(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->getSemesters($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function createSemester(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->createSemester($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateSemester(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->updateSemester($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function removeSemester(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->removeSemester($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getSemester(Request $request)
    {
        try {
            $data = $this->adminService->getSemester($request->id);
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getSemesterClass(Request $request)
    {
        try {
            $input  = $request->all();
            $semesterId = $request->semesterId;
            $data   = $this->adminService->getSemesterClass($semesterId, $input);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function createSemesterClass(Request $request)
    {
        DB::beginTransaction();
        try {
            $input  = $request->all();
            $semesterId = $request->semesterId;
            $data = $this->adminService->createSemesterClass($semesterId, $input);
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateSemesterClass(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->updateSemesterClass($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function removeSemesterClass(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->removeSemesterClass($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }
}
