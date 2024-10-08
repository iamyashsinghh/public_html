<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LoginInfo;
use App\Models\TeamMember;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyVendorCsrfToken extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $login_info = LoginInfo::where(['token' => session()->token()])->first();
        if (!$login_info) {
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('vendor.login');
        }else{
                return $next($request);
        }
        if ($login_info->get_team_member) {
            if ($login_info->get_team_member->role_id == 1 || $login_info->get_team_member->role_id == 7) {
                return $next($request);
            } else {
                session()->invalidate();
                session()->regenerateToken();
                return redirect()->route('vendor.login');
            }
        } else {
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('vendor.login');
        }
    }
}
