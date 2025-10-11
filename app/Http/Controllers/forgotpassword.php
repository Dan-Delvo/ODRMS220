<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

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

            // Clear any previous lockout data when generating new OTP
            session()->forget(['otp_attempts', 'lockout_until']);

            session([
                'email' => $request->variable,
                'otp' => $otpCode,
                'expiry' => $expiresAt,
                'email_entered' => true,
                'otp_attempts' => 0
            ]);

            Mail::to($request->variable)->send(new ResetPasswordMail($otpCode));
            session(['otp_requested' => true]);
            session()->flash('success', 'OTP Sent successfully!');
            return redirect()->route('verifyotp');
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
        Log::info("OTP verification attempt");

        // Check if user is locked out
        $lockoutUntil = session('lockout_until');
        if ($lockoutUntil && now()->lessThan($lockoutUntil)) {
            $remainingMinutes = ceil(now()->diffInMinutes($lockoutUntil, false));
            session()->forget('otp_requested');
            session()->flash('error', "Account temporarily locked. Please wait {$remainingMinutes} minutes before trying again.");
            return view('common/OTP/otp');
        }

        $otp = "{$request->first}{$request->second}{$request->third}{$request->fourth}{$request->fifth}{$request->sixth}";
        $email = session('email');
        $otpCode = session('otp');
        $expiry = session('expiry');
        $attempts = session('otp_attempts', 0);

        // Check if OTP has expired
        if (!$otpCode || !$expiry || now()->greaterThan($expiry)) {
            session()->forget('otp_requested');
            session()->flash('error', 'OTP has expired. Please request a new one.');
            return view('common/OTP/otp');
        }

        // Validate OTP
        if ($otp == $otpCode) {
            // Success - Clear all session data related to OTP
            session()->forget(['otp', 'expiry', 'otp_attempts', 'lockout_until']);
            session(['otp_verified' => true]);
            session(['password_reset_step' => 'newpassword']);
            session()->flash('status', 'OTP Verified successfully!');
            return redirect()->route('newpassword');
        } else {
            // Failed attempt
            $attempts++;
            session(['otp_attempts' => $attempts]);

            if ($attempts >= 3) {
                // Lock out for 15 minutes
                $lockoutUntil = now()->addMinutes(15);
                session([
                    'lockout_until' => $lockoutUntil,
                    'otp_attempts' => $attempts
                ]);

                session()->flash('error', 'Too many failed attempts. Account locked for 15 minutes.');
            } else {
                $remainingAttempts = 3 - $attempts;
                session()->flash('error', "Invalid OTP. {$remainingAttempts} attempts remaining.");
            }

            return view('common/OTP/otp');
        }
    }

    // Optional: Add a method to resend OTP
    public function resendOTP(Request $request)
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
        $expiresAt = Carbon::now()->addMinutes(5);

        // Reset attempts and lockout
        session([
            'otp' => $otpCode,
            'expiry' => $expiresAt,
            'otp_attempts' => 0
        ]);
        session()->forget('lockout_until');

        try {
            Mail::to($email)->send(new ResetPasswordMail($otpCode));
            session()->flash('success', 'New OTP sent successfully!');

            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
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
        return view('redirect/redirectLogin')->with('status', 'Password updated successfully!');
    }
}
