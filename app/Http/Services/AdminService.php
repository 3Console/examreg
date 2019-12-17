<?php

namespace App\Http\Services;

use App\Consts;
use App\User;
use App\Models\Semester;
use App\Models\SemesterClass;
use App\Models\UnitClass;
use App\Models\UserClass;
use App\Models\Schedule;
use App\Models\UserSchedule;
use App\Models\Shift;
use App\Models\Location;
use App\Models\Admin;
use App\Models\AdminPermission;
use App\Utils;
use Auth;
use Exception;
use Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\UserSessionTerminated;
use App\Events\MasterdataUpdated;

class AdminService {

    public function getCurrentAdmin()
    {
        return Auth::guard('admin')->user();
    }

    public function getAdministrators($input)
    {
        return Admin::when(!empty($input['search_key']), function ($query) use ($input) {
                $searchKey = $input['search_key'];
                return $query->where(function ($q) use ($searchKey) {
                    $q->where('email', 'like', '%' . $searchKey . '%')
                      ->orWhere('name', 'like', '%' . $searchKey . '%');
                });
            })
            ->when(
                !empty($input['sort']) && !empty($input['sort_type']),
                function ($query) use ($input) {
                    return $query->orderBy($input['sort'], $input['sort_type']);
                },
                function ($query) {
                    return $query->orderBy('created_at', 'asc');
                }
            )
            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function createNewOrUpdateAdministrator($input)
    {
        $admin = Admin::where('email', $input['email'])->first();
        if ($admin) {
            if ($admin->role === Consts::ROLE_SUPER_ADMIN) {
                throw new Exception('Do not privilege change info this account.');
            }
            $admin->name = $input['name'];
            if ($admin->isPasswordChanged($input['password'])) {
                $admin->password = bcrypt($input['password']);
            }
            $admin->save();
        } else {
            $admin = Admin::create([
                'name'      => $input['name'],
                'email'     => $input['email'],
                'password'  => bcrypt($input['password'])
            ]);
        }

        $permissionModels = [];
        foreach ($input['permissions'] as $permission) {
            $permissionModels[] = new AdminPermission([
                'admin_id'  => $admin->id,
                'name'      => $permission
            ]);
        }
        AdminPermission::where('admin_id', $admin->id)->delete();
        $admin->permissions()->saveMany($permissionModels);

        return $admin;
    }

    public function getAdministratorById($adminId)
    {
        $admin = Admin::select('id', 'name', 'email')
            ->findOrFail($adminId)
            ->load('permissions');
        $admin->hash = Consts::CHAR_PASSWORD_HIDDEN;
        return $admin;
    }

    public function deleteAdministrator($adminId)
    {
        $admin = Admin::findOrFail($adminId);
        if ($admin->role === Consts::ROLE_SUPER_ADMIN) {
            throw new Exception('Do not privilege change info this account.');
        }
        AdminPermission::where('admin_id', $adminId)->delete();
        $result = Admin::destroy($adminId);

        return $result;
    }

    public function getUsers($input)
    {
        return User::select('id', 'msv', 'full_name', 'username', 'email', 'unit')
                    ->when(
                        !empty($input['sort']) && !empty($input['sort_type']),
                        function ($query) use ($input) {
                            $query->orderBy($input['sort'], $input['sort_type']);
                        }, function ($query) {
                            $query->orderBy('id', 'asc');
                        }
                    )
                    ->when(!empty($input['search_key']), function ($query) use ($input) {
                        $searchKey = $input['search_key'];
                        return $query->where(function ($q) use ($searchKey) {
                            $q->where('full_name', 'like', '%' . $searchKey . '%')
                                ->orWhere('msv', 'like', '%' . $searchKey . '%')
                                ->orWhere('email', 'like', '%' . $searchKey . '%')
                                ->orWhere('username', 'like', '%' . $searchKey . '%')
                                ->orWhere('unit', 'like', '%' . $searchKey . '%');
                        });
                    })
                    ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }


    public function updateUser($input)
    {
        if (!in_array($input['status'], [Consts::USER_ACTIVE, Consts::USER_INACTIVE])) {
            throw new Exception('Status user is invalid.');
        }
        $user = User::findOrFail($input['id']);
        $status = $user->status;
        $user->level = $input['level'];
        $user->status = $input['status'];
        $user->save();

        if($status != $input['status']) {
            Mail::queue(new ChangeUserStatusMailQueue($user->email, $user->status, Consts::DEFAULT_LOCALE, $user->username));
            if ($input['status'] === Consts::USER_INACTIVE) {
                event(new UserSessionTerminated($user->id, [
                    'user_id' => $user->id,
                    'terminated' => Consts::TRUE
                ]));
            }
        }

        return $user;
    }

    public function uploadCSV($input)
    {
        $fileContent = Utils::getContentCsvFile($input['file'], Consts::TEMPLATE_USER);

        if (empty($fileContent)) {
            throw new Exception('The content file upload cannot blank.');
        }

        $separateChar = '_';

        $result = collect($fileContent)->map(function($item) use ($separateChar) {
            $item = (object) $item;
            $msgError = $this->validateRowData($item);
            if ($msgError) {
                $item->error = $msgError;
                return $item;
            }

            $sex = 0;
            if ($item->sex === "Male") {
                $sex = 1;
            }

            $data = [
                'id'                => $item->id,
                'email'             => $item->email,
                'password'          => bcrypt($item->password),
                'msv'               => $item->msv,
                'username'          => $item->username,
                'full_name'         => $item->full_name,
                'dob'               => $item->dob,
                'sex'               => $sex,
                'course'            => $item->course,
                'unit'              => $item->unit,
            ];

            User::create($data);

            return $item;
        });

        return $result;
    }

    private function validateRowData($item)
    {
        $errors = [];

        $errors = $this->validateNullOrEmpty($item, 'id', $errors);
        $errors = $this->validateNullOrEmpty($item, 'email', $errors);
        $errors = $this->validateNullOrEmpty($item, 'password', $errors);
        $errors = $this->validateNullOrEmpty($item, 'msv', $errors);
        $errors = $this->validateNullOrEmpty($item, 'username', $errors);
        $errors = $this->validateNullOrEmpty($item, 'full_name', $errors);
        $errors = $this->validateNullOrEmpty($item, 'dob', $errors);
        $errors = $this->validateNullOrEmpty($item, 'sex', $errors);
        $errors = $this->validateNullOrEmpty($item, 'course', $errors);
        $errors = $this->validateNullOrEmpty($item, 'unit', $errors);

        if (empty($errors)) {
            return null;
        }
        return join(Consts::CHAR_SPACE, $errors);
    }

    private function validateNullOrEmpty($item, $field, $errors, $aliasField = null)
    {
        if (empty($item) || empty($item->{$field})) {
            $fieldError = $aliasField ?? $field;
            $errors[] = "The $fieldError cannot blank or invalid.";
        }
        return $errors;
    }

    public function getSemesters($input)
    {
        return Semester::select('id', 'name', 'start_time', 'end_time', 'is_register')
                    ->when(
                        !empty($input['sort']) && !empty($input['sort_type']),
                        function ($query) use ($input) {
                            $query->orderBy($input['sort'], $input['sort_type']);
                        }, function ($query) {
                            $query->orderBy('id', 'asc');
                        }
                    )
                    ->when(!empty($input['search_key']), function ($query) use ($input) {
                        $searchKey = $input['search_key'];
                        return $query->where(function ($q) use ($searchKey) {
                            $q->where('name', 'like', '%' . $searchKey . '%')
                                ->orWhere('is_register', 'like', '%' . $searchKey . '%');
                        });
                    })
                    ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function createSemester($input)
    {
        $semester = Semester::create([
            'name' => $input['name'],
            'start_time' => $input['start_time'],
            'end_time' => $input['end_time'],
            'is_register' => $input['is_register'],
        ]);

        return $semester;
    }

    public function updateSemester($input)
    {

        $semester = Semester::findOrFail($input['id']);

        if (array_key_exists('name', $input)) {
            $semester->name = $input['name'];
        }
        if (array_key_exists('start_time', $input)) {
            $semester->start_time = $input['start_time'];
        }
        if (array_key_exists('end_time', $input)) {
            $semester->end_time = $input['end_time'];
        }
        if (array_key_exists('is_register', $input)) {
            $semester->is_register = $input['is_register'];
        }

        $semester->save();

        return $semester;
    }

    public function removeSemester($input)
    {
        $semester = Semester::findOrFail($input['id'])->delete();
        return $semester;
    }

    public function getSemester($semesterId)
    {
        return Semester::where('id', $semesterId)
                        ->select('name')
                        ->first();
    }

    public function getSemesterClass($semesterId, $input)
    {
        return SemesterClass::join('unit_classes', 'unit_classes.id', 'semester_classes.unit_class_id')
                            ->where('semester_classes.semester_id', $semesterId)
                            ->select('semester_classes.id', 'semester_classes.unit_class_id', 'unit_classes.subject', 'unit_classes.class_code',
                                'unit_classes.lecturer')
                            ->when(
                                !empty($input['sort']) && !empty($input['sort_type']),
                                function ($query) use ($input) {
                                    $query->orderBy($input['sort'], $input['sort_type']);
                                }, function ($query) {
                                    $query->orderBy('semester_classes.unit_class_id', 'asc');
                                }
                            )
                            ->when(!empty($input['search_key']), function ($query) use ($input) {
                                $searchKey = $input['search_key'];
                                return $query->where(function ($q) use ($searchKey) {
                                    $q->where('unit_classes.subject', 'like', '%' . $searchKey . '%')
                                        ->orWhere('unit_classes.class_code', 'like', '%' . $searchKey . '%')
                                        ->orWhere('unit_classes.lecturer', 'like', '%' . $searchKey . '%');
                                });
                            })
                            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function createSemesterClass($semesterId, $input)
    {
        $data = [
            'semester_id' => $semesterId,
            'unit_class_id' => $input['unit_class_id'],
        ];

        $exist = $this->checkExistClass($semesterId, $data['unit_class_id']);

        if ($exist === 1) {
            throw new Exception("This class is duplicated. Please check again on your data.");
        }

        SemesterClass::create($data);

        return $data;
    }

    public function updateSemesterClass($input)
    {

        $class = SemesterClass::findOrFail($input['id']);

        if (array_key_exists('unit_class_id', $input)) {
            $class->unit_class_id = $input['unit_class_id'];
        }

        $class->save();

        return $class;
    }

    public function removeSemesterClass($input)
    {
        $class = SemesterClass::findOrFail($input['id'])->delete();
        return $class;
    }

    public function getClasses($input)
    {
        return UnitClass::select('id', 'subject', 'class_code', 'lecturer')
                    ->when(
                        !empty($input['sort']) && !empty($input['sort_type']),
                        function ($query) use ($input) {
                            $query->orderBy($input['sort'], $input['sort_type']);
                        }, function ($query) {
                            $query->orderBy('id', 'asc');
                        }
                    )
                    ->when(!empty($input['search_key']), function ($query) use ($input) {
                        $searchKey = $input['search_key'];
                        return $query->where(function ($q) use ($searchKey) {
                            $q->where('subject', 'like', '%' . $searchKey . '%')
                                ->orWhere('class_code', 'like', '%' . $searchKey . '%')
                                ->orWhere('lecturer', 'like', '%' . $searchKey . '%');
                        });
                    })
                    ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function getAllClass()
    {
        return UnitClass::select('id', 'subject', 'class_code', 'lecturer')->get();
    }

    public function createClass($input)
    {
        $class = UnitClass::create([
            'subject' => $input['subject'],
            'class_code' => $input['class_code'],
            'lecturer' => $input['lecturer'],
        ]);

        return $class;
    }

    public function updateClass($input)
    {

        $class = UnitClass::findOrFail($input['id']);

        if (array_key_exists('subject', $input)) {
            $class->subject = $input['subject'];
        }
        if (array_key_exists('class_code', $input)) {
            $class->class_code = $input['class_code'];
        }
        if (array_key_exists('lecturer', $input)) {
            $class->lecturer = $input['lecturer'];
        }

        $class->save();

        return $class;
    }

    public function removeClass($input)
    {
        $class = UnitClass::findOrFail($input['id'])->delete();
        return $class;
    }

    public function getUnitClass($classId)
    {
        return UnitClass::where('id', $classId)
                        ->select('subject', 'class_code')
                        ->first();
    }

    public function getUserClass($classId, $input)
    {
        return UserClass::join('users', 'users.id', 'user_classes.user_id')
                        ->where('user_classes.unit_class_id', $classId)
                        ->select('user_classes.id', 'users.full_name', 'user_classes.is_valid')
                        ->when(
                            !empty($input['sort']) && !empty($input['sort_type']),
                            function ($query) use ($input) {
                                $query->orderBy($input['sort'], $input['sort_type']);
                            }, function ($query) {
                                $query->orderBy('users.full_name', 'asc');
                            }
                        )
                        ->when(!empty($input['search_key']), function ($query) use ($input) {
                            $searchKey = $input['search_key'];
                            return $query->where(function ($q) use ($searchKey) {
                                $q->where('users.full_name', 'like', '%' . $searchKey . '%');
                            });
                        })
                        ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    private function findIdByName($name)
    {
        $user = User::where('full_name', $name)
                    ->select('id')
                    ->first();

        if (empty($user)) {
            throw new Exception("This user doesn't in the system");
        }
        else return $user->id;
    }

    private function findIdByMSV($msv)
    {
        $user = User::where('msv', $msv)
                    ->select('id')
                    ->first();

        if (empty($user)) {
            throw new Exception("This user doesn't in the system");
        }
        else return $user->id;
    }

    public function uploadStudentCSV($input)
    {

        $fileContent = Utils::getContentCsvFile($input['file'], Consts::TEMPLATE_USER_CLASS);
        $classId = $input['classId'];
        if (empty($fileContent)) {
            throw new Exception('The content file upload cannot blank.');
        }

        $separateChar = '_';

        $result = collect($fileContent)->map(function($item) use ($separateChar, $classId) {
            $item = (object) $item;
            $msgError = $this->validateRowDataStudent($item);
            if ($msgError) {
                $item->error = $msgError;
                return $item;
            }

            $is_valid = 0;
            if ($item->is_valid === "Qualified") {
                $is_valid = 1;
            }

            $data = [
                'user_id' => $this->findIdByMSV($item->msv),
                'unit_class_id' => $classId,
                'is_valid' => $is_valid,
            ];

            $exist = $this->checkExistStudent($classId, $data['user_id']);

            if ($exist === 1) {
                $item->error = 'This student is duplicated. Please check again on your data.';
                return $item;
            }

            UserClass::create($data);

            return $item;
        });

        return $result;
    }

    private function validateRowDataStudent($item)
    {
        $errors = [];

        $errors = $this->validateNullOrEmpty($item, 'id', $errors);
        $errors = $this->validateNullOrEmpty($item, 'msv', $errors);
        $errors = $this->validateNullOrEmpty($item, 'full_name', $errors);
        $errors = $this->validateNullOrEmpty($item, 'is_valid', $errors);

        if (empty($errors)) {
            return null;
        }
        return join(Consts::CHAR_SPACE, $errors);
    }

    private function checkExistStudent($classId, $student)
    {
        $userId = UserClass::where('unit_class_id', $classId)
                        ->select('user_id')
                        ->get();

        $check = 0;
        foreach ($userId as $key => $value) {
            if ($student === $value->user_id) {
                $check = 1;
            }
        }

        return $check;
    }

    private function checkExistClass($semesterId, $class)
    {
        $classId = SemesterClass::where('semester_id', $semesterId)
                        ->select('unit_class_id')
                        ->get();

        $check = 0;
        foreach ($classId as $key => $value) {
            if ($class === $value->unit_class_id) {
                $check = 1;
            }
        }

        return $check;
    }

    public function createStudent($classId, $input)
    {
        $data = [
            'user_id' => $this->findIdByName($input['full_name']),
            'unit_class_id' => $classId,
            'is_valid' => $input['is_valid'],
        ];

        $exist = $this->checkExistStudent($classId, $data['user_id']);

        if ($exist === 1) {
            throw new Exception("This student is duplicated. Please check again on your data.");
        }

        UserClass::create($data);

        return $data;
    }

    public function updateStudent($input)
    {

        $student = UserClass::findOrFail($input['id']);

        if (array_key_exists('full_name', $input)) {
            $student->user_id = $this->findIdByName($input['full_name']);
        }
        if (array_key_exists('is_valid', $input)) {
            $student->is_valid = $input['is_valid'];
        }

        $student->save();

        return $student;
    }

    public function removeStudent($input)
    {
        $student = UserClass::findOrFail($input['id'])->delete();
        return $student;
    }

    public function getSchedules($input)
    {
        return Schedule::join('unit_classes', 'unit_classes.id', 'schedules.unit_class_id')
                        ->join('shifts', 'shifts.id', 'schedules.shift_id')
                        ->join('locations', 'locations.id', 'schedules.location_id')
                        ->select('schedules.id', 'unit_classes.id as unit_class_id', 'unit_classes.subject', 'unit_classes.class_code', 'shifts.id as shift_id','shifts.start_time', 'shifts.end_time', 'locations.id as location_id','locations.room', 'locations.address', 'schedules.date')
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
                                $q->where('unit_classes.subject', 'like', '%' . $searchKey . '%')
                                    ->orWhere('unit_classes.class_code', 'like', '%' . $searchKey . '%')
                                    ->orWhere('shifts.start_time', 'like', '%' . $searchKey . '%')
                                    ->orWhere('shifts.end_time', 'like', '%' . $searchKey . '%')
                                    ->orWhere('locations.room', 'like', '%' . $searchKey . '%')
                                    ->orWhere('ocations.address', 'like', '%' . $searchKey . '%')
                                    ->orWhere('schedules.date', 'like', '%' . $searchKey . '%');
                            });
                        })
                        ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function createSchedule($input)
    {
        $schedule = Schedule::create([
            'unit_class_id' => $input['unit_class_id'],
            'shift_id' => $input['shift_id'],
            'location_id' => $input['location_id'],
            'date' => $input['date']
        ]);

        return $schedule;
    }

    public function updateSchedule($input)
    {

        $schedule = Schedule::findOrFail($input['id']);

        if (array_key_exists('unit_class_id', $input)) {
            $schedule->unit_class_id = $input['unit_class_id'];
        }
        if (array_key_exists('shift_id', $input)) {
            $schedule->shift_id = $input['shift_id'];
        }
        if (array_key_exists('location_id', $input)) {
            $schedule->location_id = $input['location_id'];
        }
        if (array_key_exists('date', $input)) {
            $schedule->date = $input['date'];
        }

        $schedule->save();

        return $schedule;
    }

    public function removeSchedule($input)
    {
        $schedule = Schedule::findOrFail($input['id'])->delete();
        return $schedule;
    }

    public function getSchedule($id)
    {
        return Schedule::join('unit_classes', 'unit_classes.id', 'schedules.unit_class_id')
                        ->join('shifts', 'shifts.id', 'schedules.shift_id')
                        ->join('locations', 'locations.id', 'schedules.location_id')
                        ->where('schedules.id', $id)
                        ->select('schedules.id', 'unit_classes.id as unit_class_id', 'unit_classes.subject', 'unit_classes.class_code',
                            'unit_classes.lecturer', 'shifts.id as shift_id','shifts.start_time', 'shifts.end_time',
                            'locations.id as location_id','locations.room', 'locations.address', 'schedules.date')
                        ->first();
    }

    public function getStudents($id)
    {
        return UserSchedule::join('users', 'users.id', 'user_schedules.user_id')
                            ->where('user_schedules.schedule_id', $id)
                            ->select('users.id', 'users.msv', 'users.full_name', 'users.dob', 'users.course')
                            ->orderBy('users.msv','asc')
                            ->get();
    }

    public function getShifts($input)
    {
        return Shift::select('id', 'start_time', 'end_time')
                    ->when(
                        !empty($input['sort']) && !empty($input['sort_type']),
                        function ($query) use ($input) {
                            $query->orderBy($input['sort'], $input['sort_type']);
                        }, function ($query) {
                            $query->orderBy('id', 'asc');
                        }
                    )
                    ->when(!empty($input['search_key']), function ($query) use ($input) {
                        $searchKey = $input['search_key'];
                        return $query->where(function ($q) use ($searchKey) {
                            $q->where('start_time', 'like', '%' . $searchKey . '%')
                                ->orWhere('end_time', 'like', '%' . $searchKey . '%');
                        });
                    })
                    ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function getAllShift()
    {
        return Shift::select('id', 'start_time', 'end_time')->get();
    }

    public function createShift($input)
    {
        $shift = Shift::create([
            'start_time' => $input['start_time'],
            'end_time' => $input['end_time'],
        ]);

        return $shift;
    }

    public function updateShift($input)
    {

        $shift = Shift::findOrFail($input['id']);

        if (array_key_exists('start_time', $input)) {
            $shift->start_time = $input['start_time'];
        }
        if (array_key_exists('end_time', $input)) {
            $shift->end_time = $input['end_time'];
        }

        $shift->save();

        return $shift;
    }

    public function removeShift($input)
    {
        $shift = Shift::findOrFail($input['id'])->delete();
        return $shift;
    }

    public function getLocations($input)
    {
        return Location::select('id', 'room', 'address', 'slot')
                    ->when(
                        !empty($input['sort']) && !empty($input['sort_type']),
                        function ($query) use ($input) {
                            $query->orderBy($input['sort'], $input['sort_type']);
                        }, function ($query) {
                            $query->orderBy('id', 'asc');
                        }
                    )
                    ->when(!empty($input['search_key']), function ($query) use ($input) {
                        $searchKey = $input['search_key'];
                        return $query->where(function ($q) use ($searchKey) {
                            $q->where('room', 'like', '%' . $searchKey . '%')
                                ->orWhere('address', 'like', '%' . $searchKey . '%')
                                ->orWhere('slot', 'like', '%' . $searchKey . '%');
                        });
                    })
                    ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function getAllLocation()
    {
        return Location::select('id', 'room', 'address', 'slot')->get();
    }

    public function createLocation($input)
    {
        $location = Location::create([
            'room' => $input['room'],
            'address' => $input['address'],
            'slot' => $input['slot'],
        ]);

        return $location;
    }

    public function updateLocation($input)
    {

        $location = Location::findOrFail($input['id']);

        if (array_key_exists('room', $input)) {
            $location->room = $input['room'];
        }
        if (array_key_exists('address', $input)) {
            $location->address = $input['address'];
        }
        if (array_key_exists('slot', $input)) {
            $location->slot = $input['slot'];
        }

        $location->save();

        return $location;
    }

    public function removeLocation($input)
    {
        $location = Location::findOrFail($input['id'])->delete();
        return $location;
    }
}
