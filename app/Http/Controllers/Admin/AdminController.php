<?php

namespace App\Http\Controllers\Admin;

use App\Consts;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateNewOrUpdateAdministrator;
use App\Http\Services\MasterdataService;
use App\Http\Services\TransactionService;
use App\Http\Services\UserService;
use App\Http\Services\AdminService;
use App\User;
use Mail;
use App\Utils;
use App\Utils\BigNumber;
use App\Utils\SendSms;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PharIo\Manifest\Email;
use App\Http\Requests\MailFormRequest;
use App\Http\Requests\UploadCSVRequest;

class AdminController extends AppBaseController
{
    private $adminService;
    private $userService;

    public function __construct(AdminService $adminService, UserService $userService)
    {
        $this->adminService = $adminService;
        $this->userService = $userService;
    }

    public function index()
    {
        $dataVersion = MasterdataService::getDataVersion();
        return view('admin.app')->with('dataVersion', $dataVersion);
    }

    public function getUsers(Request $request)
    {
        $data = $this->adminService->getUsers($request->all());
        return $this->sendResponse($data);
    }

    public function getCurrentAdmin(Request $request)
    {
        try {
            $data   = $this->adminService->getCurrentAdmin();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getAdministrators(Request $request)
    {
        try {
            $input  = $request->all();
            $data   = $this->adminService->getAdministrators($input);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function createNewOrUpdateAdministrator(CreateNewOrUpdateAdministrator $request)
    {
        DB::beginTransaction();
        try {
            $input  = $request->all();
            $data   = $this->adminService->createNewOrUpdateAdministrator($input);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getAdministratorById(Request $request)
    {
        try {
            $adminId    = $request->id;
            $data       = $this->adminService->getAdministratorById($adminId);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function deleteAdministrator(Request $request)
    {
        DB::beginTransaction();
        try {
            $adminId    = $request->id;
            $data       = $this->adminService->deleteAdministrator($adminId);
            DB::commit();
            return $this->sendResponse('Ok');
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getUserTransactions(Request $request)
    {
        try {
            $input  = $request->all();
            $type = $request->type;
            $data   = $this->adminService->getUserTransactions($type, $input);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->updateUser($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
        }
    }

    public function getTemplateMails(Request $request)
    {
        DB::beginTransaction();
        try {
            $input  = $request->all();
            $data   = $this->adminService->getTemplateMails($input);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }

    public function getBounties(Request $request)
    {
        try {
            $input  = $request->all();
            $data   = $this->adminService->getBounties($input);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getUserBalances(Request $request) 
    {   
        try {
            $input  = $request->all();
            $data   = $this->adminService->getUserBalances($input);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function editTemplateMail(Request $request)
    {
        DB::beginTransaction();
        try {
            $data   = $this->adminService->editTemplateMail($request->id);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateUserBalance(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->adminService->updateUserBalance($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function createTemplateMail(MailFormRequest $request)
    {
        DB::beginTransaction();
        try {
            $data   = $this->adminService->createTemplateMail($request);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateTemplateMail(MailFormRequest $request)
    {
        DB::beginTransaction();
        try {
            $data   = $this->adminService->updateTemplateMail($request);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function deleteTemplateMail(Request $request)
    {
        DB::beginTransaction();
        try {
            $data   = $this->adminService->deleteTemplateMail($request->id);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function sendMails(Request $request)
    {
        DB::beginTransaction();
        try {
            $data   = $this->adminService->sendMails($request);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getMarketingMailsHistory(Request $request)
    {
        DB::beginTransaction();
        try {
            $data   = $this->adminService->getMarketingMailsHistory($request);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function resendMail(Request $request)
    {
        DB::beginTransaction();
        try {
            $data   = $this->adminService->resendMail($request->id);
            DB::commit();
            return $this->sendResponse($data);
        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }

    public function getUserBountyHistory(Request $request)
    {
        try {
            $input  = $request->all();
            $originalId = $request->original_id;
            $data   = $this->adminService->getUserBountyHistory($input, $originalId);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function getReport(Request $request)
    {
        try {
            $input  = $request->all();
            $data   = $this->adminService->getReport($input);
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }

    public function uploadCSV(UploadCSVRequest $request) {
        DB::beginTransaction();
        try {
            $data = $this->adminService->uploadCSV($request->all());
            DB::commit();
            return $this->sendResponse($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return $this->sendError($ex->getMessage());
        }
    }
}
