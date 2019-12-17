<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\SingleSession\SingleSessionService;
use Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = 'admin';
    protected $afterLogout = '/admin/login';
    protected $guard = 'admin';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    protected function guard()
    {
        return Auth::guard($this->guard);
    }

    /**
     * @return bool
     */
    protected function isAnyGuardLoggedIn()
    {
        $configGuards = config('auth.guards');
        foreach ($configGuards as $guard => $config){
            if (Auth::guard($guard)->check()) {
                return true;
            }
        }

        return false;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        if(!$this->isAnyGuardLoggedIn()){
            $request->session()->invalidate();
        }
        return redirect($this->afterLogout);
    }
}