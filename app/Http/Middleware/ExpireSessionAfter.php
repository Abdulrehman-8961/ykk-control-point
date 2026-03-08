<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ExpireSessionAfter
{
    public function handle($request, Closure $next)
    {
        // Check if last activity time is set
        if (@session()->get('last_activity')) {
            $lastActivity = session()->get('last_activity');
            $currentTime = strtotime(date("Y-m-d H:i:s"));

            $secondsDifference = $currentTime - $lastActivity;

            if ($secondsDifference >= 2592000) {
                session()->forget('last_activity');
                Auth::logout();
                return redirect('/login');
            }
        }

        return $next($request);
    }
}
