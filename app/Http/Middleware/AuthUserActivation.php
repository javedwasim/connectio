<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthUserActivation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if($user->activated!=1){
            Auth::logout();
            return redirect('login')->with('status', 'Invalid email address!.');

        }
        return $next($request);
    }
}
