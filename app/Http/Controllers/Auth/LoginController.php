<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Auth\Auth;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    /** @var string */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')
             ->except('logout');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $user =  \Illuminate\Support\Facades\Auth::user();
            //$user = Auth::user();dd();

            if ($user->activated) {
                return response()->json(['user' => $user]);
            }

            $this->guard()->logout();
            $request->session()->invalidate();
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
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
        if ($request->expectsJson()) {
            return response()->json([
                'user' => $user,
                'intended' => $this->redirectPath(),
            ]);
        }

        Session::flash('status', [
            'title' => trans('aktiv8me.status.login'),
            'message' => trans('aktiv8me.status.logged_in', ['username' => $user->name]),
            'type' => 'success',
        ]);
    }
}
