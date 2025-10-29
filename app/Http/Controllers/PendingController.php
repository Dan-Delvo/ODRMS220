<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequestModel;
use App\Models\StudentInformationModel;
use App\Models\DocumentsModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\RequestApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PendingController extends Controller
{


    private function setCurrentUserVariable()
    {
        $username = Auth::check() ? Auth::user()->username : 'guest';
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote($username));
    }

    public function index(Request $request)
    {
        // Check permission
        $PermissionPending = PermissionRoleModel::getPermission('pending', Auth::user()->role_id);
        if (empty($PermissionPending)) {
            abort(404);
        }

        // Get edit and approve permissions
        $PermissionEdit = PermissionRoleModel::getPermission('editPending', Auth::user()->role_id);
        $approvePending = PermissionRoleModel::getPermission('approvePending', Auth::user()->role_id);

        // Prepare search options
        $searchOptions = [
            'search' => $request->get('search'),
            'filter' => $request->get('filter', 'all'),
            'sort' => $request->get('sort', 'default'),
            'per_page' => 10
        ];

        // Get document requests with search/filter/sort
        $DocRequests = DocumentRequestModel::getDocumentRequests('pending', $searchOptions);

        // Get total count (unfiltered)
        $totalCount = DocumentRequestModel::getStatusCount('pending');

        return view('requestTables.pending.pending', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'PermissionEdit' => $PermissionEdit,
            'approvePending' => $approvePending
        ]);
    }


    public function create()
    {
        return view('requestTables.pending.createTable');
    }

    public function store(Request $request)
    {
        $this->setCurrentUserVariable();

        $request = $this->validateDocumentRequest($request);
        DocumentRequestModel::createDocumentRequest($request);

        return redirect('/pending')->with('Status', 'Created Successfully');
    }

    public function show($id)
    {
        Log::info('Requested ID: ' . $id);
        $table = DocumentRequestModel::with(['claimer', 'studentInformation'])->find($id);

        if (!$table) {
            Log::error('No record found for ID: ' . $id);
            return response()->json(['error' => 'Record not found'], 404);
        }

        return view('requestTables.pending.showTable', compact('table'));
    }

    public function edit(DocumentRequestModel $pending)
    {
        if (!$pending) {
            abort(404, 'Document Request not found.');
        }
        $DocType = DocumentsModel::all();

        // dd($pending->documents());
        return view('requestTables.pending.editTable', compact('pending', 'DocType'));
    }


    public function update(Request $request, DocumentRequestModel $documentRequestModel)
    {
        $this->setCurrentUserVariable();

        $validated = $this->validateDocumentRequest($request);
        DocumentRequestModel::updateOrCreateRequest($validated);

        return redirect('/pending')->with('Status', 'Updated Successfully');
    }

    public function destroy($id)
    {
        $this->setCurrentUserVariable();

        $table = DocumentRequestModel::find($id);

        if ($table) {
            $table->delete();
            return redirect('/pending')->with('Danger', 'Deleted Successfully');
        }

        return redirect('/pending')->with('error', 'Record not found');
    }

    public function decline(Request $request, $id)
    {
        $this->setCurrentUserVariable();

        $documentRequest = DocumentRequestModel::findOrFail($id);
        $account = $documentRequest->account;
        $stud = $documentRequest->studentInformation;

        $email = $account->email_address;
        $name = $stud->full_name;
        $subject = 'Your Request is Declined!';
        $reason = $request->remarks;

        Log::info("Sending email to: " . $account->email_address);

        Mail::send('emails.Decline', compact('subject', 'name', 'reason'), function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });

        $documentRequest->update([
            'status' => 'Declined',
            'remarks' => $reason
        ]);

        return redirect('/pending')->with('success', 'Declined Successfully');
    }

    public function completeRequest(Request $request, $id)
    {
        $this->setCurrentUserVariable();

        $documentRequest = DocumentRequestModel::findOrFail($id);
        $account = $documentRequest->account;
        $stud = $documentRequest->studentInformation;

        $email = $account->email_address;
        $name = $stud->full_name;
        $subject = 'Your Request is Approved!';
        $view = 'emails.toOngoing';

        Log::info("Sending email to: " . $account->email_address);

        // Queue email (non-blocking)
        Mail::to($email)->queue(new RequestApprovedMail($name, $subject, $view));

        $documentRequest->update([
            'remarks' => 'Proessing',
            'status' => 'Processing',
            'approve_date' => Carbon::now(),
        ]);

        return redirect('/pending')->with('status', 'Updated Successfully');
    }

    public function validateDocumentRequest(Request $request)
    {
        return $request->validate([
            'id' => 'required',
            'claimer_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $record = DocumentRequestModel::find($request->id);

                    if (!$record) {
                        $fail('The document request does not exist.');
                        return;
                    }

                    // ✅ Only fail if user *actually changes* the claimer while status = Pending
                    if ($record->status === 'Pending' && (string) $record->claimer->full_name !== (string) $value) {
                        $fail('Cannot change the Claimer while the request is Pending.');
                    }
                },
            ],
            'document_id' => 'required',
            'request_schl_entity' => 'required|string|max:255',
            'request_mode' => 'required|string|max:255',
            'release_mode' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',

            // 🔒 Prevent status tampering
            'status' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $record = DocumentRequestModel::find($request->id);
                    if ($record && $value !== $record->status) {
                        $fail('You cannot manually change the request status.');
                    }
                }
            ],
        ]);
    }




    /**
     * Remove the specified resource from storage.
     */


    // if (!$inserted) {
    //     Log::error('Update failed', ['data' => $request->all()]);
    //     dd('Validation asdsc');
    // }
}
