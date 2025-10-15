<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\PermissionRoleModel;
use App\Models\AuditTable;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use GeoIP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

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

        // Unified lockout system (same as OTP)
        $lockKey = "locked:$clientIp";
        $attemptKey = "login_attempts:$clientIp";
        $maxAttempts = 3;
        $lockoutMinutes = 15;

        // 🔒 Check if already locked (middleware will also catch this)
        if (Cache::has($lockKey)) {
            $lockedUntil = Cache::get($lockKey);
            $remainingMinutes = ceil(($lockedUntil - time()) / 60);
            $remainingSeconds = $lockedUntil - time();

            // Return lockout page directly
            return response()->view('auth.lockout', [
                'remaining_minutes' => $remainingMinutes,
                'remaining_seconds' => $remainingSeconds,
                'locked_until' => $lockedUntil
            ], 403);
        }

        $credentials = ['email_address' => $email, 'password' => $request->password];
        $remember = $request->has('remember');

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            // ✅ Success — clear attempts
            Cache::forget($attemptKey);

            $user = Auth::user();
            $browser = request()->header('User-Agent');
            $ipAddress = request()->ip();

            if ($user->roles->id > 1) {
                AuditTable::create([
                    'type'          => 'User Logged In',
                    'old_data'      => null,
                    'new_data'      => json_encode([
                        'ip_address' => $ipAddress,
                        'session_id' => $sessionId,
                        'browser'    => $browser,
                    ]),
                    'time'          => now(),
                    'changedBy'     => $user->studentInformation->full_name,
                    'fromTableName' => 'Log In',
                    'description' => 'An Admin has Logged In'
                ]);

                $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', $user->role_id);
                return empty($PermissionDashboard) ? redirect('/walkin/form') : redirect('/dashboard');
            } elseif ($user->roles->name === 'student') {
                AuditTable::create([
                    'type'          => 'User Logged In',
                    'old_data'      => null,
                    'new_data'      => json_encode([
                        'ip_address' => $ipAddress,
                        'session_id' => $sessionId,
                        'browser'    => $browser,
                    ]),
                    'time'          => now(),
                    'changedBy'     => $user->studentInformation->full_name,
                    'fromTableName' => 'Log In',
                    'description' => 'A Student has logged In'
                ]);

                return redirect('/stpage');
            }
        }

        // ❌ Failed attempt
        $attempts = Cache::get($attemptKey, 0) + 1;
        Cache::put($attemptKey, $attempts, now()->addMinutes($lockoutMinutes));

        if ($attempts >= $maxAttempts) {
            // Lock the account for 15 minutes
            $lockedUntil = time() + ($lockoutMinutes * 60);
            Cache::put($lockKey, $lockedUntil, now()->addMinutes($lockoutMinutes));

            // Clear attempts counter
            Cache::forget($attemptKey);

            // Log the lockout event
            Log::warning("Account locked due to failed login attempts", [
                'ip' => $clientIp,
                'email' => $email,
                'locked_until' => date('Y-m-d H:i:s', $lockedUntil)
            ]);

            // Return lockout page directly
            return response()->view('auth.lockout', [
                'remaining_minutes' => $lockoutMinutes,
                'remaining_seconds' => $lockoutMinutes * 60,
                'locked_until' => $lockedUntil
            ], 403);
        }

        // Show remaining attempts
        $remainingAttempts = $maxAttempts - $attempts;
        return redirect()->route('login')->with(
            'error',
            "Invalid email or password. You have {$remainingAttempts} attempt(s) remaining."
        );
    }

    // Logout the authenticated user
    public function logout()
    {
        Auth::logout();
        return redirect('');
    }
}
