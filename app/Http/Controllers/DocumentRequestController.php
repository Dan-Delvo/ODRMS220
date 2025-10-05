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
use App\Services\SyncService;


class DocumentRequestController extends Controller
{

    protected $syncService;

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
            ->paginate(10);

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
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));
        try {
            $request->validate([
                'claimer_first_name' => 'nullable|string|max:255',
                'claimer_last_name'  => 'nullable|string|max:255',
                'claimer_date'       => 'required|date|before_or_equal:today',
            ], [
                'claimer_date.before_or_equal' => 'The claimed date cannot be in the future.',
            ]);

            $documentRequest = DocumentRequestModel::findOrFail($id);
            $claimer = $documentRequest->clm_claimers_id ? ClaimerModel::find($documentRequest->clm_claimers_id) : null;

            $account = $documentRequest->account;
            $stud    = $documentRequest->studentInformation;

            $email   = $account->email_address;
            $name    = $stud->full_name;
            $subject = 'Your Request is Approved and Completed!';

            // Email
            try {
                Mail::send('emails.toClaimed', compact('subject', 'name'), function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
            } catch (\Exception $e) {
                Log::error("Email failed for account {$account->id} ({$email}): " . $e->getMessage());
            }

            // Push Notification
            if ($account->fcm_token) {
                try {
                    Http::withHeaders([
                        'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
                        'accept'        => 'application/json',
                        'content-type'  => 'application/json',
                    ])->post('https://onesignal.com/api/v1/notifications', [
                        'app_id'             => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf',
                        'include_player_ids' => [$account->fcm_token],
                        'contents'           => ['en' => "{$name}, Your document request has been approved and processed."],
                    ]);
                } catch (\Exception $e) {
                    Log::error("Push notification failed for account {$account->id}: " . $e->getMessage());
                }
            }

            // Handle claimed date + time
            $selectedDate = $request->input('claimer_date');
            $today        = now()->toDateString();
            $claimedTime  = ($selectedDate === $today) ? now()->format('H:i:s') : null;

            $documentRequest->update([
                'status'       => 'Claimed',
                'claimed_date' => $selectedDate,
                'claimed_time' => $claimedTime,
            ]);

            if ($claimer) {
                $claimer->update([
                    'Fname'        => $request->claimer_first_name,
                    'Lname'        => $request->claimer_last_name,
                    'contact_no'   => "09xxxxxxxxxx",
                    'claimed_date' => $selectedDate,
                ]);
            }

            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Document marked as claimed.'])
                : redirect('/tables')->with('Status', 'Completed Successfully');
        } catch (\Exception $e) {
            Log::error('completeRequest Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
            ]);

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
            return redirect('/declined-documents')->with('Danger', 'Deleted Successfully');
        }
        return redirect('/declined-documents')->with('error', 'Record not found');
    }

    // ============================
    // WALK-IN STORE
    // ============================

    public function viewSync() {

        return view('requestTables.sync');
    }

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function storeWalkIn(Request $request)
    {
        // ALWAYS USE LOCAL DATABASE - NEVER CHECK IF ONLINE
        $connection = 'mysql_local';

        Log::info('=== WALKIN FORM SUBMITTED ===');
        Log::info('Request Data: ', $request->all());

        // Enable query logging
        DB::connection('mysql_local')->enableQueryLog();

        // ALWAYS USE LOCAL DATABASE
        $connection = 'mysql_local';

        $pdo = DB::connection($connection)->getPdo();
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

        DB::connection($connection)->beginTransaction();

        try {
            // Get or create claimer (always in local database)
            $claimer = ClaimerModel::on($connection)->updateOrCreate(
                ['Fname' => 'Blank', 'Lname' => 'Blank'],
                [
                    'contact_no' => '000000',
                    'synced' => false, // Always mark as not synced initially
                ]
            );

            // Check if email address already exists in LOCAL database
            $existingAccount = Account::on($connection)
                ->where('email_address', $request->email_address)
                ->first();

            if ($existingAccount) {
                // Account exists locally, just create document request
                DocumentRequestModel::on($connection)->create([
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
                    'synced' => false, // Always mark as not synced
                        'receipt_no' => 0, // <--- ADD THIS LINE

                ]);

                DB::connection($connection)->commit();

                return redirect()->route('walkin.form')
                    ->with('Success', 'Document request saved locally. Use sync button to upload to online database.');
            }

            // Create new student in LOCAL database
            $student = StudentInformationModel::on($connection)->create([
                'FirstName' => $validated['student_first_name'],
                'LastName' => $validated['student_last_name'],
                'LRN' => $validated['lrn'] ?? '0000',
                'Grade_level' => $validated['grade_level'],
                'Std_status' => $validated['student_status'],
                'Last_sy_attended' => $validated['last_sy_attended'],
                'synced' => false, // Not synced yet
            ]);

            // Generate temporary password
            $tempPassword = Str::random(10);

            // Create account in LOCAL database
            Account::on($connection)->create([
                'user_account_id' => $student->id,
                'std_students_id' => $student->id,
                'role_id' => 1,
                'email_address' => $validated['email_address'],
                'username' => $validated['student_first_name'] . $validated['student_last_name'],
                'password' => bcrypt($tempPassword),
                'synced' => false, // Not synced yet
            ]);

            // Store temp password for later sync (you might want to create a temp_passwords table)
            DB::connection($connection)->table('temp_passwords')->insert([
                'email_address' => $validated['email_address'],
                'temp_password' => $tempPassword,
                'created_at' => now(),
            ]);

            // Create document request in LOCAL database
            DocumentRequestModel::on($connection)->create([
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
                'synced' => false, // Not synced yet
                    'receipt_no' => 0, // <--- ADD THIS LINE

            ]);

            DB::connection($connection)->commit();

            // Show different message based on connection status
            $isOnline = $this->syncService->isOnline();
            $message = $isOnline
                ? 'Document request saved locally. Click the SYNC button to upload to server and send email.'
                : 'Document request saved locally (OFFLINE). Click SYNC when internet is available.';

            return redirect()->route('walkin.form')->with('Success', $message);

        } catch (\Exception $e) {
            DB::connection($connection)->rollBack();

            Log::error('=== WALKIN FORM ERROR ===');
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            Log::error('Queries Executed: ', DB::connection('mysql_local')->getQueryLog());

            return redirect()->route('walkin.form')
                ->with('Error', 'Failed: ' . $e->getMessage());
        }
    }

    /**
     * Send temporary password email
     */
    protected function sendTempPasswordEmail($email, $name, $tempPassword)
    {
        $subject = 'Your Temporary Password';

        try {
            Mail::send('emails.tempPassword', compact('subject', 'name', 'tempPassword', 'email'), function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error("Failed to send temp password email to {$email}: " . $e->getMessage());
        }
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
