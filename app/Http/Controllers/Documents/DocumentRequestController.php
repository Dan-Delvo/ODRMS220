<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
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
use App\Models\DocuPaymentFee;
use App\Models\Guest;
use App\Service\AuditTrailLogger;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class DocumentRequestController extends Controller
{
    // ============================
    // READ FUNCTIONS
    // ============================

    public function index(Request $request)
    {
        $PermissionPending = PermissionRoleModel::getPermission('completed', Auth::user()->role_id);
        if (empty($PermissionPending)) {
            abort(404);
        }

        $data = PermissionRoleModel::getPermission('editCompleted', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('deleteCompleted', Auth::user()->role_id);

        // Prepare search options
        $searchOptions = [
            'search' => $request->get('search'),
            'filter' => $request->get('filter', 'all'),
            'sort' => $request->get('sort', 'default'),
            'per_page' => 10
        ];

        // Get document requests with search/filter/sort
        $DocRequests = DocumentRequestModel::getDocumentRequests('For Release', $searchOptions);

        // Get total count (unfiltered)
        $totalCount = DocumentRequestModel::getStatusCount('For Release');

        // Get filtered count (with search/filter applied)
        $filteredCount = $DocRequests->total();

        // Get search counts across all statuses
        $searchCounts = DocumentRequestModel::getSearchCountsAcrossAllStatuses($request->get('search') ?? '');

        return view('requestTables.completed.completed', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
            'searchCounts' => $searchCounts,
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

    public function store(Request $request, AuditTrailLogger $logger)
    {
        $logger->logger();

        $validated = $this->validateDocumentRequest($request);
        DocumentRequestModel::createDocumentRequest($validated);

        return redirect('/tables')->with('Status', 'Created Successfully');
    }

    public function edit(DocumentRequestModel $table, AuditTrailLogger $logger)
    {
        $logger->logger();

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

        $validated = $request->validate([
            'app_date' => 'required|date|before_or_equal:today',
            'rel_date' => 'required|date|after_or_equal:app_date|before_or_equal:today',
        ]);
        DocumentRequestModel::where('id', $request->id)
            ->update([
                'approve_date' => $validated['app_date'],
                'forRelease_date' => $validated['rel_date'],
            ]);
        return redirect('/tables')->with('Status', 'Updated Successfully');
    }

    public function completeRequest(Request $request, $id)
    {
        $pdo = DB::connection()->getPdo();
        // Set user for database auditing purposes
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        try {
            // Validate incoming request: MADE CLAIMER NAME FIELDS REQUIRED
            $request->validate([
                'claimer_first_name' => 'required|string|max:255',
                'claimer_last_name'  => 'required|string|max:255',
                'claimer_date'       => 'required|date|before_or_equal:today',
            ], [
                'claimer_first_name.required'  => 'The claimer\'s first name is required.',
                'claimer_last_name.required'   => 'The claimer\'s last name is required.',
                'claimer_date.before_or_equal' => 'The claimed date cannot be in the future.',
            ]);

            // Find the document request
            $documentRequest = DocumentRequestModel::findOrFail($id);

            // Get account and student info
            $account = $documentRequest->account;
            $stud    = $documentRequest->studentInformation;

            // --- CLAIMER LOGIC (REVISED) ---
            $claimerData = [
                'Fname'        => $request->claimer_first_name,
                'Lname'        => $request->claimer_last_name,
                'contact_no'   => $request->input('claimer_contact_no', 'N/A'), // Use a default if not collected in modal
            ];

            // Find or Create the Claimer based on name combination
            $claimer = ClaimerModel::firstOrCreate([
                'Fname' => $claimerData['Fname'],
                'Lname' => $claimerData['Lname'],
            ], $claimerData);

            // --- END CLAIMER LOGIC ---


            // Handle claimed date + time
            $selectedDate = $request->input('claimer_date');
            $today        = now()->toDateString();
            $claimedTime  = ($selectedDate === $today) ? now()->format('H:i:s') : null;


            $documentRequest->update([
                'remarks'         => 'Claimed',
                'status'          => 'Claimed',
                'claimed_date'    => $selectedDate,
                'claimed_time'    => $claimedTime,
                'clm_claimers_id' => $claimer->id, // CRITICAL: Link the claimer to the request
            ]);

            Log::info("Document request {$id} marked as claimed on {$selectedDate}. Claimer ID: {$claimer->id}");

            $guest = Guest::where('doc_request_id', $id)->first();
            $isGuestRequest = !is_null($guest);

            if ($isGuestRequest) {
                // Guest request - use guest email
                $email = $guest->email_address ?? 'nubzman123@gmail.com';
                $name = $stud->full_name ?? $guest->name ?? 'Guest';
                Log::info("Processing guest request notification for ID: {$id}, Email: {$email}");
            } else {
                // Regular account request - use account email
                $email = $account->email_address ?? 'nubzman123@gmail.com';
                $name = $stud->full_name ?? ' ';
                Log::info("Processing account request notification for ID: {$id}, Email: {$email}");
            }

            $subject = 'Your Request is Approved and Completed!';

            // Send Email (for both guest and account requests)
            try {
                Mail::send('emails.toClaimed', compact('subject', 'name'), function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
                Log::info("Email sent successfully to {$email} for request ID: {$id}");
            } catch (\Exception $e) {
                $requestType = $isGuestRequest ? "guest request" : "account {$account->id}";
                Log::error("Email failed for {$requestType} ({$email}): " . $e->getMessage());
            }

            // Send Push Notification (ONLY for account requests, NOT for guest requests)
            if (!$isGuestRequest && $account && $account->fcm_token) {
                try {
                    $response = Http::withHeaders([
                        // Replace with your actual authorization header
                        'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
                        'accept'        => 'application/json',
                        'content-type'  => 'application/json',
                    ])->post('https://onesignal.com/api/v1/notifications', [
                        'app_id'             => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf', // Replace with your actual App ID
                        'include_player_ids' => [$account->fcm_token],
                        'contents'           => ['en' => "{$name}, Your document request has been approved and processed."],
                    ]);
                    Log::info("Push notification sent for account request ID: {$id}. Response: " . $response->body());
                } catch (\Exception $e) {
                    Log::error("Push notification failed for account {$account->id}: " . $e->getMessage());
                }
            } else {
                $reason = $isGuestRequest ? "guest request (no push notification for guests)" : "no account or FCM token available";
                Log::info("Push notification skipped for request ID: {$id} - {$reason}");
            }

            // --- END NOTIFICATION LOGIC ---


            // Return appropriate response based on request type
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document marked as claimed successfully!',
                    'redirect' => route('tables.index')
                ], 200);
            }

            return redirect()->route('tables.index')
                ->with('Status', 'Document marked as claimed successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in completeRequest: ' . json_encode($e->errors()), ['request_id' => $id]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. ' . collect($e->errors())->flatten()->implode('; '),
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('Danger', 'Validation failed. Please check your input.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Document Request Not Found: ' . $e->getMessage(), ['request_id' => $id]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found.'
                ], 404);
            }

            return redirect()->route('tables.index')
                ->with('Danger', 'Document request not found.');
        } catch (\Exception $e) {
            Log::error('completeRequest Error: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing the request. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('Danger', 'An error occurred while processing the request. Please try again.');
        }
    }

    public function revertToProcessing(Request $request, $id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        try {
            $request->validate([
                'revert_reason' => 'required|string|max:500',
            ]);

            $documentRequest = DocumentRequestModel::findOrFail($id);

            if ($documentRequest->status !== 'For Release') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request is not in For Release status.',
                ], 400);
            }

            $documentRequest->update([
                'status' => 'Processing',
                'forRelease_date' => null,
                'remarks' => $request->revert_reason,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document has been successfully reverted to Processing status.',
                    'redirect' => route('tables.index')
                ]);
            }

            return redirect('/tables')->with('Status', 'Document reverted to Processing successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in revertToProcessing: ' . json_encode($e->errors()));

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
            Log::error('Model not found in revertToProcessing: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found.',
                ], 404);
            }

            return redirect()->back()->with('Danger', 'Document request not found.');

        } catch (\Exception $e) {
            Log::error('Error in revertToProcessing: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while reverting the document: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('Danger', 'An error occurred while reverting the document.');
        }
    }

    public function destroy($id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $table = DocumentRequestModel::find($id);
        if ($table) {
            $table->delete();
            return redirect('/declined-documents')->with('success', 'Deleted Successfully');
        }
        return redirect('/declined-documents')->with('error', 'Record not found');
    }

    // ============================
    // WALK-IN STORE
    // ============================

    public function storeWalkIn(Request $request)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        // Determine if this is a guest request FIRST
        $isGuestRequest = $request->has('requesting_for_others') && $request->requesting_for_others == '1';

        // Base validation rules
        $rules = [
            'request_schl_entity' => 'required|string|max:255',
            'document_id' => 'required|exists:doc_categories,id',
            'release_mode' => 'required|string|max:255',
            'student_first_name' => 'required|string|max:255',
            'student_last_name' => 'required|string|max:255',
            'lrn' => 'nullable|max:12',
            'grade_level' => 'required|string|max:50',
            'student_status' => 'required|string|max:20',
            'last_sy_attended' => 'required|string|max:50',
        ];

        // Conditional validation based on request type
        if ($isGuestRequest) {
            // Guest request: relationship required, email optional
            $rules['relationship'] = 'required|string|max:255';
            $rules['email_address'] = 'nullable|email|max:100';
        } else {
            // Self request: email required, no relationship needed
            $rules['email_address'] = 'required|email|max:100';
        }

        $validated = $request->validate($rules, [
            'relationship.required' => 'Requestor name is required when requesting for others.',
            'email_address.required' => 'Email address is required when requesting for yourself.',
        ]);

        // Set relationship value
        $relationshipValue = $isGuestRequest
            ? $validated['relationship']
            : ($validated['student_first_name'] . ' ' . $validated['student_last_name']);

        // Create claimer record based on request type
        if ($isGuestRequest) {
            // Guest request: Use requestor's name as claimer
            $claimerNames = explode(' ', $validated['relationship'], 2);
            $claimer = ClaimerModel::create([
                'Fname' => $claimerNames[0] ?? 'Guest',
                'Lname' => $claimerNames[1] ?? 'Requestor',
                'contact_no' => '000000'
            ]);
            Log::info("Guest request - Claimer created: " . $claimer->id);
        } else {
            // Self request: Use blank claimer
            $claimer = ClaimerModel::create([
                'Fname' => 'Blank',
                'Lname' => 'Blank',
                'contact_no' => '000000'
            ]);
            Log::info("Self request - Blank claimer created: " . $claimer->id);
        }

        // ============================================
        // GUEST REQUEST FLOW (No Account Creation)
        // ============================================
        if ($isGuestRequest) {
            Log::info("Processing GUEST request - No account will be created");

            // Check if student record exists by matching name + LRN (or just name if no LRN)
            $studentQuery = StudentInformationModel::where('FirstName', $validated['student_first_name'])
                ->where('LastName', $validated['student_last_name']);

            if (!empty($validated['lrn'])) {
                $studentQuery->where('LRN', $validated['lrn']);
            }

            $existingStudent = $studentQuery->first();

            if ($existingStudent) {
                Log::info("Existing student record found (ID: {$existingStudent->id}) - Using for guest request");
                $studentId = $existingStudent->id;
            } else {
                Log::info("Creating new student record for guest request (NO ACCOUNT)");

                $student = StudentInformationModel::create([
                    'FirstName' => $validated['student_first_name'],
                    'LastName' => $validated['student_last_name'],
                    'LRN' => $validated['lrn'] ?? '000000000000',
                    'Grade_level' => $validated['grade_level'],
                    'Std_status' => $validated['student_status'],
                    'Last_sy_attended' => $validated['last_sy_attended']
                ]);

                $studentId = $student->id;
                Log::info("New student record created (ID: {$studentId}) WITHOUT account");
            }

            // ✅ FIX: Store the ID in a variable BEFORE creating
            $docRequestId = random_int(10000, 99999);

            $docRequest = DocumentRequestModel::create([
                'id' => $docRequestId,
                'clm_claimers_id' => $claimer->id,
                'std_students_id' => $studentId,
                'doc_categories_id' => $validated['document_id'],
                'request_time' => now()->format('H:i:s'),
                'request_date' => now()->toDateString(),
                'request_schl_entity' => $validated['request_schl_entity'],
                'release_mode' => $validated['release_mode'],
                'remarks' => 'Pending',
                'status' => 'Pending',
                'request_mode' => 'Walk-in (Guest)',
                'relationship' => $validated['relationship'],
            ]);

            // ✅ FIX: Use the variable instead of $docRequest->id
            Log::info("Document request created (ID: {$docRequestId})");

            // ✅ FIX: Use $docRequestId instead of $docRequest->id
            $guest = Guest::create([
                'doc_request_id' => $docRequestId, // ✅ Use the variable
                'name' => $validated['relationship'],
                'email_address' => $validated['email_address'] ?? null,
                'contact_no' => null
            ]);

            Log::info("Guest record created (ID: {$guest->id}) linked to doc_request (ID: {$docRequestId})");

            // Send notification email if email was provided
            if (!empty($guest->email_address)) {
                Log::info("Guest request - Sending notification email to: " . $guest->email_address);

                $subject = 'Document Request Submitted - Guest Request';
                $studentName = $validated['student_first_name'] . ' ' . $validated['student_last_name'];
                $requestorName = $guest->name;
                $email = $guest->email_address;

                try {
                    Mail::send('emails.guestRequestNotification', compact('subject', 'studentName', 'requestorName', 'email'), function ($message) use ($email, $subject) {
                        $message->to($email)->subject($subject);
                    });
                    Log::info("Guest notification email sent successfully to: " . $email);
                } catch (\Exception $e) {
                    Log::error("Failed to send guest notification email: " . $e->getMessage());
                }

                return redirect()->route('walkin.form')
                    ->with('Success', 'Document request submitted successfully on behalf of ' . $studentName . '! A notification has been sent to ' . $email);
            }

            return redirect()->route('walkin.form')
                ->with('Success', 'Document request submitted successfully on behalf of ' . $validated['student_first_name'] . ' ' . $validated['student_last_name'] . '!');
        }

        // ============================================
        // SELF REQUEST FLOW (With Account Creation)
        // ============================================
        Log::info("Processing SELF request - Account creation flow");

        // Check if student account already exists by email
        $existingAccount = Account::where('email_address', $validated['email_address'])->first();

        if ($existingAccount) {
            // Account exists - just create request
            Log::info("Existing student account found: " . $existingAccount->user_account_id);

            DocumentRequestModel::create([
                'id' => random_int(10000, 99999),
                'clm_claimers_id' => $claimer->id,
                'std_students_id' => $existingAccount->user_account_id,
                'doc_categories_id' => $validated['document_id'],
                'request_time' => now()->format('H:i:s'),
                'request_date' => now()->toDateString(),
                'request_schl_entity' => $validated['request_schl_entity'],
                'release_mode' => $validated['release_mode'],
                'remarks' => 'Pending',
                'status' => 'Pending',
                'request_mode' => 'Walk-in',
                'relationship' => $relationshipValue,
            ]);

            return redirect()->route('walkin.form')
                ->with('Success', 'Document request submitted successfully!');
        }

        // Create new student record + account
        Log::info("Creating new student account with credentials");

        $student = StudentInformationModel::create([
            'FirstName' => $validated['student_first_name'],
            'LastName' => $validated['student_last_name'],
            'LRN' => $validated['lrn'] ?? '000000000000',
            'Grade_level' => $validated['grade_level'],
            'Std_status' => $validated['student_status'],
            'Last_sy_attended' => $validated['last_sy_attended']
        ]);

        // Generate temporary password
        $tempPassword = Str::random(10);

        // Create account
        Account::create([
            'user_account_id' => $student->id,
            'std_students_id' => $student->id,
            'role_id' => 1,
            'email_address' => $validated['email_address'],
            'username' => $validated['student_first_name'] . $validated['student_last_name'],
            'password' => bcrypt($tempPassword),
        ]);

        // Send temporary password email
        $subject = 'Your Temporary Password - Document Request System';
        $name = $validated['student_first_name'] . ' ' . $validated['student_last_name'];
        $email = $validated['email_address'];

        try {
            Mail::send('emails.tempPassword', compact('subject', 'name', 'tempPassword', 'email'), function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
            Log::info("Temporary password email sent to: " . $email);
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
        }

        // Create document request
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
            'request_mode' => 'Walk-in',
            'relationship' => $relationshipValue,
        ]);

        return redirect()->route('walkin.form')
            ->with('Success', 'Document request submitted successfully! Check your email for login credentials.');
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
            'status' => 'required|string'
        ]);
    }

    // ============================
    // DEBUG
    // ============================

}
