<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequestModel;
use App\Models\Account;
use App\Models\StudentInformationModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ClaimerModel;
use App\Models\DocumentsModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class DocumentRequestController extends Controller
{
    // ============================
    // READ FUNCTIONS
    // ============================

    public function index()
    {
        $PermissionPending = PermissionRoleModel::getPermission('completed', Auth::user()->role_id);
        if (empty($PermissionPending)) {
            abort(404);
        }

        $data = PermissionRoleModel::getPermission('editCompleted', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('deleteCompleted', Auth::user()->role_id);

        $totalCount = DocumentRequestModel::where('status', 'For Release')->count();

        $DocRequests = DocumentRequestModel::where('status', 'For Release')
            ->with('claimer', 'studentInformation')
            ->orderBy('req_no', 'asc')
            ->paginate(9);

        return view('requestTables.completed.completed', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'PermissionEdit' => $data,
            'deleteCompleted' => $data1
        ]);
    }

    public function show($id)
    {
        Log::info('Requested ID: ' . $id);
        $table = DocumentRequestModel::with(['claimer', 'studentInformation'])->find($id);

        if (!$table) {
            Log::error('No record found for ID: ' . $id);
            return response()->json(['error' => 'Record not found'], 404);
        }

        return view('requestTables.completed.showTable', compact('table'));
    }

    public function showRequestForm()
    {
        $PermissionWalk = PermissionRoleModel::getPermission('walkinRequest', Auth::user()->role_id);
        if (empty($PermissionWalk)) {
            abort(404);
        }

        $DocType = DocumentsModel::all();
        $grade = ['7', '8', '9', '10', '11', '12'];
        $stat = ['Alumni', 'Regular', 'ALS'];

        return view('requestTables.walkin', compact('DocType', 'grade', 'stat'));
    }

    // ============================
    // CRUD FUNCTIONS
    // ============================

    public function create()
    {
        return view('requestTables.completed.createTable');
    }

    public function store(Request $request)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $validated = $this->validateDocumentRequest($request);
        DocumentRequestModel::createDocumentRequest($validated);

        return redirect('/tables')->with('Status', 'Created Successfully');
    }

    public function edit(DocumentRequestModel $table)
    {
        if (!$table) {
            abort(404, 'Document Request not found.');
        }

        $DocType = DocumentsModel::all();
        return view('requestTables.completed.editTable', compact('table', 'DocType'));
    }

    public function update(Request $request, DocumentRequestModel $documentRequestModel)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $validated = $this->validateDocumentRequest($request);
        DocumentRequestModel::updateOrCreateRequest($validated);

        return redirect('/tables')->with('Status', 'Updated Successfully');
    }

    public function completeRequest(Request $request, $id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        try {
            $request->validate([
                'claimer_first_name' => 'max:255',
                'claimer_last_name' => 'max:255',
                'claimer_contact' => 'max:50',
            ]);

            $documentRequest = DocumentRequestModel::findOrFail($id);
            $claimer = $documentRequest->clm_claimers_id ? ClaimerModel::find($documentRequest->clm_claimers_id) : null;

            $account = $documentRequest->account;
            $stud = $documentRequest->studentInformation;

            $email = $account->email_address;
            $name = $stud->full_name;
            $subject = 'Your Request is Approved and Completed!';

            try {
                Mail::send('emails.toClaimed', compact('subject', 'name'), function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
            } catch (\Exception $e) {
                Log::error('Email failed: ' . $e->getMessage());
            }

            if ($account->fcm_token) {
                try {
                    Http::withHeaders([
                        'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ])->post('https://onesignal.com/api/v1/notifications', [
                        'app_id' => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf',
                        'include_player_ids' => [$account->fcm_token],
                        'contents' => ['en' => $name . ', Your document request has been approved and Processed.'],
                    ]);
                } catch (\Exception $e) {
                    Log::error('Push notification failed: ' . $e->getMessage());
                }
            }

            $documentRequest->update([
                'status' => 'Claimed',
                'claimed_date' => Carbon::now(),
            ]);

            if ($claimer) {
                $claimer->update([
                    'Fname' => $request->claimer_first_name,
                    'Lname' => $request->claimer_last_name,
                    'contact_no' => $request->claimer_contact,
                ]);
            }

            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Document marked as claimed.'])
                : redirect('/tables')->with('Status', 'Completed Successfully');

        } catch (\Exception $e) {
            Log::error('completeRequest Error: ' . $e->getMessage());
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Error processing request.'], 500)
                : redirect()->back()->with('Danger', 'An error occurred while processing the request.');
        }
    }

    public function destroy($id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $table = DocumentRequestModel::find($id);
        if ($table) {
            $table->delete();
            return redirect('/tables')->with('Danger', 'Deleted Successfully');
        }
        return redirect('/tables')->with('error', 'Record not found');
    }

    // ============================
    // WALK-IN STORE
    // ============================

    public function storeWalkIn(Request $request)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $validated = $request->validate([
            'request_schl_entity' => 'required|string|max:255',
            'document_id' => 'required|exists:doc_categories,id',
            'release_mode' => 'required|string|max:255',

            'student_first_name' => 'required|string|max:255',
            'student_last_name' => 'required|string|max:255',
            'lrn' => 'max:12',
            'grade_level' => 'required|string|max:50',
            'student_status' => 'required|string|max:20',
            'last_sy_attended' => 'required|string|max:50',
            'email_address' => 'required|string|max:100',
        ]);

        $claimer = ClaimerModel::updateOrCreate(
            ['Fname' => 'Blank', 'Lname' => 'Blank'],
            ['contact_no' => '000000']
        );

        // Check if email address is unique
        if (Account::where('email_address', $request->email_address)->exists()) {

            $idAcc = Account::where('email_address', $request->email_address)->value('user_account_id');
            DocumentRequestModel::create([
            'id' => random_int(10000, 99999),
            'clm_claimers_id' => $claimer->id,
            'std_students_id' => $idAcc,
            'doc_categories_id' => $validated['document_id'],
            'request_time' => now()->format('H:i:s'),
            'request_date' => now()->toDateString(),
            'request_schl_entity' => $validated['request_schl_entity'],
            'release_mode' => $validated['release_mode'],
            'remarks' => 'Pending',
            'status' => 'Pending',
            'request_mode' => 'Online',
            ]);

            return redirect()->route('walkin.form')->with('Success', 'Document request submitted successfully!');
        }


        $student = StudentInformationModel::create(
            [
                'FirstName' => $validated['student_first_name'],
                'LastName' => $validated['student_last_name'],
                'LRN' => $validated['lrn'] ?? 0000,
            ],
            [
                'Grade_level' => $validated['grade_level'],
                'Std_status' => $validated['student_status'],
                'Last_sy_attended' => $validated['last_sy_attended'],
            ]
        );
            $tempPassword = Str::random(10);

            Account::create([
                'user_account_id' => $student->id,
                'std_students_id' => $student->id,
                'role_id' => 1,
                'email_address' => $validated['email_address'],
                'username' => $validated['student_first_name'] . $validated['student_last_name'],
                'password' => bcrypt($tempPassword),
            ]);

        $subject = 'Your Temporary Password';
        $name = $validated['student_first_name'] . ' ' . $validated['student_last_name'];
        $email = $validated['email_address'];

        // Send email
        Mail::send('emails.tempPassword', compact('subject', 'name', 'tempPassword'), function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });

        DocumentRequestModel::create([
            'id' => random_int(10000, 99999),
            'clm_claimers_id' => $claimer->id,
            'std_students_id' => $student->id,
            'doc_categories_id' => $validated['document_id'],
            'request_time' => now()->format('H:i:s'),
            'request_date' => now()->toDateString(),
            'request_schl_entity' => $validated['request_schl_entity'],
            'release_mode' => $validated['release_mode'],
            'remarks' => 'Pending',
            'status' => 'Pending',
            'request_mode' => 'Online',
        ]);


        return redirect()->route('walkin.form')->with('Success', 'Document request submitted successfully!');
    }

    // ============================
    // VALIDATION FUNCTION
    // ============================

    public function validateDocumentRequest(Request $request)
    {
        return $request->validate([
            'id' => 'required',
            'claimer_id' => 'required',
            'document_id' => 'required',
            'request_schl_entity' => 'required|string|max:255',
            'request_mode' => 'required|string|max:255',
            'release_mode' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'status' => 'required|string',
        ]);
    }

    // ============================
    // DEBUG
    // ============================

    public function trylang(Request $request)
    {
        dd("sadasda");
    }
<<<<<<< HEAD
=======

    public function storeWalkIn(Request $request)
    {
        $validated = $request->validate([
            // Document Request Validation
            'request_schl_entity' => 'required|string|max:255',
            'document_id' => 'required|exists:doc_categories,id',
            'release_mode' => 'required|string|max:255',

            // Student Information Validation
            'student_first_name' => 'required|string|max:255',
            'student_last_name' => 'required|string|max:255',
            'lrn' => 'max:12',
            'grade_level' => 'required|string|max:50',
            'student_status' => 'required|string|max:20',
            'last_sy_attended' => 'required|string|max:50',
        ]);

        $claimer = ClaimerModel::updateOrCreate(
            [
                'Fname' => 'Blank',
                'Lname' => 'Blank',
            ],
            [
                'contact_no' => '000000',
            ]
        );


        // Create or update the Student Information record
        $student = StudentInformationModel::updateOrCreate(
            [
                'FirstName' => $validated['student_first_name'],
                'LastName' => $validated['student_last_name'],
                'LRN' => $validated['lrn'] ?? 0000,
            ],
            [
                'Grade_level' => $validated['grade_level'],
                'Std_status' => $validated['student_status'], // Added student_status
                'Last_sy_attended' => $validated['last_sy_attended'],
            ]
        );

        // Create the Document Request record
        DocumentRequestModel::create([
            'id' => random_int(10000, 99999),
            'clm_claimers_id' => $claimer->id,
            'std_students_id' => $student->id,
            'doc_categories_id' => $validated['document_id'],
            'request_time' => now()->format('H:i:s'),
            'request_date' => now()->toDateString(),
            'request_schl_entity' => $validated['request_schl_entity'],
            'release_mode' => $validated['release_mode'],
            'remarks' => 'Pending', // Default remarks
            'status' => 'Pending', // Default status
        ]);

        return redirect()->route('walkin.form')->with('success', 'Document request submitted successfully!');
    }

    public function showRequestForm()
    {
        $PermissionWalk = PermissionRoleModel::getPermission('walkinRequest', Auth::user()->role_id);
        if(empty($PermissionWalk))
        {
            abort(404);
        }
        // Fetch the document types (assuming the DocType is stored in the doc_categories table)
        $DocType = DocumentsModel::all();

        // Grade levels and statuses
        $grade = ['7', '8', '9', '10', '11', '12'];
        $stat = ['Alumni', 'Regular', 'ALS'];

        // Return the view with necessary data
        return view('requestTables.walkin', compact('DocType', 'grade', 'stat'));
    }

    public function completeRequest(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'claimer_first_name' => 'max:255',
                'claimer_last_name' => 'max:255',
                'claimer_contact' => 'max:50',
            ]);

            // Find the document request by ID
            $documentRequest = DocumentRequestModel::findOrFail($id);

            // Check if claimer exists - but don't stop execution with dd()
            $claimer = null;
            if ($documentRequest->clm_claimers_id) {
                $claimer = ClaimerModel::find($documentRequest->clm_claimers_id);
            }


            // Log claimer info instead of using dd()
            if ($claimer) {
                Log::info('Claimer found: ' . $claimer->id);
            } else {
                Log::info('No claimer found for document request: ' . $id);
            }

            $account = $documentRequest->account;
            $stud = $documentRequest->studentInformation;

            $email = $account->email_address;
            $name = $stud->full_name;
            $subject = 'Your Request is Approved and Completed!';
            Log::info("Sending email to: " . $account->email_address);

            // Send email notification
            try {
                Mail::send('emails.toClaimed', compact('subject', 'name'), function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
                Log::info('Email sent successfully to: ' . $email);
            } catch (\Exception $e) {
                Log::error('Email sending failed: ' . $e->getMessage());
                // Don't stop execution, just log the error
            }

            // // Send push notification via OneSignal
            // $pushId = $account->fcm_token;

            // if ($pushId) {
            //     try {
            //         $response = Http::withHeaders([
            //             'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
            //             'accept' => 'application/json',
            //             'content-type' => 'application/json',
            //         ])->post('https://onesignal.com/api/v1/notifications', [
            //             'app_id' => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf',
            //             'include_player_ids' => [$pushId],
            //             'contents' => ['en' => $name . ', Your document request has been approved and Processed.'],
            //         ]);

            //         Log::info('Notification sent: ' . $response->body());
            //     } catch (\Exception $e) {
            //         Log::error('Push notification failed: ' . $e->getMessage());
            //         // Don't stop execution, just log the error
            //     }
            // }

            // Update the document request status to 'Claimed'
            $documentRequest->update([
                'status' => 'Claimed',
                'claimed_date' => Carbon::now(),
                'claimed_time' => Carbon::now()->format('H:i:s'), // Store the current time
            ]);

            // Update claimer if exists
            if ($claimer) {
                $claimer->update([
                    'Fname' => $request->claimer_first_name,
                    'Lname' => $request->claimer_last_name,
                    'contact_no' => $request->claimer_contact,
                ]);
            }

            // Handle both AJAX and regular requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document has been successfully marked as claimed.',
                    'redirect' => route('tables.index') // or wherever you want to redirect
                ]);
            }

            return redirect('/tables')->with('Status', 'Completed Successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in completeRequest: ' . json_encode($e->errors()));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('Danger', 'Please check the form for errors.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Model not found in completeRequest: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found.',
                ], 404);
            }

            return redirect()->back()->with('Danger', 'Document request not found.');

        } catch (\Exception $e) {
            Log::error('Error in completeRequest: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing the request. Please try again.',
                ], 500);
            }

            return redirect()->back()->with('Danger', 'An error occurred while processing the request. Please try again.');
        }

    }


>>>>>>> b8dc7111 (Audit Changes)
}
