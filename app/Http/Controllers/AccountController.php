<?php

namespace App\Http\Controllers;

use App\Mail\VerifyMail;
use App\Models\Account;
use App\Models\RolesModel;
use App\Models\StudentInformationModel;
use App\Models\PermissionRoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    // Show the account creation form

    public function display()
    {
        $PermissionAcc = PermissionRoleModel::getPermission('user', Auth::user()->role_id);
        if(empty($PermissionAcc))
        {
            abort(404);
        }

        $data = PermissionRoleModel::getPermission('userEdit', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('userDelete', Auth::user()->role_id);
        $data2 = PermissionRoleModel::getPermission('userInfo', Auth::user()->role_id);
        $user = Account::with('roles')->paginate(10);
        return view('maintenance.users', compact('user'))
        ->with([
            'PermissionEdit' => $data,
            'PermissionDelete' => $data1,
            'PermissionInfo' => $data2,
        ]);
    }

    public function edit($id)
    {
        $user = Account::find($id);
        $roles = RolesModel::all();
        return view('maintenance.editUsers', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'std_students_id' => 'required|string|max:255',
            'email_address' => 'required|email',  // No uniqueness check
            'role' => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);


        // Find the user by ID
        $user = Account::find($id);

        // Check if user exists
        if (!$user) {
            return redirect()->route('user.list')->with('Danger', 'User not found!');
        }

        // Update user data
        $user->std_students_id = $validatedData['std_students_id'];
        $user->email_address = $validatedData['email_address'];
        $user->role_id = $validatedData['role'];
        $user->username = $validatedData['username'];
        $user->account_edited = Carbon::now()->toDateTimeString();
        $user->save();

        // Redirect back with success message
        return redirect()->route('user')->with('Status', 'User updated successfully!');
    }

    public function delete($id)
    {
        // Find the user by ID
        $user = Account::find($id);
        $stud = StudentInformationModel::find($id);

        // Check if user exists
        if (!$user) {
            return redirect('panel/user')->with('Danger', 'User not found!');
        }

        // Delete the user
        $user->delete();
        $stud->delete();

        // Redirect back with success message
        return redirect('panel/user')->with('Danger', 'User successfully deleted');
    }

    public function show($id)
    {
        $user = Account::with('roles')->find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        return view('maintenance.showUsers', compact('user'));
    }




    public function create()
    {
        return view('common.studentregister2');  // This is the view where the user will create their account
    }

    // Store the account information and link it with the student information
    public function store(Request $request)
    {
        $otp = "{$request->first}{$request->second}{$request->third}{$request->fourth}{$request->fifth}{$request->sixth}";
        $otpCode = session('otp');
        $expiry = session('expiresAt') ? \Carbon\Carbon::parse(session('expiresAt')) : null;

        if (!$otpCode || !$expiry || now()->greaterThan($expiry)) {
            session()->flash('error', 'OTP Expired. Please request a new one');
            session()->forget(['otp', 'countdown_start', 'durationInSeconds', 'expiresAt']);
            return view('common.verifyEmail');
        }

        if ($otp == $otpCode) {
                session()->forget(['otp', 'countdown_start', 'durationInSeconds', 'expiresAt']);
                // dd(session('std_students_id'));
                // dd($data);
                // return redirect()->route('account.store')->with($data);
                $id = Session::get('std_students_id'); // Get the student ID from session
                if (!$id) {
                    dd($id);
                    return redirect()->route('student.create')->with('error', 'No student information found.');
                }
                $request->validate([
                    'email_address' => 'required|email|unique:acc_users,email_address',
                    'username' => 'required|string|max:255',
                    'password' => 'required|string|min:8',
                ]);
                // Create the account and associate it with the student information
                Account::create([
                    'user_account_id' => $id,
                    'std_students_id' => $id,
                    'role_id' => 1,  // Adjust the role as necessary (here it's set to 1)
                    'email_address' => $request->email_address,
                    'username' => $request->username,
                    'password' => bcrypt($request->password),  // Hash the password
                ]);

                // Optionally, clear the session
                Session::forget('std_students_id');

                return redirect()->route('login');  // Redirect to dashboard or another page
        }
        session()->flash('error', 'Invalid or expired OTP');
        return view('common.verifyEmail');

        // $email = session('email_address');
        // $username = session('username');
        // $password = session('password');

        // $validator = Validator::make([
        //     'email_address' => $email,
        //     'username' => $username,
        //     'password' => $password,
        // ], [
        //     'email_address' => 'required|email|unique:acc_users,email_address',
        //     'username' => 'required|string|max:255',
        //     'password' => 'required|string|min:8',
        // ]);

        // if ($validator->fails()) {
        //     // Session::forget('email_address');
        //     // Session::forget('username');
        //     // Session::forget('password');
        //     return redirect()->route('account.create')
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // $request->validate([
        //     'email_address' => 'required|email|unique:acc_users,email_address',
        //     'username' => 'required|string|max:255',
        //     'password' => 'required|string|min:8|confirmed',
        // ]);

        // $data = [
        //     'email_address' => session('email_address'),
        //     'username' => session('username'),
        //     'password' => session('password'),
        // ];

        // $validator = Validator::make($data, [
        //     'email_address' => 'required|email|unique:acc_users,email_address',
        //     'username' => 'required|string|max:255',
        //     'password' => 'required|string|min:8|confirmed',
        // ]);

        // if ($validator->fails()) {
        //     dd("Hello");
        //     return redirect()->route('account.create')->withErrors($validator)->withInput();

        // }
    }

    public function viewOtp(Request $request) {
        $email = $request->input('email_address');
        $username = $request->input('username');
        $password = $request->input('password');

        $otpCode = rand(100000, 999999);
        $duration = 300;
        if (!session()->has('countdown_start')) {
            session([
                'countdown_start' => now(),
                'durationInSeconds' => $duration,
                'expiresAt' => now()->addSeconds($duration)
            ]);
        }
        session(['otp' => $otpCode]);
        Mail::to($email)->send(new VerifyMail($otpCode));
        return view('common.verifyEmail', compact('email', 'username', 'password')); 
    }

    public function verifyEmail(Request $request) {
        // $otp = "{$request->first}{$request->second}{$request->third}{$request->fourth}{$request->fifth}{$request->sixth}";
        // $otpCode = session('otp');
        // $expiry = session('expiresAt');

        // $data = $request->only('email_address', 'username', 'password');

        // if (!$otpCode || now()->greaterThan($expiry)) {
        //         session()->flash('error', 'OTP Expired. Please request a new one');
        //         session()->forget('expiresAt');
        //         return view('common.verifyEmail');
        // }

        // if ($otp == $otpCode) {
        //         session()->forget(['otp', 'expiry']);
        //         // dd(session('std_students_id'));
        //         // dd($data);
        //         return redirect()->route('account.store')->with($data);
        // }
        // session()->flash('error', 'Invalid or expired OTP');
        // return view('common.verifyEmail');
    }

    public function addUserStud()
    {
        $grade = ['7', '8', '9', '10', '11', '12'];
        $stat = ['Alumni', 'Regular', 'ALS'];
        $role = RolesModel::all();

        return view('maintenance.addUserStudent', compact('grade', 'stat', 'role'));

    }

    public function storeUserStud(Request $request)
    {
        $request->validate([
            // Validation for personal information
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'LRN' => 'string|max:20',
            'Grade_level' => 'string|max:50',
            'Std_status' => 'string|max:50',
            'role' => 'required',

            // Validation for account information
            'email_address' => 'required|email|unique:acc_users,email_address',
            'username' => 'required|string|max:255|unique:acc_users,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Store personal information
        $studentId = StudentInformationModel::create([
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'MiddleName' => $request->MiddleName,
            'Suffix' => $request->Suffix,
            'LRN' => $request->LRN ?? '0000',
            'Grade_level' => $request->Grade_level ?? '0',
            'Std_status' => $request->Std_status ?? 'NA',
            'Last_sy_attended' => $request->Last_sy_attended ?? '0000',
        ])->id;

        // Store account information
        Account::create([
            'user_account_id' => $studentId,
            'std_students_id' => $studentId,
            'role_id' => $request->role,
            'email_address' => $request->email_address,
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        return redirect('panel/user')->with('Status', 'Account created successfully!');


    }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return redirect()->back()->with('error', 'User not authenticated.');
        }

        // Ensure the user is an instance of the Account model
        if (!$user instanceof Account) {
            return redirect()->back()->with('error', 'Invalid user instance.');
        }

        // Update user's FCM token
        $user->fcm_token = $request->input('fcm_token');
        $user->account_edited = Carbon::now()->toDateTimeString();

        // Attempt to save the user
        $user->save();

        return redirect()->back()->with('success', 'FCM token updated successfully.');
    }



}

