<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginInfo;
use Carbon\Carbon;
class LogUserNavigation
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id;

            $lastLoginInfo = LoginInfo::where('user_id', $userId)->latest('login_at')->first();
            $loginDateTime = optional($lastLoginInfo)->login_at ?? now(); // Use now() as a fallback

            $formattedLoginDateTime = Carbon::parse($loginDateTime)->format('Y-M-d-H-i-s');

            $filename = storage_path("logs/user_navigation/{$userId}_{$formattedLoginDateTime}.txt");
            $directory = dirname($filename);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $logDetails = [
                'from' => url()->previous(),
                'to' => $request->url(),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ];
            $jsonLogDetails = json_encode($logDetails, JSON_UNESCAPED_SLASHES);
            file_put_contents($filename, $jsonLogDetails . PHP_EOL, FILE_APPEND);
        }

        return $response;
    }
}
