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

class UserService
{
    const CACHE_LIVE_TIME = 5; // minutes

    public function getCurrentDevice($name, $userId = null) {
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
        $deviceDetector = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
        $deviceDetector->parse();

        $device = new UserDeviceRegister;
        $device->user_id = $userId ?? Auth::id();
        $device->kind =  $deviceDetector->getDeviceName();
        $device->name = $name;
        $device->platform = $deviceDetector->getClient()['name'] . " " . $deviceDetector->getClient()['version'];
        $device->operating_system = $deviceDetector->getOs()['name']  . " " .  $deviceDetector->getOs()['version'];

        $payload = [$device->user_id, $device->kind, $device->platform, $device->operating_system];
        $device->user_device_identify = base64url_encode(implode('_', $payload));

        $existedDevice = UserDeviceRegister::where('user_device_identify', $device->user_device_identify)->first();

        if ($existedDevice) {
            return $existedDevice;
        }

        $device->save();

        return $device;
    }

    public function addMoreBalance($userId, $amount, $currency = 'usd')
    {
        $this->updateBalance($userId, Consts::TRUE, $amount, $currency);
    }

    public function subtractBalance($userId, $amount, $currency = 'usd')
    {
        $this->updateBalance($userId, Consts::FALSE, $amount, $currency);
    }

    private function updateBalance($userId, $isAddition = false, $amount, $currency = 'usd')
    {
        $userBalance = $this->getUserBalanceAndLock($userId);
        if (empty($userBalance)) {
            throw new Exception(__('user.invalid_balance'));
        }
        $newBalance = BigNumber::new($userBalance->balance)->sub($amount)->toString();
        if ($isAddition) {
            $newBalance = BigNumber::new($userBalance->balance)->add($amount)->toString();
        }

        // $data = [
        //     'balance' => $newBalance
        // ];
        // DB::table("{$currency}_account")->where('id', $userId)->update($data);

        $userBalance->balance = $newBalance;
        $userBalance->save();


        event(new BalanceUpdated($userId, [ 'balance' => $newBalance ]));
    }

    public function getUserBalanceAndLock($userId, $currency = 'usd')
    {
        return UserBalance::where('id', $userId)
            ->lockForUpdate()
            ->first();
    }

    public function createNewUserBalance($userId)
    {
        return UserBalance::insert([
            'id'            => $userId,
            'balance'       => 0,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now()
        ]);
    }

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

    public function getGamersLadder($input, $isLoadRelationData = true)
    {
        $userId    = Auth::id();
        return  User::when(!empty($userId), function ($query) use ($userId) {
                return $query->where('id', '<>', $userId);
            })
            ->when(!empty($input['rating']), function($query) use ($input) {
                    return $query->where('rating', '>=',$input['rating']);
            })
            ->when($isLoadRelationData, function ($query) {
                return $query->with(['userGame','bountyType']);
            })
            ->when(!empty($input['game_id']),function($query) use ($input) {
                $game_id = $input["game_id"];
                return $query->WhereHas('userGame',function ($q2) use ($game_id) {
                    $q2->where('game_id',$game_id);
                });
            })
            ->when(!empty($input['min']), function($query) use ($input) {
                return $query->where('price', '>=', $input['min']);
            })
            ->when(!empty($input['max']), function($query) use ($input) {
                return $query->where('price', '<=', $input['max']);
            })
            ->when(
                !empty($input['sort']) && !empty($input['sort_type']),
                function ($query) use ($input) {
                    $query->orderBy($input['sort'], $input['sort_type']);
                },
                function ($query) {
                    $query->orderBy('updated_at', 'desc');
                }
            )
            ->when(!empty($input['user_service']), function ($query) use ($input) {
                $userService = $input['user_service'];
                return $query->WhereHas('bountyType',function ($q2) use ($userService) {
                    $q2->where('bounty_type_id',$userService);
                });
            })
            ->when(!empty($input['search_key']), function ($query) use ($input) {
                $searchKey = $input['search_key'];
                return $query->where(function ($q) use ($searchKey) {
                    return $q->where('full_name', 'like', "%{$searchKey}%")
                        ->orWhere('description', 'like', "%{$searchKey}%");
                });
            })
            ->when(!empty($input['limit']), function ($query) use ($input) {
                return $query->paginate($input['limit']);
            });
    }

    public function getUserBounties($input)
    {
        return UserBounty::join('game_bounties', 'game_bounties.id', 'user_bounties.game_bounty_id')
                        ->join('games', 'games.id', 'game_bounties.game_id')
                        ->join('users', 'users.id', 'user_bounties.claimer_id')
                        ->where('user_bounties.claimer_id', Auth::id())
                        ->select('user_bounties.id as id', 'user_bounties.status', 'users.full_name as gamelancer',
                                    'games.title as game', 'user_bounties.created_at', 'user_bounties.price',
                                    'game_bounties.description', 'user_bounties.updated_at')
                        ->when(
                            !empty($input['sort']),
                            function ($query) use ($input) {
                                $query->orderBy($input['sort'], $input['sort_type']);
                            }, function ($query) {
                                $query->orderBy('updated_at', 'desc');
                            }
                        )
                        ->when(!empty($input['searchKey']), function ($query) use ($input) {
                            $searchKey = $input['searchKey'];
                            return $query->where(function ($q) use ($searchKey) {
                                return $q->where('game_bounties.description', 'like', "%{$searchKey}%")
                                        ->orWhere('games.title', 'like', "%{$searchKey}%")
                                        ->orWhere('users.full_name', 'like', "%{$searchKey}%");

                            });
                        })
                        ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function getGamesPlayed($userId)
    {
        return UserBounty::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('claimer_id', $userId);
            })
            ->where('status', Consts::USER_BOUNTY_STATUS_COMPLETED)
            ->count();
    }

    public function getUsersByRatingDecrease($input)
    {
        return User::select('id', 'full_name', 'avatar', 'rating', 'number_reviewer', 'description')
            ->orderBy('rating', 'desc')
            ->when(!empty($input['search_key']), function ($query) use ($input) {
                $searchKey = $input['search_key'];
                $query->where(function ($q) use ($searchKey) {
                    $q->where('full_name', 'like', "%{$searchKey}%")
                      ->orWhere('rating', 'like', "%{$searchKey}%")
                      ->orWhere('number_reviewer', 'like', "%{$searchKey}%");
                });
            })
            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function saveProfilePicture($image, $userId)
    {
        $imgPath = Utils::saveFileToStorage($image, 'avatars');

        $user = User::findOrFail($userId);
        $user->avatar = $imgPath;
        $user->save();

        dispatch(new CalculateUserLevel(Auth::id()));

        return $user;
    }

    public function submitRating($input)
    {
        if ($input['user_id'] === Auth::id()) {
            throw new Exception('Cannot submit rate myself.');
        }
        $currentMillis = Utils::currentMilliseconds();

        $report = $this->insertReport(
                            $input['user_id'],
                            Auth::id(),
                            $input['user_bounty_id'],
                            $input['content'],
                            $input['rate'],
                        Consts::REPORT_TYPE_RATE,
                            $currentMillis
                        );

        return $report;
    }

    public function updateUserInfo($userId, $input)
    {
        $user = User::findOrFail($userId);

        if (array_key_exists('full_name', $input)) {
            $user->full_name = $input['full_name'];
        }
        if (array_key_exists('username', $input)) {
            $user->username = $input['username'];
        }
        if (array_key_exists('description', $input)) {
            $user->description = $input['description'];
        }
        if (array_key_exists('is_notified', $input)) {
            $user->is_notified = $input['is_notified'];
        }
        if (array_key_exists('price', $input)) {
            $user->price = $input['price'];
        }
        if (array_key_exists('languages', $input)) {
            $user->languages = $input['languages'];
        }
        $user->save();

        dispatch(new CalculateUserLevel(Auth::id()));

        return $user;
    }


    public function report($input)
    {
        $userBounty = UserBounty::where('id',$input['id'])->first();
        $currentMillis = Utils::currentMilliseconds();

        if (!$userBounty) {
            throw new Exception('User bounty not found');
        }

        $report = $this->insertReport(
            $userBounty->user_id,
            $userBounty->claimer_id,
            $userBounty->id,
            $input['description'],
            null,
            Consts::REPORT_TYPE,
            $currentMillis
        );

        return $report;
    }

    private function insertReport($userId,$reviewerId,$userBountyId,$content,$rate,$type,$currentMillis)
    {
        $rating                 = new Report;
        $rating->user_id        = $userId;
        $rating->reviewer_id    = $reviewerId;
        $rating->user_bounty_id = $userBountyId;
        $rating->content        = $content;
        if (!empty($rate)) {
            $rating->rate           = $rate;
        }
        $rating->type           = $type;
        $rating->created_at     = $currentMillis;
        $rating->updated_at     = $currentMillis;
        $rating->save();
    }

    public function getCurrentUserViaGuard()
    {
        return Auth::guard('api')->user();
    }

    public function getReviewers($userId, $input = []) {
        $columns = DB::raw('users.id,users.full_name, users.avatar, user_bounties.quantity,
                    user_bounties.price,
                    ratings.reviewer_id, ratings.rate, ratings.created_at');
        return User::select($columns)
            ->leftJoin('ratings', 'ratings.reviewer_id', '=', 'users.id')
            ->leftJoin('user_bounties', 'ratings.user_bounty_id', '=', 'user_bounties.id')
            ->where('ratings.user_id', $userId)
            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function insertOrRemoveFavourite($idolId, $isFavourite)
    {
        $userId = Auth::id();
        if ($userId === $idolId) {
            throw new Exception('Cannot favourite myself.');
        }
        $userFavourite = UserFavourite::where('favouriter_id', $userId)
            ->where('idol_id', $idolId)
            ->first();
        if (! $userFavourite ) {
            $userFavourite = new UserFavourite();
            $userFavourite->favouriter_id = $userId;
            $userFavourite->idol_id = $idolId;
        }
        $userFavourite->is_favourite = $isFavourite;
        $userFavourite->save();

        return $userFavourite;
    }

    public function isFavouriteIdol($idolId)
    {
        return UserFavourite::where('favouriter_id', Auth::id())
            ->where('idol_id', $idolId)
            ->select('is_favourite')
            ->first();
    }

    public function getListIdols($input)
    {
        return UserFavourite::join('users', 'user_favourites.idol_id', 'users.id')
                            ->where('user_favourites.favouriter_id', Auth::id())
                            ->where('user_favourites.is_favourite', Consts::TRUE)
                            ->select('users.id', 'users.full_name', 'users.avatar', 'users.rating', 'users.number_reviewer')
                            ->orderBy('rating', 'desc')
                            ->paginate(array_get($input, 'limit', Consts::DEFAULT_PER_PAGE));
    }

    public function getUserNotifications()
    {
        return Auth::user()->notifications()->get()
            ->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'user_bounty_id' => $item->data['user_bounty_id'],
                    'content' => $item->data['content'],
                    'read_at' => $item->read_at,
                    'created_at' => $item->created_at
                ];
            });
    }

    public function markAsReadNotification($notifiId)
    {
        $notification = DatabaseNotification::find($notifiId);
        $notification->markAsRead();
        return [
            'id' => $notification->id,
            'user_bounty_id' => $notification->data['user_bounty_id'],
            'content' => $notification->data['content'],
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at
        ];
    }

    public function markAsReadAllNotifications()
    {
        return Auth::user()->notifications->markAsRead();
    }

    public function getNumberNotificationUnReadForEachBounty()
    {
        return Auth::user()->notifications()
            ->whereNull('read_at')
            ->select('data')
            ->get()
            ->transform(function ($item) {
                return $item->data;
            })
            ->countBy('user_bounty_id');
    }
}
