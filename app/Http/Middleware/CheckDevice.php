<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class CheckDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $guards = ['admin', 'manager', 'nonvenue', 'bdm', 'vendormanager', 'seomanager', 'team'];
        $user = null;
        $activeGuard = null;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $activeGuard = $guard;
                break;
            }
        }
        $type = 'team';
        if (!$user) {
            return $next($request);
        }
        $device_id = Cookie::get("device_id_{$type}-{$user->mobile}");
        $device = Device::where('device_id', $device_id)
            ->where('team_member_id', $user->id)
            ->where('type', $type)
            ->first();
        if (!$device) {
            Auth::guard($activeGuard)->logout();
            return redirect()->route('login')->with('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Unregistered device. Please contact the admin for device registration.']);
        }
        return $next($request);
    }
}
