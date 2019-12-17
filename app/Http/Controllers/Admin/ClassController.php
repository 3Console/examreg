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
use App\Http\Requests\UploadCSVRequest;

class ClassController extends AppBaseController
{
    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function getClasses(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->getClasses($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getAllClass(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->getAllClass($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function createClass(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->createClass($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateClass(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->updateClass($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function removeClass(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->removeClass($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getUnitClass(Request $request)
    {
        try {
            $data = $this->adminService->getUnitClass($request->id);
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getUserClass(Request $request)
    {
        try {
            $input  = $request->all();
            $classId = $request->classId;
            $data   = $this->adminService->getUserClass($classId, $input);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function createStudent(Request $request)
    {
        DB::beginTransaction();
        try {
            $input  = $request->all();
            $classId = $request->classId;
            $data = $this->adminService->createStudent($classId, $input);
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateStudent(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->updateStudent($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function removeStudent(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->removeStudent($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function uploadStudentCSV(UploadCSVRequest $request) {
        DB::beginTransaction();
        try {
            $data = $this->adminService->uploadStudentCSV($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }
}
