<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::check()) {
            $user = Auth::user();
            $role = Role::find($user->role_id);
            $currentTime = date('H:i:s');

            $role = Role::find($user->role_id);
            $currentTime = date('H:i:s');
            if ($role->is_all_time_login === 0) {
                if ($role->login_start_time && $role->login_end_time) {
                    if ($currentTime < $role->login_start_time || $currentTime > $role->login_end_time) {
                        Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();
                        return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Logged out due login time period is over.'], 400);
                    }
                }
            }
        }
        return $next($request);
    }
}
