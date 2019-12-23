<?php

namespace App\Http\Services;

use App\Consts;
use App\User;
use App\Models\UserClass;
use App\Models\UserSchedule;
use App\Models\Schedule;
use App\Models\UnitClass;
use App\Models\Shift;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Utils\BigNumber;
use Exception;
use App\Utils;

class UserService
{

    public function getProfile($userId)
    {
        return User::where('id', $userId)
                ->select('id', 'msv', 'username', 'full_name', 'dob', 'course', 'unit')
                ->first();
    }

    public function getUserSchedules($userId, $input)
    {
        return UserSchedule::join('schedules', 'schedules.id', 'user_schedules.schedule_id')
                            ->join('unit_classes', 'unit_classes.id', 'schedules.unit_class_id')
                            ->join('shifts', 'shifts.id', 'schedules.shift_id')
                            ->join('locations', 'locations.id', 'schedules.location_id')
                            ->where('user_schedules.user_id', $userId)
                            ->select('user_schedules.id', 'unit_classes.subject', 'schedules.date', 'locations.room', 
                                        'locations.address', 'shifts.start_time', 'shifts.end_time')
                            ->when(
                                !empty($input['sort']),
                                function ($query) use ($input) {
                                    $query->orderBy($input['sort'], $input['sort_type']);
                                }, function ($query) {
                                    $query->orderBy('user_schedules.id', 'asc');
                                }
                            )
                            ->when(!empty($input['searchKey']), function ($query) use ($input) {
                                $searchKey = $input['searchKey'];
                                return $query->where(function ($q) use ($searchKey) {
                                    return $q->where('unit_classes.subject', 'like', "%{$searchKey}%")
                                            ->orWhere('schedules.date', 'like', "%{$searchKey}%")
                                            ->orWhere('locations.room', 'like', "%{$searchKey}%")
                                            ->orWhere('locations.address', 'like', "%{$searchKey}%")
                                            ->orWhere('shifts.start_time', 'like', "%{$searchKey}%")
                                            ->orWhere('shifts.end_time', 'like', "%{$searchKey}%");

                                });
                            })
                            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function getScheduleDetail($id)

    {
        return UserSchedule::join('schedules', 'schedules.id', 'user_schedules.schedule_id')
                            ->join('unit_classes', 'unit_classes.id', 'schedules.unit_class_id')
                            ->join('shifts', 'shifts.id', 'schedules.shift_id')
                            ->join('locations', 'locations.id', 'schedules.location_id')
                            ->join('users', 'users.id', 'user_schedules.user_id')
                            ->where('user_schedules.id', $id)
                            ->select('user_schedules.id', 'unit_classes.subject', 'schedules.date', 'locations.room', 
                                        'locations.address', 'shifts.start_time', 'shifts.end_time', 'users.msv', 'users.full_name', 'users.dob'
                                        , 'users.course')
                            ->first();
    }
}
