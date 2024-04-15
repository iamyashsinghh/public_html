<?php

namespace App\Http\Middleware;

use App\Models\LoginInfo;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware {
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [];

    public function handle($request, Closure $next) {
        $login_info = LoginInfo::where(['token' => session()->token(), 'login_type' => 'team'])->first();
        if ($login_info) {
            return $next($request);
        }
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }
}
