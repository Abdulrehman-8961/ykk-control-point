<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
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

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'general' => [trans('auth.failed')],
            ]);
        }

        $request->session()->regenerate();

        return $this->authenticated($request, Auth::user())
            ?? redirect()->intended($this->redirectTo);
    }

    protected function authenticated(Request $request, $user): ?RedirectResponse
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

        if ($user->must_change == 1) {
            return redirect('/change-password');
        }

        return null;
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
