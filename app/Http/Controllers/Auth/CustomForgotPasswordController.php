<?php 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = DB::table('users')->where('email', $request->email)->where('is_deleted', 0)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User email not found.']);
        }

        if ($user->portal_access == 0) {
            return back()->withErrors(['email' => 'User is not active, please contact system administrator.']);
        }

        // Generate a temporary password
        $tempPassword = Str::random(12);

        // Update password and mark must_change
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'name' => $tempPassword,
                'password' => Hash::make($tempPassword),
                'must_change' => 1,
            ]);

        $name = $user->firstname . " " . $user->lastname;  
        
        // Send the email
        $emailData = [
            'name' => $name,
            'email' => $user->email,
            'new_password' => $tempPassword,
            'login_url' => url('/login'),
        ];

        Mail::send('emails.password_reset_notification', $emailData, function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your Temporary Password');
        });

        return redirect()->route('password.return-to-login')->with('status', 'A temporary password has been sent to your email.');

    }

}
?>