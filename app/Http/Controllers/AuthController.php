<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    // Method to generate a hashed password for testing/debugging purposes
    public function dumpy()
    {
        dd(Hash::make(123));
    }

    // Login method to redirect authenticated users based on their role
    public function login()

    {
        // Check if a user is already logged in and redirect accordingly
        if (Auth::check()) {
            if (Auth::user()->roles->name === 'student') {
                return redirect('stpage');
            }

            $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', Auth::user()->role_id);
            if(empty($PermissionDashboard))
            {
                return redirect ('/tables');
            }
            else{
                return redirect('/dashboard');
            }
        }

        // Show the login page if no user is logged in
        return view('common.studentlogin');
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

            if ($user->roles->id > 1) {
                $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', $user->role_id);
                return empty($PermissionDashboard) ? redirect('/tables') : redirect('/dashboard');
            } elseif ($user->roles->name === 'student') {
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
            'error','Invalid email or password. You have ' . RateLimiter::remaining($key, $maxAttempts) . ' attempts left.'
        );
    }

    // Logout the authenticated user
    public function logout()
    {
        Auth::logout();
        return redirect('');
    }
}
