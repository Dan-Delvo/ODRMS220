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

class PendingController extends Controller
{
    private function setCurrentUserVariable()
    {
        $username = Auth::check() ? Auth::user()->username : 'guest';
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote($username));
    }

    public function index()
    {
        $PermissionPending = PermissionRoleModel::getPermission('pending', Auth::user()->role_id);
        if (empty($PermissionPending)) {
            abort(404);
        }

        $data = PermissionRoleModel::getPermission('editPending', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('approvePending', Auth::user()->role_id);

        $totalCount = DocumentRequestModel::where('status', 'pending')->count();
        $DocRequests = DocumentRequestModel::where('status', 'pending')
            ->with('claimer')
            ->with('studentInformation')
            ->orderBy('req_no', 'asc')
            ->paginate(10);

        return view('requestTables.pending.pending', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'PermissionEdit' => $data,
            'approvePending' => $data1
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

        return redirect('/pending')->with('Danger', 'Declined Successfully');
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

        Log::info("Sending email to: " . $account->email_address);

        Mail::send('emails.toOngoing', compact('subject', 'name'), function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });

        // $pushId = $account->fcm_token;

        // try {
        //     $response = Http::withHeaders([
        //         'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
        //         'accept' => 'application/json',
        //         'content-type' => 'application/json',
        //     ])->post('https://onesignal.com/api/v1/notifications', [
        //         'app_id' => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf',
        //         'include_player_ids' => [$pushId],
        //         'contents' => ['en' => $name . ', Your document request has been approved and is now ongoing.'],
        //     ]);

        //     Log::info('Notification sent: ' . $response->body());

        // } catch (\Exception $e) {
        //     report($e);
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }

        $documentRequest->update([
            'status' => 'Processing',
            'approve_date' => Carbon::now(),
        ]);

        return redirect('/pending')->with('status', 'Updated Successfully');
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

    /**
     * Remove the specified resource from storage.
     */


        // if (!$inserted) {
        //     Log::error('Update failed', ['data' => $request->all()]);
        //     dd('Validation asdsc');
        // }
}
