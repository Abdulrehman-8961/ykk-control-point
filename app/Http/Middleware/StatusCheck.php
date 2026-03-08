<?php

namespace App\Http\Middleware;

 
use Closure;
use Illuminate\Support\Facades\Auth;

class StatusCheck
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

            if(Auth::user()->portal_access!=1){
             return redirect('error')->with('message','You have no access to portal');
            }
            
              if(Auth::user()->password_verified==''){
             return redirect('change-password');
            }
          
               return $next($request);
    }
}
