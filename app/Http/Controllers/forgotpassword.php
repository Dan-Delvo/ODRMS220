<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
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
    public function index() {
        return view('common/forgetpass');
    }

    public function forgotpost (Request $request){
        $email = DB::table('acc_users')->where('email_address', $request->variable)->first();

        if ($email) {
            $otpCode = rand(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(5);

            session(['email' => $request->variable,
                    'otp' => $otpCode,
                    'expiry' => $expiresAt,
                    'email_entered' => true
                    ]);

            Mail::to($request->variable)->send(new ResetPasswordMail($otpCode));
            return redirect()->route('verifyotp')->with('success', 'OTP sent sucessfully');
        } else {
            return redirect()->back()->with('error', 'Invalid email address!');
        }
    }

    public function showVerifyOTP(){
        return view('common/OTP/otp'); 
    }

    public function verifyOTP (Request $request) {
            Log::info("Nakapasok");
            $otp = "{$request->first}{$request->second}{$request->third}{$request->fourth}{$request->fifth}{$request->sixth}";
            $email = session('email');
            $otpCode = session('otp');
            $expiry = session('expiry');

            if (!$otpCode || now()->greaterThan($expiry)) {
                session()->flash('error', 'OTP Expired. Please request a new one');
                return view('common/OTP/otp');
            }

            if ($otp == $otpCode) {
                session()->forget(['otp', 'expiry']);
                session(['otp_verified' => true]);
                return redirect()->route('newpassword')->with('sucess', 'OTP sent sucessfully');
            }

            session()->flash('error', 'Invalid or expired OTP');
            return view('common/OTP/otp');
    }

    public function showNewPassword(){
        return view('common/OTP/newpassword'); 
    }

    public function newpassword (Request $request) {
        $password = Hash::make($request->password);
        $email = session('email');
        DB::table('acc_users')->where('email_address', $email)->update([
            'password' => $password
        ]);
        session(['password_change' => true]);
        session()->forget(['email_entered', 'otp_sent', 'otp_verified']);
        return redirect()->route('login');
    }
}
