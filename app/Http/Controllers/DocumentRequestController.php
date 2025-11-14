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
use App\Models\DocuPaymentFee;
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
                'claimer_first_name' => 'required|string|max:255', // CRITICAL FIX: required
                'claimer_last_name'  => 'required|string|max:255',  // CRITICAL FIX: required
                'claimer_date'       => 'required|date|before_or_equal:today',
            ], [
                'claimer_first_name.required'  => 'The claimer\'s first name is required.',
                'claimer_last_name.required'   => 'The claimer\'s last name is required.',
                'claimer_date.before_or_equal' => 'The claimed date cannot be in the future.',
            ]);

            // Find the document request
            $documentRequest = DocumentRequestModel::findOrFail($id);

            // Get account and student info (assuming these relationships exist)
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

            // Update document request: CRITICAL FIX: Assign the Claimer ID
            $documentRequest->update([
                'remarks'         => 'Claimed',
                'status'          => 'Claimed',
                'claimed_date'    => $selectedDate,
                'claimed_time'    => $claimedTime,
                'clm_claimers_id' => $claimer->id, // CRITICAL: Link the claimer to the request
            ]);

            Log::info("Document request {$id} marked as claimed on {$selectedDate}. Claimer ID: {$claimer->id}");

            // --- NOTIFICATION LOGIC (Send Email & Push Notification) ---
            $email   = $account->email_address;
            $name    = $stud->full_name;
            $subject = 'Your Request is Approved and Completed!';

            // Send Email
            try {
                Mail::send('emails.toClaimed', compact('subject', 'name'), function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
                Log::info("Email sent successfully to {$email} for request ID: {$id}");
            } catch (\Exception $e) {
                Log::error("Email failed for account {$account->id} ({$email}): " . $e->getMessage());
            }

            // Send Push Notification
            if ($account->fcm_token) {
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
                    Log::info("Push notification sent for request ID: {$id}. Response: " . $response->body());
                } catch (\Exception $e) {
                    Log::error("Push notification failed for account {$account->id}: " . $e->getMessage());
                }
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

        $claimer = ClaimerModel::Create(
            ['Fname' => 'Blank', 'Lname' => 'Blank'],
            ['contact_no' => '000000']
        );

        Log::info("Created account: " . $claimer);

        // Check if email address is unique
        if (Account::where('email_address', $request->email_address)->exists()) {

            $document = DocumentsModel::find($validated['document_id']);
            $receipt = DocuPaymentFee::create([
                "receipt_no" => random_int(10000, 99999),
                'docu_categories_id' => $validated['document_id'],
                'doc_amount' => $document->DocPrice,
                'name_request' => Auth::user()->std_students_id,
                'time_request' => Carbon::now()
            ]);

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
                'relationship' => $request->relationship ?? ($request->student_first_name . ' ' . $request->student_last_name),
                'receipt_no' => $receipt->receipt_no
            ]);

            return redirect()->route('walkin.form')->with('Success', 'Document request submitted successfully!');
        }

            $document = DocumentsModel::find($validated['document_id']);
            $receipt = DocuPaymentFee::create([
                "receipt_no" => random_int(10000, 99999),
                'docu_categories_id' => $validated['document_id'],
                'doc_amount' => $document->DocPrice,
                'name_request' => Auth::user()->std_students_id,
                'time_request' => Carbon::now()
            ]);


        $student = StudentInformationModel::create(
            [
                'FirstName' => $validated['student_first_name'],
                'LastName' => $validated['student_last_name'],
                'LRN' => $validated['lrn'] ?? 0000,
                'Grade_level' => $validated['grade_level'],
                'Std_status' => $validated['student_status'],
                'Last_sy_attended' => $validated['last_sy_attended']
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
        Mail::send('emails.tempPassword', compact('subject', 'name', 'tempPassword', 'email'), function ($message) use ($email, $subject) {
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
            'relationship' => $request->relationship ?? ($request->student_first_name . ' ' . $request->student_last_name),
            'receipt_no' => $receipt->receipt_no
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
            'status' => 'required|string'
        ]);
    }

    // ============================
    // DEBUG
    // ============================

    public function trylang(Request $request)
    {
        dd("sadasda");
    }
}
