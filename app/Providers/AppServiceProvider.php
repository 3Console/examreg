<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Http\Services\MasterdataService;
use App\User;
use App\Models\Admin;
use App\Consts;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Validator::extend('unique_email', function($attribute, $value, $parameters, $validator) {
            $user = User::where('status', Consts::USER_ACTIVE)
                ->where('email', $value)
                ->first();
            return !$user;
        });
        Validator::extend('unique_email_adminstrator', function($attribute, $value, $parameters, $validator) {
            return !Admin::where('email', $value)->exists();
        });
        Validator::extend('unique_username', function($attribute, $value, $parameters, $validator) {
            $isExistedUsername = User::where('username', $value)
                ->exists();
            return !$isExistedUsername;
        });
        Validator::extend('password_white_space', function($attribute, $value, $parameters, $validator) {
            return is_int(strpos($value, ' ')) ? false :true;
        });
        Validator::extend('verified_email', function($attribute, $value, $parameters, $validator) {
            $user = User::where('email', $value)->first();
            if ($user) {
                return $user->securitySetting->email_verified;
            }
            return true;
        });
        Validator::extend('correct_password', function ($attribute, $value, $parameters, $validator) {
            $user = Auth::user();
            return (password_verify($value, $user->password));
        });

        View::composer('*', function ($view) {
            $dataVersion = MasterdataService::getDataVersion();
            $view->with('dataVersion', $dataVersion);
         });

        DB::enableQueryLog();
        DB::listen(function ($query) {
            $ignoreKyes = ['insert into `jobs`', 'select * from `jobs`'];
            foreach ($ignoreKyes as $key) {
                if (substr($query->sql, 0, strlen($key)) === $key) {
                    return;
                }
            }

            Log::debug('SQL', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'runtime' => $query->time
            ]);
        });
    }
}
