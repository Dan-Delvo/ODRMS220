<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequestModel;
use App\Models\StudentInformationModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PermissionRoleModel;
use App\Models\DocumentsModel;

class OngoingController extends Controller
{
    private function setCurrentUserVariable()
    {
        $username = Auth::check() ? Auth::user()->username : 'guest';
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote($username));
    }

    public function index(Request $request)
    {
        $PermissionPending = PermissionRoleModel::getPermission('ongoing', Auth::user()->role_id);
        if (empty($PermissionPending)) {
            abort(404);
        }

        $data = PermissionRoleModel::getPermission('editOngoing', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('approveOngoing', Auth::user()->role_id);

        // Prepare search options
        $searchOptions = [
            'search' => $request->get('search'),
            'filter' => $request->get('filter', 'all'),
            'sort' => $request->get('sort', 'default'),
            'per_page' => 10
        ];

                // Get document requests with search/filter/sort
        $DocRequests = DocumentRequestModel::getDocumentRequests('processing', $searchOptions);

        // Get total count (unfiltered)
        $totalCount = DocumentRequestModel::getStatusCount('processing');
        
        // Get filtered count (with search/filter applied)
        $filteredCount = $DocRequests->total();
        
        // Get search counts across all statuses
        $searchCounts = DocumentRequestModel::getSearchCountsAcrossAllStatuses($request->get('search') ?? '');

        return view('requestTables.ongoing.ongoing', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
            'searchCounts' => $searchCounts,
            'PermissionEdit' => $data,
            'approveOngoing' => $data1
        ]);
    }

    public function create()
    {
        return view('requestTables.ongoing.createTable');
    }

    public function store(Request $request)
    {
        $this->setCurrentUserVariable();

        $request = $this->validateDocumentRequest($request);
        DocumentRequestModel::createDocumentRequest($request);

        return redirect('/ongoing')->with('Status', 'Created Successfully');
    }

    public function show($id)
    {
        Log::info('Requested ID: ' . $id);

        $table = DocumentRequestModel::with(['claimer', 'studentInformation'])->find($id);

        if (!$table) {
            Log::error('No record found for ID: ' . $id);
            return response()->json(['error' => 'Record not found'], 404);
        }

        return view('requestTables.ongoing.showTable', compact('table'));
    }

    public function edit(DocumentRequestModel $ongoing)
    {
        if (!$ongoing) {
            abort(404, 'Document Request not found.');
        }

        $DocType = DocumentsModel::all();

        return view('requestTables.ongoing.editTable', compact('ongoing', 'DocType'));
    }

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

    public function update(Request $request, DocumentRequestModel $documentRequestModel)
    {
        $this->setCurrentUserVariable();

        $validated = $request->validate([
            'app_date' => 'required|date|before_or_equal:today'
        ]);
        DocumentRequestModel::where('id', $request->id)
            ->update([
                'approve_date' => $validated['app_date']
            ]);

        // $studentId = $documentRequestModel->student_information_id;
        // $student = StudentInformationModel::find($studentId);

        return redirect('/ongoing')->with('status', 'Updated Successfully');
    }

    public function destroy($id)
    {
        $this->setCurrentUserVariable();

        $table = DocumentRequestModel::find($id);

        if ($table) {
            $table->delete();
            return redirect('/ongoing')->with('danger', 'Deleted Successfully');
        }

        return redirect('/ongoing')->with('error', 'Record not found');
    }

    public function trylang(Request $request)
    {
        dd("sadasda");
    }

    public function completeRequest(Request $request, $id)
    {
        $this->setCurrentUserVariable();

        $documentRequest = DocumentRequestModel::findOrFail($id);

        $account = $documentRequest->account;
        $stud = $documentRequest->studentInformation;

        $email = $account->email_address;
        $name = $stud->full_name;
        $subject = 'Your Request is Approved and Completed!';

        Log::info("Sending email to: " . $account->email_address);

        Mail::send('emails.toComplete', compact('subject', 'name'), function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });

        $pushId = $account->fcm_token;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf',
                'include_player_ids' => [$pushId],
                'contents' => ['en' => $name . ', Your document request has been approved and now Processing.'],
            ]);

            Log::info('Notification sent: ' . $response->body());
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $documentRequest->update([
            'remarks' => 'For Release',
            'status' => 'For Release',
            'forRelease_date' => Carbon::now(),
        ]);

        return redirect('/ongoing')->with('status', 'Completed Successfully');
    }
}
