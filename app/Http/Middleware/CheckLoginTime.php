<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckLoginTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guards = ['admin', 'manager', 'nonvenue', 'bdm', 'vendormanager', 'seomanager', 'team'];

        $user = null;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                break;
            }
        }

        if ($user) {
            $role = Role::find($user->role_id);

            if ($role && $role->is_all_time_login == 0) {
                $currentTime = date('H:i:s');

                if ($role->login_start_time && $role->login_end_time) {
                    if ($currentTime < $role->login_start_time || $currentTime > $role->login_end_time) {
                        Auth::guard($guard)->logout();
                        session()->invalidate();
                        session()->regenerateToken();
                        return redirect()->route('login')->with('status', [
                            'success' => false,
                            'alert_type' => 'error',
                            'message' => 'Your session has expired due to login time restrictions.'
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
