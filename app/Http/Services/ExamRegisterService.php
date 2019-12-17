<?php

namespace App\Http\Services;

use App\Consts;
use App\Models\Report;
use App\Models\UserConnectionHistory;
use App\Models\UserDeviceRegister;
use App\Models\UserBalance;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\UserFavourite;
use App\User;
use App\Models\UserClass;
use App\Models\UserSchedule;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\UnitClass;
use App\Models\Shift;
use App\Models\Location;
use Carbon\Carbon;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Utils\BigNumber;
use Exception;
use App\Models\UserBounty;
use App\Utils;
use App\Events\BalanceUpdated;
use App\Jobs\CalculateUserLevel;

class ExamRegisterService
{
    public function getSemesters()
    {
        return Semester::select('id', 'name', 'start_time', 'end_time', 'is_register')
                        ->get();
    }

    public function getSemesterDetail($id)
    {
        return Semester::where('id', $id)
                        ->select('start_time', 'end_time', 'is_register')
                        ->first();
    }
}
