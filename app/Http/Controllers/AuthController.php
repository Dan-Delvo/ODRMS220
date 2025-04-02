<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

            if (Auth::user()->roles->id > 1 ) {

                return redirect('dashboard');
            }
        }

        // Show the login page if no user is logged in
        return view('common.studentlogin');
    }

    // Handle login logic with validation
    public function auth_login(Request $request)
    {
        // Validate login credentials
        $credentials = ['email_address' => $request->email, 'password' => $request->password];
        $remember = $request->has('remember');

        // Attempt authentication
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Redirect based on the user's role
            if ($user->roles->id > 1) {
                return redirect('/dashboard');

            } elseif ($user->roles->name === 'student') {
                return redirect('/stpage');
            }
        }

        // Redirect back with an error if authentication fails
        return redirect()->back()->with('error', 'Invalid email or password');
    }

    // Logout the authenticated user
    public function logout()
    {
        Auth::logout();
        return redirect('');
    }
}
