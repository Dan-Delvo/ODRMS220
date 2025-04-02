<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequestModel;
use App\Models\StudentInformationModel;
use App\Notifications\RequestStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentsModel;

class PendingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        //dd("asdasd");

        $PermissionPending = PermissionRoleModel::getPermission('pending', Auth::user()->role_id);
        if(empty($PermissionPending))
        {
            abort(404);
        }

        $data = PermissionRoleModel::getPermission('editPending', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('approvePending', Auth::user()->role_id);

        $totalCount = DocumentRequestModel::where('status', 'pending')->count();
        $DocRequests = DocumentRequestModel::where('status', 'pending')
        ->with('claimer')
        ->with('studentInformation')
        ->paginate(9);


        return view('requestTables.pending.pending', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'PermissionEdit' => $data,
            'approvePending' => $data1
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('requestTables.pending.createTable');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request = $this->validateDocumentRequest($request);

        DocumentRequestModel::createDocumentRequest($request);

        return redirect('/pending')->with('Status', 'Created Succesfully');

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Log the incoming ID
        Log::info('Requested ID: ' . $id);

        // Try to fetch the record with relationships
        $table = DocumentRequestModel::with(['claimer', 'studentInformation'])->find($id);

        if (!$table) {
            Log::error('No record found for ID: ' . $id);
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Return the table data
        return view('requestTables.pending.showTable', compact('table'));
    }






    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentRequestModel $pending)
    {
        //
        if (!$pending) {
            abort(404, 'Document Request not found.');
        }

        $DocType = DocumentsModel::all();

        return view('requestTables.pending.editTable', compact('pending', 'DocType'));
    }






    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentRequestModel $documentRequestModel)
    {
        $validated = $this->validateDocumentRequest($request);
        DocumentRequestModel::updateOrCreateRequest($validated);

        $studentId = $documentRequestModel->student_information_id;
        $student = StudentInformationModel::find($studentId);

        return redirect('/pending' )->with('Status', 'Updated Succesfully');


    }

    public function validateDocumentRequest(Request $request)
    {
        return $request->validate([
            'id' => 'required',
            'claimer_id' => 'required',
            'document_id' => 'required',
            'request_schl_entity' => 'required|string|max:255',
            'requested_sf10' => 'required|string|max:255',
            'release_mode' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'status' => 'required|string',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the record by ID
        $table = DocumentRequestModel::find($id);

        if ($table) {
            // Delete the record
            $table->delete();

            // Redirect with a success message
            return redirect('/pending')->with('Danger', 'Deleted Successfully');
        }

        // Redirect with an error message if the record was not found
        return redirect('/pending')->with('error', 'Record not found');
    }

    public function trylang(Request $request){
        dd("sadasda");
    }


    public function completeRequest(Request $request, $id)
    {
        // Find the document request by ID
        $documentRequest = DocumentRequestModel::findOrFail($id);

        $account = $documentRequest->account;
        $stud = $documentRequest->studentInformation;


        $email = $account->email_address;
        $name = $stud->full_name;
        $subject = 'Your Request is Approved!';
        Log::info("Sending email to: " . $account->email_address);

        // Send email notification
        Mail::send('emails.toOngoing', compact('subject', 'name'), function ($message) use ($email, $subject) {
            $message->to($email)
                    ->subject($subject);
        });

        // Retrieve the push ID (FCM token) for the user
        $pushId = $account->fcm_token;

        // Send push notification via OneSignal
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf',
                'include_player_ids' => [$pushId], // Send notification to the user based on their push subscription ID
                'contents' => ['en' => $name . ', Your document request has been approved and is now ongoing.'], // Message content
            ]);

            Log::info('Notification sent: ' . $response->body());

        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // Update the document request status to 'Ongoing'
        $documentRequest->update([
            'status' => 'Ongoing',
        ]);

        return redirect('/pending')->with('Status', 'Updated Successfully');
    }





        // if (!$inserted) {
        //     Log::error('Update failed', ['data' => $request->all()]);
        //     dd('Validation asdsc');
        // }
}


