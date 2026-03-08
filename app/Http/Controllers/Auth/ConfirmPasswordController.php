<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Auth;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
      public function redirectTo()

    { 

            switch (Auth::user()->role) {
                case 'admin':
                      $this->redirectTo = 'admin';
            return $this->redirectTo;
        
                    break;
                   case 'user':
                    $this->redirectTo = '/';
            return $this->redirectTo;
        
                    break;
                
                default:
                    # code...
                    break;
            }
 
     
        
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
