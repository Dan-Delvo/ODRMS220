<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class forgotpassword extends Controller
{
    public function index()
    {
        if (session('otp_verified')) {
            return redirect()->route('newpassword');
        }
        if (session('otp_requested')) {
            return redirect()->route('verifyotp');
        }
        return view('common/forgetpass');
    }

    public function forgotpost(Request $request)
    {
        $email = DB::table('acc_users')->where('email_address', $request->variable)->first();

        if ($email) {
            $otpCode = rand(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(5);

            // ✅ Clear ALL old session data to prevent conflicts
            session()->forget(['otp', 'expiry', 'otp_attempts', 'lockout_until', 'email_entered', 'otp_requested', 'otp_verified', 'password_reset_step']);

            // ✅ Store OTP as STRING for consistency
            session([
                'email' => $request->variable,
                'otp' => (string)$otpCode,  // 👈 Convert to string
                'expiry' => $expiresAt,
                'email_entered' => true,
                'otp_requested' => true
            ]);

            try {
                // Use send() instead of queue() to avoid mail queue issues
                Mail::to($request->variable)->send(new ResetPasswordMail($otpCode));

                // Log for debugging
                Log::info("Initial OTP Sent", [
                    'email' => $request->variable,
                    'otp' => $otpCode,
                    'expiry' => $expiresAt->toDateTimeString()
                ]);

                session()->flash('success', 'OTP sent successfully!');
                return redirect()->route('verifyotp');
            } catch (\Exception $e) {
                Log::error("Failed to send initial OTP", [
                    'email' => $request->variable,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect()->back()->with('error', 'Failed to send OTP. Please try again.');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid email address!');
        }
    }

    public function showVerifyOTP()
    {
        if (!session('otp_requested')) {
            // No OTP requested — send them back to email form
            return redirect()->route('forgot');
        }
        if (session('otp_verified')) {
            // OTP already verified — go straight to new password
            return redirect()->route('newpassword');
        }
        // Check if user is locked out
        $lockoutUntil = session('lockout_until');
        if ($lockoutUntil && now()->lessThan($lockoutUntil)) {
            $remainingTime = ceil(now()->diffInMinutes($lockoutUntil, false));
            session()->flash('error', "Account temporarily locked. Please wait {$remainingTime} minutes before trying again.");
            return view('common/OTP/otp');
        }

        // Check if OTP has expired
        $expiry = session('expiry');
        if (!$expiry || now()->greaterThan($expiry)) {
            session()->flash('error', 'OTP has expired. Please request a new one.');
            return view('common/OTP/otp');
        }
        return view('common/OTP/otp');
    }

    public function verifyOTP(Request $request)
    {
        $ip = $request->ip();
        $lockKey = "locked:$ip";
        $attemptKey = "otp_attempts:$ip";

        // 🔒 Check if already locked
        if (Cache::has($lockKey)) {
            $lockedUntil = Cache::get($lockKey);
            $remainingMinutes = ceil(($lockedUntil - time()) / 60);
            $remainingSeconds = $lockedUntil - time();

            session()->flush();

            return response()->view('auth.lockout', [
                'remaining_minutes' => $remainingMinutes,
                'remaining_seconds' => $remainingSeconds,
                'locked_until' => $lockedUntil
            ], 403);
        }

        // 🧩 Combine OTP input
        $inputOtp = trim("{$request->first}{$request->second}{$request->third}{$request->fourth}{$request->fifth}{$request->sixth}");
        $storedOtp = session('otp');
        $expiry = session('expiry');

        // Debug logging (remove after testing)
        Log::info("OTP Verification Attempt", [
            'input_otp' => $inputOtp,
            'stored_otp' => $storedOtp,
            'expiry' => $expiry ? $expiry->toDateTimeString() : 'null',
            'ip' => $ip
        ]);

        // ⏳ Check if OTP exists and is not expired
        if (!$storedOtp || !$expiry) {
            session()->forget('otp_requested');
            return redirect()->route('forgot.password')->with('error', 'No OTP found. Please request a new one.');
        }

        if (now()->greaterThan($expiry)) {
            session()->forget(['otp', 'expiry', 'otp_requested']);
            return back()->with('error', 'OTP has expired. Please request a new one.');
        }

        // ✅ Correct OTP - Convert both to strings for comparison
        if ((string)$inputOtp === (string)$storedOtp) {
            // Clear attempts on success
            Cache::forget($attemptKey);
            session()->forget(['otp', 'expiry']);
            session(['otp_verified' => true, 'password_reset_step' => 'newpassword']);

            Log::info("OTP Verified Successfully", ['ip' => $ip]);

            return redirect()->route('newpassword')->with('status', 'OTP verified successfully!');
        }

        // ❌ Failed attempt
        $attempts = Cache::get($attemptKey, 0) + 1;
        Cache::put($attemptKey, $attempts, now()->addMinutes(15));

        Log::warning("Invalid OTP Attempt", [
            'ip' => $ip,
            'attempt' => $attempts,
            'input' => $inputOtp,
            'expected' => $storedOtp
        ]);

        if ($attempts >= 3) {
            // Lock the account for 15 minutes
            $lockedUntil = time() + (15 * 60);
            Cache::put($lockKey, $lockedUntil, now()->addMinutes(15));

            // Clear all attempts and session data
            Cache::forget($attemptKey);
            session()->flush();

            Log::warning("Account locked due to failed OTP attempts", [
                'ip' => $ip,
                'locked_until' => date('Y-m-d H:i:s', $lockedUntil)
            ]);

            // Return lockout page directly
            $remainingMinutes = 15;
            $remainingSeconds = 15 * 60;

            return response()->view('auth.lockout', [
                'remaining_minutes' => $remainingMinutes,
                'remaining_seconds' => $remainingSeconds,
                'locked_until' => $lockedUntil
            ], 403);
        } else {
            $remaining = 3 - $attempts;
            return back()->with('error', "Invalid OTP. You have {$remaining} attempt(s) remaining.");
        }
    }

    public function resendOTP()
    {
        $email = session('email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please start over.'
            ], 400);
        }

        // Generate new OTP
        $otpCode = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        // ✅ Clear old OTP data completely
        session()->forget(['otp', 'expiry', 'otp_verified', 'password_reset_step']);

        // ✅ Store new OTP as STRING
        session([
            'otp' => (string)$otpCode,  // 👈 Store as string
            'expiry' => $expiresAt,
            'email' => $email,
            'otp_requested' => true
        ]);

        try {
            // Use send() instead of queue()
            Mail::to($email)->send(new ResetPasswordMail($otpCode));

            // Log for debugging
            Log::info("OTP Resent", [
                'email' => $email,
                'otp' => $otpCode,
                'otp_type' => gettype($otpCode),
                'session_otp' => session('otp'),
                'session_otp_type' => gettype(session('otp')),
                'expiry' => $expiresAt->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully!',
                'expiry' => $expiresAt->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error("Resend OTP failed", [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showNewPassword()
    {
        if (!session('otp_verified')) {
            // User can’t access this without verified OTP
            return redirect()->route('verifyotp');
        }
        return view('common/OTP/newpassword');
    }

    public function newpassword(Request $request)
    {
        $oldPass = Account::where('email_address', session('email'))->value('password');
        $request->validate([
            'password' => [
                'required',
                function ($attribute, $value, $fail) use ($oldPass) {
                    if (Hash::check($value, $oldPass)) {
                        $fail('The new password must be different from the old password.');
                    }
                }
            ]
        ]);

        $password = Hash::make($request->password);
        $email = session('email');
        DB::table('acc_users')->where('email_address', $email)->update([
            'password' => $password
        ]);
        session(['password_change' => true]);
        session()->forget(['email_entered', 'otp_requested', 'otp_verified', '']);
        session()->forget('password_reset_step');
        session()->flash('status', 'Password updated successfully!');
        return redirect()->route('login');
    }
}
