<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PermissionRoleModel;
use App\Models\AuditTable;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    // Login method to redirect authenticated users based on their role
    public function login()
    {

        // Check if a user is already logged in and redirect accordingly
        if (Auth::check()) {
            if (Auth::user()->roles->name === 'student') {
                // dd("hello world");
                return redirect('stpage');
            }

            $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', Auth::user()->role_id);
            if (empty($PermissionDashboard)) {
                return redirect('/walkin/form');
            } else {

                return redirect('/dashboard');
            }
        }

        if (session('otp_verified')) {
            return redirect()->route('newpassword');
        }
        if (session('otp_requested')) {
            return redirect()->route('verifyotp');
        }
        return response()
            ->view('common.studentlogin')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // Handle login logic with validation
    public function auth_login(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email',
            'password' => 'required',
        ]);

        $email = Str::lower($request->input('email_address'));

        $clientIp = $request->ip();
        $sessionId = $request->session()->getId();
        $key = "login_attempts|{$clientIp}|{$sessionId}";
        $maxAttempts = 3;
        $decayMinutes = 1;

        // Check if this email is locked out
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->route('login')->with('error', 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.');
        }

        $credentials = ['email_address' => $email, 'password' => $request->password];
        $remember = $request->has('remember');


        // Attempt login regardless of whether email exists
        if (Auth::attempt($credentials, $remember)) {
            // ✅ Success — reset limiter
            RateLimiter::clear($key);

            $user = Auth::user();
            $browser = request()->header('User-Agent');
            $ipAddress = request()->ip(); // or request()->getClientIp();

            if ($user->roles->id > 1) {

                AuditTable::create([
                    'type'          => 'User Logged In',       // action becomes type
                    'old_data'      => null,                   // No previous data for login
                    'new_data'      => json_encode([
                        'ip_address' => $ipAddress,
                        'session_id' => $sessionId,
                        'browser'    => $browser,
                    ]),
                    'time'          => now(),                  // Current datetime
                    'changedBy'     => $user->studentInformation->full_name, // The user who logged in
                    'fromTableName' => 'Log In',                 // Assuming the related table
                    'description' => 'An Admin has Logged In'
                ]);


                $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', $user->role_id);
                return empty($PermissionDashboard) ? redirect('/walkin/form') : redirect('/dashboard');
            } elseif ($user->roles->name === 'student') {

                AuditTable::create([
                    'type'          => 'User Logged In',       // action becomes type
                    'old_data'      => null,                   // No previous data for login
                    'new_data'      => json_encode([
                        'ip_address' => $ipAddress,
                        'session_id' => $sessionId,
                        'browser'    => $browser,
                    ]),
                    'time'          => now(),                  // Current datetime
                    'changedBy'     => $user->studentInformation->full_name, // The user who logged in
                    'fromTableName' => 'Log In',                 // Assuming the related table
                    'description' => 'A Student has logged In'
                ]);
                return redirect('/stpage');
            }
        }

        // ❌ Failed — always count the attempt, even if email doesn’t exist
        RateLimiter::hit($key, $decayMinutes * 60);

        // Check remaining attempts AFTER hitting the rate limiter
        $remainingAttempts = RateLimiter::remaining($key, $maxAttempts);

        // If no attempts remaining, show lockout message
        if ($remainingAttempts <= 0) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->route('login')->with('error', 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.');
        }

        return redirect()->route('login')->with(
            'error',
            'Invalid email or password. You have ' . RateLimiter::remaining($key, $maxAttempts) . ' attempts left.'
        );
    }

    // Logout the authenticated user
    public function logout()
    {
        Auth::logout();
        return redirect('');
    }
}
