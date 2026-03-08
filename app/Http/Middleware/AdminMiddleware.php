<?php

namespace App\Http\Middleware;

 
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {           
 
            if(Auth::user()->role!='admin'){
                                echo "You dont have access";
                                exit;
            }

               return $next($request);
            
    }
}
