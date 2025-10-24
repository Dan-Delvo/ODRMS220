<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckLockout
{
    /**
     * Routes that should be excluded from lockout check
     */
    protected $excludedRoutes = [
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Skip lockout check for excluded routes
        if ($this->shouldExclude($request)) {
            return $next($request);
        }

        // Get user email (if authenticated, or from request for login attempts)
        $email = null;
        if (Auth::check()) {
            $email = Str::lower(Auth::user()->email_address);
        } elseif ($request->has('email_address')) {
            $email = Str::lower($request->input('email_address'));
        }

        $clientIp = $request->ip();
        $deviceId = $this->getDeviceFingerprint($request);

        // Build lockout keys for all three layers
        $emailLockKey = $email ? "locked:email:{$email}" : null;
        $ipLockKey = "locked:ip:{$clientIp}";
        $deviceLockKey = "locked:device:{$deviceId}";

        // Timestamp keys for 24-hour reset
        $emailLockoutCountKey = $email ? "lockout_count:email:{$email}" : null;
        $ipLockoutCountKey = "lockout_count:ip:{$clientIp}";
        $deviceLockoutCountKey = "lockout_count:device:{$deviceId}";

        $emailLockoutTimestampKey = $email ? "lockout_timestamp:email:{$email}" : null;
        $ipLockoutTimestampKey = "lockout_timestamp:ip:{$clientIp}";
        $deviceLockoutTimestampKey = "lockout_timestamp:device:{$deviceId}";

        // 🔄 Reset lockout counts if 24 hours have passed
        if ($emailLockoutCountKey) {
            $this->resetExpiredLockoutCounts($emailLockoutCountKey, $emailLockoutTimestampKey);
        }
        $this->resetExpiredLockoutCounts($ipLockoutCountKey, $ipLockoutTimestampKey);
        $this->resetExpiredLockoutCounts($deviceLockoutCountKey, $deviceLockoutTimestampKey);

        // Check all lockouts
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

            // Return lockout page - blocking all access
            $response = response()->view('auth.lockout', [
                'remaining_minutes' => $remainingMinutes,
                'remaining_seconds' => $remainingSeconds,
                'locked_until' => $lockedUntil,
                'lock_type' => $lockType
            ], 403);

            // Add headers to prevent caching and back button access
            return $this->addNoCacheHeaders($response);
        }

        // Add no-cache headers to all responses to prevent back button issues
        $response = $next($request);
        return $this->addNoCacheHeaders($response);
    }

    /**
     * Reset lockout count if 24 hours have passed since it was set
     */
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

    /**
     * Check all three lockout layers (email, IP, device)
     */
    private function checkAllLockouts($emailLockKey, $ipLockKey, $deviceLockKey)
    {
        $locks = [];

        // Check email lock (if email is available)
        if ($emailLockKey && Cache::has($emailLockKey)) {
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

    /**
     * Generate device fingerprint
     */
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

        // Create a unique hash from device characteristics
        $fingerprint = hash('sha256', implode('|', [
            $userAgent,
            $acceptLanguage,
            $acceptEncoding,
        ]));

        return $fingerprint;
    }

    /**
     * Determine if the request should be excluded from lockout
     */
    protected function shouldExclude(Request $request): bool
    {
        foreach ($this->excludedRoutes as $route) {
            if ($request->is($route) || $request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add no-cache headers to prevent browser caching
     */
    protected function addNoCacheHeaders($response)
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}