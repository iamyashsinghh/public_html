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
        // List of guards that are used for different user roles
        $guards = ['admin', 'manager', 'nonvenue', 'bdm', 'vendormanager', 'seomanager', 'team'];

        $user = null;

        // Loop through each guard to find the authenticated user
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                break; // Stop the loop once the user is found
            }
        }

        if ($user) {
            $role = Role::find($user->role_id); // Get the user's role

            if ($role && $role->is_all_time_login == 0) {
                // Get the current time
                $currentTime = date('H:i:s');
                Log::info("Current Time: " . $currentTime);

                // Check if login_start_time and login_end_time are set
                if ($role->login_start_time && $role->login_end_time) {
                    // If current time is outside allowed time range, log the user out
                    if ($currentTime < $role->login_start_time || $currentTime > $role->login_end_time) {
                        Auth::guard($guard)->logout();
                        session()->invalidate();
                        session()->regenerateToken();
                        Log::info("User logged out due to time restrictions");
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
