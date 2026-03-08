<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers; 

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
  
       protected $redirectTo = RouteServiceProvider::HOME;

   


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

        // ✅ Option 1: Runs after successful login
protected function authenticated(Request $request, $user)
{ 
    if ($user->is_deleted) {
        Auth::logout();

        return redirect()->back()
            ->withErrors(['general' => 'Account not found.']);
    }
    if ($user->portal_access == 0) {
        Auth::logout();

        return redirect()->back()
            ->withErrors(['general' => 'Your account is inactive. Please contact support.']);
    }

    // Redirect to password change screen if must_change is enabled
    if ($user->must_change == 1) {
        return redirect('/change-password');
    }

    // Default redirect (you can change '/home' to wherever you want)
    return redirect()->intended($this->redirectPath());
}


    // ✅ Option 2: Runs when login fails (wrong credentials)
    protected function sendFailedLoginResponse(\Illuminate\Http\Request $request)

    {
        throw ValidationException::withMessages([
            'general' => [trans('auth.failed')],
        ]);
    }
}
