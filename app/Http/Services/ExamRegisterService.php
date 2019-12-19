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
use App\Models\SemesterClass;
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

    public function getAllSemesterClass($input)
    {
        return SemesterClass::join('unit_classes', 'unit_classes.id', 'semester_classes.unit_class_id')
                            ->where('semester_classes.semester_id', $input['id'])
                            ->select('semester_classes.id', 'unit_classes.id as class_id', 'unit_classes.subject')
                            ->when(
                                !empty($input['sort']) && !empty($input['sort_type']),
                                function ($query) use ($input) {
                                    $query->orderBy($input['sort'], $input['sort_type']);
                                }, function ($query) {
                                    $query->orderBy('semester_classes.id', 'asc');
                                }
                            )
                            ->when(!empty($input['search_key']), function ($query) use ($input) {
                                $searchKey = $input['search_key'];
                                return $query->where(function ($q) use ($searchKey) {
                                    $q->where('unit_classes.subject', 'like', '%' . $searchKey . '%');
                                });
                            })
                            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function getAllUserClass($userId, $input)
    {
        return SemesterClass::join('unit_classes', 'unit_classes.id', 'semester_classes.unit_class_id')
                            ->join('user_classes', 'user_classes.unit_class_id', 'unit_classes.id')
                            ->where('semester_classes.semester_id', $input['id'])
                            ->where('user_classes.user_id', $userId)
                            ->select('semester_classes.id', 'unit_classes.id as class_id', 'unit_classes.subject')
                            ->when(
                                !empty($input['sort']) && !empty($input['sort_type']),
                                function ($query) use ($input) {
                                    $query->orderBy($input['sort'], $input['sort_type']);
                                }, function ($query) {
                                    $query->orderBy('semester_classes.id', 'asc');
                                }
                            )
                            ->when(!empty($input['search_key']), function ($query) use ($input) {
                                $searchKey = $input['search_key'];
                                return $query->where(function ($q) use ($searchKey) {
                                    $q->where('unit_classes.subject', 'like', '%' . $searchKey . '%');
                                });
                            })
                            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function checkUserClass($userId, $classId)
    {
        $class = UserClass::where('user_id', $userId)
                            ->select('unit_class_id')
                            ->get();

        $check = 0;

        foreach ($class as $key => $value) {
            if ($classId == $value->unit_class_id) {
                $check = 1;
            }
        }

        return $check;
    }

    public function checkUserSchedule($userId, $classId)
    {
        $class = UserSchedule::join('schedules', 'schedules.id', 'user_schedules.schedule_id')
                                ->where('user_schedules.user_id', $userId)
                                ->select('schedules.unit_class_id')
                                ->get();
        $check = 0;

        foreach ($class as $key => $value) {
            if ($classId == $value->unit_class_id) {
                $check = 1;
            }
        }

        return $check;
    }

    public function checkSchedule($userId, $scheduleId)
    {
        $schedule = UserSchedule::where('user_id', $userId)
                            ->where('schedule_id', $scheduleId)
                            ->select('id')
                            ->first();

        $check = 0;
        if (!empty($schedule)) {
            $check = 1;
        }

        return $check;
    }

    public function countSlot($scheduleId)
    {
        $count = UserSchedule::where('schedule_id', $scheduleId)->count();
        return $count;
    }

    public function getUserStatus($userId, $classId)
    {
        return UserClass::where('user_id', $userId)
                        ->where('unit_class_id', $classId)
                        ->select('is_valid')
                        ->first();
    }

    public function getClassSchedule($input)
    {
        return Schedule::join('shifts', 'shifts.id', 'schedules.shift_id')
                        ->join('locations', 'locations.id', 'schedules.location_id')
                        ->where('schedules.unit_class_id', $input['id'])
                        ->select('schedules.id', 'schedules.date', 'shifts.start_time', 'shifts.end_time', 'locations.room', 'locations.address',
                                    'locations.slot')
                        ->when(
                            !empty($input['sort']) && !empty($input['sort_type']),
                            function ($query) use ($input) {
                                $query->orderBy($input['sort'], $input['sort_type']);
                            }, function ($query) {
                                $query->orderBy('schedules.id', 'asc');
                            }
                        )
                        ->when(!empty($input['search_key']), function ($query) use ($input) {
                            $searchKey = $input['search_key'];
                            return $query->where(function ($q) use ($searchKey) {
                                $q->where('schedules.date', 'like', '%' . $searchKey . '%')
                                    ->orWhere('shifts.start_time', 'like', '%' . $searchKey . '%')
                                    ->orWhere('shifts.end_time', 'like', '%' . $searchKey . '%')
                                    ->orWhere('locations.room', 'like', '%' . $searchKey . '%')
                                    ->orWhere('locations.address', 'like', '%' . $searchKey . '%')
                                    ->orWhere('locations.slot', 'like', '%' . $searchKey . '%');
                            });
                        })
                        ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function submitSchedule($userId, $input)
    {
        $userSchedule = UserSchedule::create([
            'user_id' => $userId,
            'schedule_id' => $input['id'],
        ]);

        return $userSchedule;
    }

    public function cancelSchedule($userId, $input)
    {
        $schedule = UserSchedule::where('user_id', $userId)
                            ->where('schedule_id', $input['id'])
                            ->select('id')
                            ->first();

        $result = UserSchedule::findOrFail($schedule->id)->delete();
        return $result;
    }
}
