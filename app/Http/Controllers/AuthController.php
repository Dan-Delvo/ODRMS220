<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PermissionRoleModel;
use App\Models\AuditTable;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use GeoIP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{

    // Login method to redirect authenticated users based on their role
    public function login()
    {

        // Check if a user is already logged in and redirect accordingly
        if (Auth::check()) {
            if (Auth::user()->roles->name === 'student') {
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

    // Handle login logic with validation (AJAX-enabled)
    public function auth_login(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email',
            'password' => 'required',
        ]);

        $email = Str::lower($request->input('email_address'));
        $clientIp = $request->ip();
        $sessionId = $request->session()->getId();

        // 🔐 Generate Device Fingerprint
        $deviceId = $this->getDeviceFingerprint($request);

        // Multi-layer lockout system - tracks THREE separate lockouts
        $emailLockKey = "locked:email:{$email}";
        $ipLockKey = "locked:ip:{$clientIp}";
        $deviceLockKey = "locked:device:{$deviceId}";

        $emailAttemptKey = "attempts:email:{$email}";
        $ipAttemptKey = "attempts:ip:{$clientIp}";
        $deviceAttemptKey = "attempts:device:{$deviceId}";

        $emailLockoutCountKey = "lockout_count:email:{$email}";
        $ipLockoutCountKey = "lockout_count:ip:{$clientIp}";
        $deviceLockoutCountKey = "lockout_count:device:{$deviceId}";

        // Timestamp keys for tracking when lockout count was last set
        $emailLockoutTimestampKey = "lockout_timestamp:email:{$email}";
        $ipLockoutTimestampKey = "lockout_timestamp:ip:{$clientIp}";
        $deviceLockoutTimestampKey = "lockout_timestamp:device:{$deviceId}";

        $maxAttempts = 3;
        $baseLockoutMinutes = 15; // First lockout: 15 minutes
        $maxLockoutMinutes = 1440; // 24 hours max

        // 🔄 Reset lockout counts if 24 hours have passed
        $this->resetExpiredLockoutCounts($emailLockoutCountKey, $emailLockoutTimestampKey);
        $this->resetExpiredLockoutCounts($ipLockoutCountKey, $ipLockoutTimestampKey);
        $this->resetExpiredLockoutCounts($deviceLockoutCountKey, $deviceLockoutTimestampKey);

        // 🔒 Check ALL three lockout types - if ANY are locked, deny access
        $lockoutInfo = $this->checkAllLockouts($emailLockKey, $ipLockKey, $deviceLockKey);

        if ($lockoutInfo['locked']) {
            $remainingMinutes = $lockoutInfo['remaining_minutes'];
            $remainingSeconds = $lockoutInfo['remaining_seconds'];
            $lockedUntil = $lockoutInfo['locked_until'];
            $lockType = $lockoutInfo['lock_type'];

            // Check if AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'message' => "Access denied. Your {$lockType} has been locked due to too many failed attempts.",
                    'remaining_minutes' => $remainingMinutes,
                    'remaining_seconds' => $remainingSeconds,
                    'locked_until' => $lockedUntil,
                    'lock_type' => $lockType
                ], 403);
            }

            // Return lockout page for non-AJAX requests
            return response()->view('auth.lockout', [
                'remaining_minutes' => $remainingMinutes,
                'remaining_seconds' => $remainingSeconds,
                'locked_until' => $lockedUntil,
                'lock_type' => $lockType
            ], 403);
        }

        $credentials = ['email_address' => $email, 'password' => $request->password];
        $remember = $request->has('remember');

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            // ✅ Success — clear ALL attempt counters AND lockout counts (RESET TO ZERO)
            Cache::forget($emailAttemptKey);
            Cache::forget($ipAttemptKey);
            Cache::forget($deviceAttemptKey);

            // 🔄 RESET lockout counts to zero on successful login
            Cache::forget($emailLockoutCountKey);
            Cache::forget($ipLockoutCountKey);
            Cache::forget($deviceLockoutCountKey);

            // 🔄 RESET lockout timestamps
            Cache::forget($emailLockoutTimestampKey);
            Cache::forget($ipLockoutTimestampKey);
            Cache::forget($deviceLockoutTimestampKey);

            $user = Auth::user();
            $browser = $request->header('User-Agent');
            $ipAddress = $request->ip();

            // Store device ID in cookie for future requests (1 year expiry)
            cookie()->queue('device_id', $deviceId, 525600, '/', null, true, true);

            if ($user->roles->id > 1) {
                AuditTable::create([
                    'type'          => 'User Logged In',
                    'old_data'      => null,
                    'new_data'      => json_encode([
                        'ip_address' => $ipAddress,
                        'session_id' => $sessionId,
                        'browser'    => $browser,
                        'device_id'  => $deviceId,
                    ]),
                    'time'          => now(),
                    'changedBy'     => $user->studentInformation->full_name,
                    'fromTableName' => 'Log In',
                    'description' => 'An Admin has Logged In'
                ]);

                $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', $user->role_id);
                $redirectUrl = empty($PermissionDashboard) ? '/walkin/form' : '/dashboard';

                // Return JSON for AJAX requests
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Login successful',
                        'redirect' => $redirectUrl,
                        'user_type' => 'admin'
                    ]);
                }

                return redirect($redirectUrl);
            } elseif ($user->roles->name === 'student') {
                AuditTable::create([
                    'type'          => 'User Logged In',
                    'old_data'      => null,
                    'new_data'      => json_encode([
                        'ip_address' => $ipAddress,
                        'session_id' => $sessionId,
                        'browser'    => $browser,
                        'device_id'  => $deviceId,
                    ]),
                    'time'          => now(),
                    'changedBy'     => $user->studentInformation->full_name,
                    'fromTableName' => 'Log In',
                    'description' => 'A Student has logged In'
                ]);

                // Return JSON for AJAX requests
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Login successful',
                        'redirect' => '/stpage',
                        'user_type' => 'student'
                    ]);
                }

                return redirect('/stpage');
            }
        }

        // ❌ Failed attempt - increment ALL three counters
        $emailAttempts = Cache::get($emailAttemptKey, 0) + 1;
        $ipAttempts = Cache::get($ipAttemptKey, 0) + 1;
        $deviceAttempts = Cache::get($deviceAttemptKey, 0) + 1;

        Cache::put($emailAttemptKey, $emailAttempts, now()->addMinutes($baseLockoutMinutes));
        Cache::put($ipAttemptKey, $ipAttempts, now()->addMinutes($baseLockoutMinutes));
        Cache::put($deviceAttemptKey, $deviceAttempts, now()->addMinutes($baseLockoutMinutes));

        // Check if any of the three reached max attempts
        $lockedLayers = [];

        // Check EMAIL lockout
        if ($emailAttempts >= $maxAttempts) {
            $emailLockoutCount = Cache::get($emailLockoutCountKey, 0) + 1;
            $emailLockoutMinutes = min($baseLockoutMinutes * pow(2, $emailLockoutCount - 1), $maxLockoutMinutes);
            $emailLockedUntil = time() + ($emailLockoutMinutes * 60);

            Cache::put($emailLockKey, $emailLockedUntil, now()->addMinutes($emailLockoutMinutes));
            Cache::put($emailLockoutCountKey, $emailLockoutCount, now()->addDays(1)); // Store for 1 day
            Cache::put($emailLockoutTimestampKey, time(), now()->addDays(1)); // Track when count was set
            Cache::forget($emailAttemptKey);

            $lockedLayers[] = [
                'type' => 'email',
                'minutes' => $emailLockoutMinutes,
                'until' => $emailLockedUntil,
                'count' => $emailLockoutCount
            ];
        }

        // Check IP lockout
        if ($ipAttempts >= $maxAttempts) {
            $ipLockoutCount = Cache::get($ipLockoutCountKey, 0) + 1;
            $ipLockoutMinutes = min($baseLockoutMinutes * pow(2, $ipLockoutCount - 1), $maxLockoutMinutes);
            $ipLockedUntil = time() + ($ipLockoutMinutes * 60);

            Cache::put($ipLockKey, $ipLockedUntil, now()->addMinutes($ipLockoutMinutes));
            Cache::put($ipLockoutCountKey, $ipLockoutCount, now()->addDays(1)); // Store for 1 day
            Cache::put($ipLockoutTimestampKey, time(), now()->addDays(1)); // Track when count was set
            Cache::forget($ipAttemptKey);

            $lockedLayers[] = [
                'type' => 'IP address',
                'minutes' => $ipLockoutMinutes,
                'until' => $ipLockedUntil,
                'count' => $ipLockoutCount
            ];
        }

        // Check DEVICE lockout
        if ($deviceAttempts >= $maxAttempts) {
            $deviceLockoutCount = Cache::get($deviceLockoutCountKey, 0) + 1;
            $deviceLockoutMinutes = min($baseLockoutMinutes * pow(2, $deviceLockoutCount - 1), $maxLockoutMinutes);
            $deviceLockedUntil = time() + ($deviceLockoutMinutes * 60);

            Cache::put($deviceLockKey, $deviceLockedUntil, now()->addMinutes($deviceLockoutMinutes));
            Cache::put($deviceLockoutCountKey, $deviceLockoutCount, now()->addDays(1)); // Store for 1 day
            Cache::put($deviceLockoutTimestampKey, time(), now()->addDays(1)); // Track when count was set
            Cache::forget($deviceAttemptKey);

            $lockedLayers[] = [
                'type' => 'device',
                'minutes' => $deviceLockoutMinutes,
                'until' => $deviceLockedUntil,
                'count' => $deviceLockoutCount
            ];
        }

        // If any layer got locked, return lockout response
        if (!empty($lockedLayers)) {
            // Use the longest lockout duration
            usort($lockedLayers, fn($a, $b) => $b['minutes'] <=> $a['minutes']);
            $primaryLock = $lockedLayers[0];

            $lockTypes = implode(', ', array_column($lockedLayers, 'type'));
            $durationMessage = $this->formatLockoutDuration($primaryLock['minutes']);

            // Log the lockout event
            Log::warning("Multi-layer lockout triggered", [
                'ip' => $clientIp,
                'email' => $email,
                'device_id' => $deviceId,
                'locked_layers' => $lockTypes,
                'primary_duration_minutes' => $primaryLock['minutes'],
                'locked_until' => date('Y-m-d H:i:s', $primaryLock['until'])
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'message' => "Too many failed attempts. Your {$lockTypes} locked for {$durationMessage}.",
                    'remaining_minutes' => $primaryLock['minutes'],
                    'remaining_seconds' => $primaryLock['minutes'] * 60,
                    'locked_until' => $primaryLock['until'],
                    'locked_layers' => $lockedLayers
                ], 403);
            }

            // Return lockout page for non-AJAX requests
            return response()->view('auth.lockout', [
                'remaining_minutes' => $primaryLock['minutes'],
                'remaining_seconds' => $primaryLock['minutes'] * 60,
                'locked_until' => $primaryLock['until'],
                'duration_message' => $durationMessage,
                'locked_layers' => $lockedLayers
            ], 403);
        }

        // Show remaining attempts (use the minimum across all three)
        $remainingAttempts = min(
            $maxAttempts - $emailAttempts,
            $maxAttempts - $ipAttempts,
            $maxAttempts - $deviceAttempts
        );

        $errorMessage = "Invalid email or password. You have {$remainingAttempts} attempt(s) remaining.";

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'locked' => false,
                'message' => $errorMessage,
                'remaining_attempts' => $remainingAttempts
            ], 401);
        }

        return redirect()->route('login')->with('error', $errorMessage);
    }

    private function resetExpiredLockoutCounts($lockoutCountKey, $timestampKey)
    {
        if (Cache::has($timestampKey)) {
            $timestamp = Cache::get($timestampKey);
            $hoursPassed = (time() - $timestamp) / 3600;

            // If 24 hours have passed, reset the count
            if ($hoursPassed >= 24) {
                Cache::forget($lockoutCountKey);
                Cache::forget($timestampKey);
            }
        }
    }

    private function checkAllLockouts($emailLockKey, $ipLockKey, $deviceLockKey)
    {
        $locks = [];

        // Check email lock
        if (Cache::has($emailLockKey)) {
            $lockedUntil = Cache::get($emailLockKey);
            if ($lockedUntil > time()) {
                $locks[] = [
                    'type' => 'email',
                    'until' => $lockedUntil
                ];
            } else {
                Cache::forget($emailLockKey);
            }
        }

        // Check IP lock
        if (Cache::has($ipLockKey)) {
            $lockedUntil = Cache::get($ipLockKey);
            if ($lockedUntil > time()) {
                $locks[] = [
                    'type' => 'IP address',
                    'until' => $lockedUntil
                ];
            } else {
                Cache::forget($ipLockKey);
            }
        }

        // Check device lock
        if (Cache::has($deviceLockKey)) {
            $lockedUntil = Cache::get($deviceLockKey);
            if ($lockedUntil > time()) {
                $locks[] = [
                    'type' => 'device',
                    'until' => $lockedUntil
                ];
            } else {
                Cache::forget($deviceLockKey);
            }
        }

        // If any lock exists, return the longest one
        if (!empty($locks)) {
            usort($locks, fn($a, $b) => $b['until'] <=> $a['until']);
            $primaryLock = $locks[0];

            return [
                'locked' => true,
                'lock_type' => $primaryLock['type'],
                'locked_until' => $primaryLock['until'],
                'remaining_seconds' => $primaryLock['until'] - time(),
                'remaining_minutes' => ceil(($primaryLock['until'] - time()) / 60)
            ];
        }

        return ['locked' => false];
    }

    private function getDeviceFingerprint(Request $request)
    {
        // Check if device already has a stored ID in cookie
        $existingDeviceId = $request->cookie('device_id');

        if ($existingDeviceId) {
            return $existingDeviceId;
        }

        // Generate new device fingerprint from multiple sources
        $userAgent = $request->header('User-Agent') ?? 'unknown';
        $acceptLanguage = $request->header('Accept-Language') ?? 'unknown';
        $acceptEncoding = $request->header('Accept-Encoding') ?? 'unknown';

        // Create a unique hash from device characteristics (without IP for more persistence)
        $fingerprint = hash('sha256', implode('|', [
            $userAgent,
            $acceptLanguage,
            $acceptEncoding,
        ]));

        return $fingerprint;
    }

    private function formatLockoutDuration($minutes)
    {
        if ($minutes < 60) {
            return "{$minutes} minute(s)";
        } elseif ($minutes < 1440) {
            $hours = round($minutes / 60, 1);
            return "{$hours} hour(s)";
        } else {
            return "24 hours";
        }
    }

    // Logout the authenticated user
    public function logout()
    {
        Auth::logout();
        return redirect('');
    }
}
