<?php

namespace App\Http\Controllers;

use App\Mail\PasswordOTPMail;
use App\Mail\ResetPasswordMail;
use App\Mail\VerifyAccountUpdateMail;
use App\Mail\VerifyMail;
use App\Models\Account;
use App\Models\DocumentRequestModel;
use App\Models\RolesModel;
use App\Models\StudentInformationModel;
use App\Models\PermissionRoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;

class AccountController extends Controller
{
    // Show the account creation form
    public function display(Request $request)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        try {
            $PermissionAcc = PermissionRoleModel::getPermission('user', Auth::user()->role_id);
            if (empty($PermissionAcc)) {
                abort(404);
            }

            // Get search and sort parameters
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'user_account_id');
            $sortOrder = $request->input('sort_order', 'asc');

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['user_account_id', 'username', 'email_address', 'role_id'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'user_account_id';
            }

            // Validate sort order
            if (!in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'asc';
            }

            // Build query with relationships
            $query = Account::with(['roles', 'studentInformation']);

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_account_id', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%')
                        ->orWhere('email_address', 'like', '%' . $search . '%')
                        ->orWhereHas('roles', function ($roleQuery) use ($search) {
                            $roleQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('studentInformation', function ($studentQuery) use ($search) {
                            $studentQuery->whereRaw("CONCAT(FirstName, ' ', MiddleName, ' ', LastName) LIKE ?", ['%' . $search . '%'])
                                ->orWhereRaw("CONCAT(FirstName, ' ', LastName) LIKE ?", ['%' . $search . '%']);
                        });
                });
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results (10 per page to match your original)
            $user = $query->paginate(10);

            // Get permissions
            $data = PermissionRoleModel::getPermission('userEdit', Auth::user()->role_id);
            $data1 = PermissionRoleModel::getPermission('userDelete', Auth::user()->role_id);
            $data2 = PermissionRoleModel::getPermission('userInfo', Auth::user()->role_id);

            return view('maintenance.users', compact('user'))
                ->with([
                    'PermissionEdit' => $data,
                    'PermissionDelete' => $data1,
                    'PermissionInfo' => $data2,
                ]);
        } catch (Exception $e) {
            Log::error('Error in display method: ' . $e->getMessage());
            return redirect()->back()->with('Danger', 'An error occurred while loading users.');
        }
    }

    public function edit($id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        try {
            if (!is_numeric($id) || $id <= 0) {
                return redirect()->route('user')->with('Danger', 'Invalid user ID provided.');
            }

            $user = Account::find($id);
            if (!$user) {
                return redirect()->route('user')->with('Danger', 'User not found.');
            }

            $roles = RolesModel::all();
            if ($roles->isEmpty()) {
                return redirect()->route('user')->with('Danger', 'No roles available for assignment.');
            }

            return view('maintenance.editUsers', compact('user', 'roles'));
        } catch (Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage());
            return redirect()->route('user')->with('Danger', 'An error occurred while loading the edit form.');
        }
    }

    public function update(Request $request, $id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        if ($request->std_students_id !== $id && $request->user_account_id !== $id) {
            // Return with a SweetAlert flash message instead of abort(403)
            return redirect()->back()->with([
                'swal_error_title' => 'Unauthorized Action',
                'swal_error_text' => 'You are not allowed to modify the Student ID or Account ID.',
                'swal_error_icon' => 'error'
            ]);
        }
        try {
            if (!is_numeric($id) || $id <= 0) {
                return redirect()->route('user')->with('Danger', 'Invalid user ID provided.');
            }

            // Validate the incoming data
            $validatedData = $request->validate([
                'std_students_id' => 'required|string|max:255',
                'email_address' => 'required|email|max:255',
                'role' => 'required|integer',
                'username' => 'required|string|max:255',
            ]);

            // Find the user by ID
            $user = Account::find($id);

            // Check if user exists
            if (!$user) {
                return redirect()->route('user')->with('Danger', 'User not found!');
            }

            // // Check if email is unique (excluding current user)
            // $emailExists = Account::where('email_address', $validatedData['email_address'])
            //     ->where('id', '!=', $id)
            //     ->exists();
            // if ($emailExists) {
            //     return redirect()->back()->with('Danger', 'Email address is already in use by another user.');
            // }

            // // Check if username is unique (excluding current user)
            // $usernameExists = Account::where('username', $validatedData['username'])
            //     ->where('id', '!=', $id)
            //     ->exists();
            // if ($usernameExists) {
            //     return redirect()->back()->with('Danger', 'Username is already in use by another user.');
            // }

            // Update user data
            $user->std_students_id = $validatedData['std_students_id'];
            $user->email_address = $validatedData['email_address'];
            $user->role_id = $validatedData['role'];
            $user->username = $validatedData['username'];
            $user->account_edited = Carbon::now()->toDateTimeString();
            $user->save();

            // Redirect back with success message
            return redirect()->route('user')->with('Status', 'User updated successfully!');
        } catch (Exception $e) {
            Log::error('Error in update method: ' . $e->getMessage());
            return redirect()->back()->with('Danger', 'An error occurred while updating the user.');
        }
    }

    public function accountSendOtp($id)
    {
        $user = Account::with('studentInformation')->find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.']);
        }
        $fullName = $user->studentInformation->FirstName . ' ' . $user->studentInformation->LastName;
        $customSubject = "UBNHS Portal - Requesting Change Password Verification";
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP and expiry in session
        Session::put('password_otp', [
            'code' => $otp,
            'expires_at' => now()->addMinutes(5),
            'user_id' => $user->user_account_id
        ]);

        try {
            // Send OTP to user's email
            Mail::to($user->email_address)
                ->send(new PasswordOTPMail($fullName, $otp, $customSubject, '3 minutes'));
            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your email address.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ]);
        }
    }

    /**
     * Verify OTP from the user
     */
    public function accountVerifyOtp($id, Request $request)
    {
        $otp = $request->input('otp');
        $sessionOtp = Session::get('password_otp');

        if (!$sessionOtp) {
            return response()->json(['verified' => false, 'message' => 'No OTP session found.']);
        }

        if ($sessionOtp['user_id'] != $id) {
            return response()->json(['verified' => false, 'message' => 'Invalid session user.']);
        }

        if (Carbon::now()->greaterThan($sessionOtp['expires_at'])) {
            Session::forget('password_otp');
            return response()->json(['verified' => false, 'message' => 'OTP expired.']);
        }

        if ($sessionOtp['code'] != $otp) {
            return response()->json(['verified' => false, 'message' => 'Invalid OTP code.']);
        }

        // Mark user as verified for password change
        Session::put('password_verified', $id);
        Session::forget('password_otp');

        return response()->json([
            'verified' => true,
            'message' => 'OTP verified successfully. You can now change your password.'
        ]);
    }

    /**
     * Update password after OTP verification
     */
    public function accountUpdatePassword($id, Request $request)
    {
        // Check verification
        if (Session::get('password_verified') != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized password update. Please verify OTP first.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Please enter your current password',
            'new_password.required' => 'Please enter a new password',
            'new_password.min' => 'New password must be at least 8 characters',
            'new_password.confirmed' => 'The passwords do not match'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Account::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'current_password' => ['Current password is incorrect.']
                ]
            ], 422);
        }

        // Check if new password is the same as current password
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'new_password' => ['New password cannot be the same as your current password.']
                ]
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Clear session verification
        Session::forget('password_verified');

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }

    public function verifyUpdateProfile(Request $request, $id)
    {
        $student = StudentInformationModel::where('id', $id)
            ->with('account')
            ->firstOrFail();

        $request->validate([
            // 'email' => [
            //     'sometimes',
            //     'filled',
            //     'email',
            //     Rule::unique('acc_users', 'email_address')
            //         ->ignore($student->account->getKey(), $student->account->getKeyName())
            // ],
            'username' => [
                'sometimes',
                'filled',
                'string',
                'max:255',
                Rule::unique('acc_users', 'username')
                    ->ignore($student->account->getKey(), $student->account->getKeyName())
            ],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $changes = [];
        if ($request->username !== $student->account->username) {
            $changes['username'] = $request->username;
        }
        if ($request->email !== $student->account->email_address) {
            $changes['email'] = $request->email;
        }
        if ($request->filled('new_password')) {
            $changes['password'] = bcrypt($request->new_password);
        }

        if (empty($changes)) {
            return back()->with('Info', 'No changes are updated.');
        }

        // If email changed → require verification
        if (isset($changes['email'])) {
            $token = Str::random(40);
            session([
                'account_update' => [
                    'student_id' => $student->id,
                    'username' => $changes['username'] ?? null,
                    'email' => $changes['email'] ?? null,
                    'password' => $changes['password'] ?? null,
                    'token' => $token,
                    'expires_at' => now()->addMinutes(10),
                ],
            ]);

            $verifyUrl = route('student.profile.confirmUpdate', ['token' => $token]);
            Mail::to($changes['email'])->queue(new VerifyAccountUpdateMail($student, $verifyUrl));

            return back()->with('Success', 'Verification email sent! Please check your inbox.');
        }

        // Otherwise apply changes directly
        if (isset($changes['username'])) {
            $student->account->username = $changes['username'];
        }
        if (isset($changes['password'])) {
            $student->account->password = $changes['password'];
        }
        $student->account->save();

        return back()->with('Success', 'Your account has been updated successfully!');
    }


    public function confirmUpdate($token)
    {
        $pending = session('account_update');

        if (!$pending || $pending['token'] !== $token || now()->greaterThan($pending['expires_at'])) {
            return redirect()->route('student.profile')->with('Danger', 'Invalid or expired verification link.');
        }

        $student = StudentInformationModel::where('id', $pending['student_id'])
            ->with('account')
            ->firstOrFail();

        if ($pending['email']) {
            $student->account->email_address = $pending['email'];
        }
        if ($pending['username']) {
            $student->account->username = $pending['username'];
        }
        if ($pending['password']) {
            $student->account->password = $pending['password'];
        }
        $student->account->save();

        session()->forget('account_update');

        return redirect()->route('student.profile')->with('Success', 'Your account has been updated successfully!');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = Account::where('email_address', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }


    public function delete($id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        try {
            if (!is_numeric($id) || $id <= 0) {
                return redirect('panel/user')->with('Danger', 'Invalid user ID provided.');
            }

            // Find the user by ID
            $user = Account::find($id);
            if (!$user) {
                return redirect('panel/user')->with('Danger', 'User not found!');
            }

            // Prevent deletion of currently logged-in user
            if (Auth::id() == $id) {
                return redirect('panel/user')->with('Danger', 'You cannot delete your own account.');
            }

            // Check if user has existing requests with detailed information
            $requestCheck = $this->checkUserHasRequests($id);
            if ($requestCheck['hasRequests']) {
                $errorMessage = 'Cannot delete user with existing requests. ';
                $errorMessage .= 'Please resolve the following first: ';
                $errorMessage .= implode(', ', $requestCheck['requestTypes']);

                return redirect('panel/user')->with('Warning', $errorMessage);
            }

            DB::beginTransaction();

            // Delete related student information
            $stud = StudentInformationModel::where('id', $user->std_students_id)->first();
            if ($stud) {
                $stud->delete();
            }

            // Delete the user
            $user->delete();

            DB::commit();

            // Redirect back with success message
            return redirect('panel/user')
                ->with('success', "User {$user->username} has been successfully deleted.");
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error in delete method: ' . $e->getMessage());
            return redirect('panel/user')->with('Danger', 'An error occurred while deleting the user. Please try again or contact administrator.');
        }
    }

    /**
     * Check if user has existing requests that block deletion
     * Users can only be deleted if all their requests have 'Claimed' status or they have no requests
     * 
     * @param int $userId
     * @return array ['hasRequests' => bool, 'requestTypes' => array]
     */
    private function checkUserHasRequests($userId)
    {
        try {
            $requestTypes = [];
            $hasRequests = false;

            // Check for document requests with statuses that block deletion
            // Only 'Claimed' status allows deletion
            $blockingRequests = DocumentRequestModel::where('std_students_id', $userId)
                ->whereIn('status', ['Declined', 'Pending', 'Processing', 'For Release'])
                ->count();

            if ($blockingRequests > 0) {
                $hasRequests = true;

                // Get breakdown of blocking statuses for detailed message
                $statusCounts = DocumentRequestModel::where('std_students_id', $userId)
                    ->whereIn('status', ['Declined', 'Pending', 'Processing', 'For Release'])
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get();

                $statusDetails = [];
                foreach ($statusCounts as $statusCount) {
                    $statusDetails[] = $statusCount->count . ' ' . $statusCount->status;
                }

                $requestTypes[] = 'Document request(s): ' . implode(', ', $statusDetails);
            }

            return [
                'hasRequests' => $hasRequests,
                'requestTypes' => $requestTypes
            ];
        } catch (Exception $e) {
            Log::error('Error checking user requests: ' . $e->getMessage());
            // Return true to prevent deletion if we can't check properly
            return [
                'hasRequests' => true,
                'requestTypes' => ['Unable to verify request status - deletion blocked for safety']
            ];
        }
    }

    /**
     * Check if user has existing requests
     * Adjust this method based on your actual request tables
     */

    public function show($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                abort(404, 'Invalid user ID');
            }

            $user = Account::with('roles')->find($id);

            if (!$user) {
                abort(404, 'User not found');
            }

            return view('maintenance.showUsers', compact('user'));
        } catch (Exception $e) {
            Log::error('Error in show method: ' . $e->getMessage());
            abort(500, 'An error occurred while loading user details');
        }
    }

    public function create()
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));
        try {
            return view('common.studentregister2');
        } catch (Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the registration form.');
        }
    }

    // Store the account information and link it with the student information
    public function store(Request $request)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));
        $otp = "{$request->first}{$request->second}{$request->third}{$request->fourth}{$request->fifth}{$request->sixth}";
        $otpCode = session('otp');
        $expiry = session('expiresAt') ? \Carbon\Carbon::parse(session('expiresAt')) : null;

        if (!$otpCode || !$expiry || now()->greaterThan($expiry)) {
            session()->flash('error', 'OTP Expired. Please request a new one');
            session()->forget(['otp', 'countdown_start', 'durationInSeconds', 'expiresAt', 'email']);
            return view('common.verifyEmail')->with('error', 'Invalid or Expired Code.');
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

            return redirect()->route('login')->with('success', 'Account Created Successfully');  // Redirect to dashboard or another page
        }
        session()->flash('error', 'Invalid or expired OTP');
        return view('common.verifyEmail');
    }

    public function showOtp()
    {
        $email = session('email_address');
        $username = session('username');
        $password = session('password');
        return view('common.OTP.adminOtp', compact('email', 'username', 'password'));
    }

    const MAX_OTP_ATTEMPTS = 3;
    const LOCKOUT_DURATION_MINUTES = 15;
    const OTP_EXPIRY_SECONDS = 180; // 3 minutes

    public function viewOtp(Request $request)
    {
        // dd($request->username);
        $request->validate([
            // Validation for personal information
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'MiddleName' => 'nullable|string|max:255',
            'LRN' => 'sometimes|required_if:role,1|string|max:12|unique:std_students,LRN',
            'Grade_level' => 'sometimes|required_if:role,1|string|max:50',
            'Std_status' => 'sometimes|required_if:role,1|string|max:50',
            'Last_sy_attended' => 'sometimes|required_if:role,1|string|max:9',
            'role' => 'required',

            // Validation for account information
            'email_address' => 'required|email|unique:acc_users,email_address',
            'username' => 'required|string|max:255|unique:acc_users,username',
            'password' => 'required|string|min:8|max:20|confirmed:password_confirmation'

        ], [
            'FirstName.required' => 'Please enter your first name.',
            'LastName.required' => 'Please enter your last name.',
            'LRN.digits' => 'LRN must be exactly 12 digits.',
            'LRN.unique' => 'This LRN already exists',
            'role.required' => 'Please select a role.',
            'Last_sy_attended.digits' => 'Last school year must be 4 digits (e.g. 2024).',
            'email_address.unique' => 'This email already exists',
            'username.unique' => 'This username already exists',
        ]);

        session([
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'MiddleName' => $request->MiddleName,
            'Suffix' => $request->Suffix,
            'LRN' => $request->LRN,
            'Grade_level' => $request->Grade_level,
            'Std_status' => $request->Std_status,
            'Last_sy_attended' => $request->Last_sy_attended,
            'role' => $request->role,
            'email_address' => $request->email_address,
            'username' => $request->username,
            'password' => $request->password,
        ]);

        // $request->validate([
        //     'email_address' => 'email|required|unique:acc_users,email_address',
        //     'username' => 'required|string|unique:acc_users,username'
        // ], [
        //     'email_address.unique' => 'This email already exists',
        //     'username.unique' => 'This username already exists',
        // ]);

        $email = $request->email_address;
        $username = $request->username;
        $password = $request->password;

        // Check if user is currently locked out
        if ($this->isLockedOut($email)) {
            $lockoutEnd = session("lockout_until_{$email}");
            $remainingTime = now()->diffInMinutes($lockoutEnd, false);

            return view('common.OTP.adminOtp', compact('email', 'username', 'password'))
                ->with('error', "Account temporarily locked. Try again in {$remainingTime} minutes.");
        }

        // Generate and send new OTP
        $this->generateAndSendOTP($email, $username, $password);

        return view('common.OTP.adminOtp', compact('email', 'username', 'password'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'first' => 'required|digits:1',
            'second' => 'required|digits:1',
            'third' => 'required|digits:1',
            'fourth' => 'required|digits:1',
            'fifth' => 'required|digits:1',
            'sixth' => 'required|digits:1',
        ]);

        $email = session('email');
        $enteredOtp = $request->first . $request->second . $request->third .
            $request->fourth . $request->fifth . $request->sixth;

        // Check if user is locked out
        if ($this->isLockedOut($email)) {
            return back()->with('error', 'Account is temporarily locked due to too many failed attempts.');
        }

        // Check if OTP has expired
        if ($this->isOtpExpired()) {
            $this->handleExpiredOtp($email);
            return back()->with('error', 'OTP has expired. Account temporarily locked.');
        }

        // Verify OTP
        if (session('otp') != $enteredOtp) {
            return $this->handleFailedAttempt($email);
        }

        // OTP is correct - clear all session data and proceed

        // dd([
        //     'request_username' => $request->username,
        //     'session_username' => session('username'),
        //     'all_session' => session()->all(),
        // ]);
        // Create the user account here
        $studentId = StudentInformationModel::create([
            'FirstName' => session('FirstName'),
            'LastName' => session('LastName'),
            'MiddleName' => session('MiddleName'),
            'Suffix' => session('Suffix'),
            'LRN' => session('LRN') ?? '0000',
            'Grade_level' => session('Grade_level') ?? '0',
            'Std_status' => session('Std_status') ?? 'NA',
            'Last_sy_attended' => session('Last_sy_attended') ?? '0000',
        ])->id;

        Account::create([
            'user_account_id' => $studentId,
            'std_students_id' => $studentId,
            'role_id' => session('role'),
            'email_address' => session('email_address'),
            'username' => session('username'),
            'password' => bcrypt(session('password')),
        ]);

        $this->clearOtpSession($email);
        return redirect('userStud/add')->with('Status', 'Account created successfully!');
    }

    public function SendAgainOTP(Request $request)
    {
        try {
            $email = $request->input('email') ?? $request->query('email');
            $username = $request->input('username') ?? $request->query('username');
            $password = $request->input('password') ?? $request->query('password');

            // Check if user is locked out
            if ($this->isLockedOut($email)) {
                $message = 'Account is temporarily locked. Please wait before requesting a new code.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 423); // 423 Locked
                }
                return back()->with('error', $message);
            }

            // Check rate limiting (1 minute between requests)
            $lastRequest = session('last_otp_request');
            if ($lastRequest && now()->diffInSeconds($lastRequest) < 60) {
                $message = 'Please wait before requesting another code.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 429);
                }
                return back()->with('error', $message);
            }

            // Reset attempt counter when resending (give user fresh chances)
            session()->forget("otp_attempts_{$email}");

            // Generate and send new OTP
            $this->generateAndSendOTP($email, $username, $password);

            $message = 'New verification code sent to your email!';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'expiry' => session('expiresAt')->toISOString()
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('OTP resend failed: ' . $e->getMessage());

            $message = 'Failed to send verification code. Please try again.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            return back()->with('error', $message);
        }
    }

    private function generateAndSendOTP($email, $username = null, $password = null)
    {
        // Clear previous OTP session data
        session()->forget(['otp', 'countdown_start', 'durationInSeconds', 'expiresAt']);

        $otpCode = rand(100000, 999999);
        $startTime = now();
        $expiresAt = $startTime->copy()->addSeconds(self::OTP_EXPIRY_SECONDS);

        session([
            'otp' => $otpCode,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'countdown_start' => $startTime,
            'durationInSeconds' => self::OTP_EXPIRY_SECONDS,
            'expiresAt' => $expiresAt,
            'last_otp_request' => now()
        ]);

        // Send email
        Mail::to($email)->send(new VerifyMail($otpCode));
    }

    private function handleFailedAttempt($email)
    {
        $attemptKey = "otp_attempts_{$email}";
        $attempts = session($attemptKey, 0) + 1;
        session([$attemptKey => $attempts]);

        if ($attempts >= self::MAX_OTP_ATTEMPTS) {
            $this->lockoutUser($email);
            return back()->with('error', 'Too many failed attempts. Account temporarily locked for ' . self::LOCKOUT_DURATION_MINUTES . ' minutes.');
        }

        $remaining = self::MAX_OTP_ATTEMPTS - $attempts;
        return redirect('account/otp')->with('error', "Invalid OTP. {$remaining} attempt(s) remaining.");
    }

    private function handleExpiredOtp($email)
    {
        // When OTP expires, lock the user out
        $this->lockoutUser($email);
        $this->clearOtpSession($email);
    }

    private function lockoutUser($email)
    {
        $lockoutUntil = now()->addMinutes(self::LOCKOUT_DURATION_MINUTES);
        session([
            "lockout_until_{$email}" => $lockoutUntil,
            "otp_attempts_{$email}" => self::MAX_OTP_ATTEMPTS
        ]);
    }

    private function isLockedOut($email)
    {
        $lockoutUntil = session("lockout_until_{$email}");

        if (!$lockoutUntil) {
            return false;
        }

        if (now()->greaterThan($lockoutUntil)) {
            // Lockout has expired, clear it
            session()->forget([
                "lockout_until_{$email}",
                "otp_attempts_{$email}"
            ]);
            return false;
        }

        return true;
    }

    private function isOtpExpired()
    {
        $expiresAt = session('expiresAt');
        return $expiresAt && now()->greaterThan($expiresAt);
    }

    private function clearOtpSession($email)
    {
        session()->forget([
            'otp',
            'email',
            'username',
            'password',
            'countdown_start',
            'durationInSeconds',
            'expiresAt',
            'last_otp_request',
            "otp_attempts_{$email}",
            "lockout_until_{$email}"
        ]);
    }

    // Method to check lockout status (for frontend)
    public function checkLockoutStatus(Request $request)
    {
        $email = $request->input('email');

        if ($this->isLockedOut($email)) {
            $lockoutEnd = session("lockout_until_{$email}");
            $remainingMinutes = now()->diffInMinutes($lockoutEnd, false);

            return response()->json([
                'locked' => true,
                'remaining_minutes' => $remainingMinutes,
                'message' => "Account locked for {$remainingMinutes} more minutes."
            ]);
        }

        return response()->json([
            'locked' => false,
            'attempts' => session("otp_attempts_{$email}", 0),
            'max_attempts' => self::MAX_OTP_ATTEMPTS
        ]);
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
        // Get the role to determine validation rules
        $roleId = $request->input('role');

        // Base validation rules
        $rules = [
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'MiddleName' => 'required|string|max:255',
            'Suffix' => 'nullable|string|max:50',
            'role' => 'required|exists:role,id', // Assuming you have a roles table
            'email_address' => 'required|email|unique:acc_users,email_address',
            'username' => 'required|string|max:255|unique:acc_users,username',
            'password' => 'required|min:8|confirmed'
        ];

        // Conditional validation for student fields based on role
        if ($roleId == 1) {
            // Role ID = 1: All student fields are required and must be actual values
            $rules['LRN'] = 'required|digits:12|unique:std_students,LRN';
            $rules['Grade_level'] = 'required|string|max:50';
            $rules['Std_status'] = 'required|string|max:50';
            $rules['Last_sy_attended'] = 'required|digits:4';
        } else {
            // Check if this role is a student role (but not role ID = 1)
            $role = \App\Models\RolesModel::find($roleId); // Adjust model name as needed
            if ($role && stripos($role->name, 'student') !== false) {
                // Other student roles: fields are optional or have default values
                $rules['LRN'] = 'nullable|digits:12';
                $rules['Grade_level'] = 'nullable|string|max:50';
                $rules['Std_status'] = 'nullable|string|max:50';
                $rules['Last_sy_attended'] = 'nullable|digits:4';
            }
            // For non-student roles, these fields will be ignored
        }

        $request->validate($rules);

        // Prepare student data with conditional defaults
        $studentData = [
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'MiddleName' => $request->MiddleName,
            'Suffix' => $request->Suffix,
        ];

        // Handle student-specific fields based on role
        $role = \App\Models\RolesModel::find($roleId);
        $isStudentRole = $role && stripos($role->name, 'student') !== false;

        if ($roleId == 1) {
            // Role ID = 1: Use actual user input
            $studentData['LRN'] = $request->LRN;
            $studentData['Grade_level'] = $request->Grade_level;
            $studentData['Std_status'] = $request->Std_status;
            $studentData['Last_sy_attended'] = $request->Last_sy_attended;
        } elseif ($isStudentRole) {
            // Other student roles: Use default values (not null)
            $studentData['LRN'] = '000000000000';
            $studentData['Grade_level'] = 'N/A'; // Use string instead of null
            $studentData['Std_status'] = 'N/A';   // Use string instead of null
            $studentData['Last_sy_attended'] = '0000';
        } else {
            // Non-student roles: Use default values (since LRN cannot be null)
            $studentData['LRN'] = '000000000000'; // Default value instead of null
            $studentData['Grade_level'] = 'N/A';
            $studentData['Std_status'] = 'N/A';
            $studentData['Last_sy_attended'] = '0000';
        }

        // Store student
        $studentId = StudentInformationModel::create($studentData)->id;

        // Create account
        $account = Account::create([
            'user_account_id' => $studentId,
            'std_students_id' => $studentId,
            'role_id' => $request->role,
            'email_address' => $request->email_address,
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        // 🔑 Fire Laravel's Registered event → sends verification email
        event(new Registered($account));

        return redirect()->route('verification.notice')
            ->with('Status', 'Account created! Please verify your email.');
    }

    public function saveFcmToken(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string|max:255',
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
            if (!$user->save()) {
                throw new Exception('Failed to save FCM token');
            }

            return redirect()->back()->with('success', 'FCM token updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in saveFcmToken method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating FCM token.');
        }
    }
}
