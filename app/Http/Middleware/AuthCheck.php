<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCheck {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        } else if (Auth::guard('manager')->check()) {
            return redirect()->route('manager.dashboard');
        } else if (Auth::guard('nonvenue')->check()) {
            return redirect()->route('nonvenue.dashboard');
        } else if (Auth::guard('team')->check()) {
            return redirect()->route('team.dashboard');
        } else if (Auth::guard('vendor')->check()) {
            return redirect()->route('vendor.dashboard');
        }
        return $next($request);
    }
}
