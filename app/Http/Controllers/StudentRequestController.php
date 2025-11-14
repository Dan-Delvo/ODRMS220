<?php

namespace App\Http\Controllers;

use App\Models\ClaimerModel;
use App\Models\DocumentRequestModel;
use App\Models\DocuPaymentFee;
use App\Models\DocumentsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentRequestController extends Controller
{
    public function viewRequest()
    {
        $totalCount = DocumentRequestModel::where('std_students_id', Auth::user()->std_students_id)->count();
        $DocRequests = DocumentRequestModel::where('std_students_id', Auth::user()->std_students_id)
            ->with('claimer')
            ->with('studentInformation')
            ->paginate(9);

        return view('common.viewRequest', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount
        ]);
    }

    public function create()
    {
        // Set current user in SQL session
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $DocRequests = DocumentRequestModel::where('std_students_id', Auth::user()->std_students_id)
            ->with('documents')
            ->get()
            ->pluck('documents')
            ->flatten();

        $DocType = DocumentsModel::all();
        $ReleaseMode = ['Pickup', 'Online'];
        return view('common.studentrequest', compact('DocType', 'ReleaseMode', 'DocRequests'));
    }

    public function store(Request $request)
    {
        // Set current user in SQL session
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        // Step 1: Validate the request data
        $validatedData = $request->validate([
            'document_id' => 'required|integer',
            'request_schl_entity' => 'required|string|max:255',
            'release_mode' => 'required|max:255',
            'reason' => 'required|string|max:1000', // ✅ CHANGED from remarks to reason
            'supporting_document' => 'required|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240', // 10MB max
        ]);

        // Step 2: Initialize file path variable
        $supportingDocumentPath = null;

        // Step 3: Handle file upload if present
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            // Generate a unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Define upload path
            $uploadPath = public_path('uploads/supporting_documents');

            // Create directory if it doesn't exist
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Move file to the upload directory
            $file->move($uploadPath, $filename);

            // Store relative path for database
            $supportingDocumentPath = 'uploads/supporting_documents/' . $filename;
        }

        // Step 4: Insert a new Claimer
        $claimer = ClaimerModel::create([
            'Fname' => 'Blank',
            'Lname' => 'Blank',
            'contact_no' => '000000',
        ]);

        // Step 5: Get document details
        $document = DocumentsModel::find($validatedData['document_id']);

        // Step 6: Create payment receipt
        // $receipt = DocuPaymentFee::create([
        //     "receipt_no" => random_int(10000, 99999),
        //     'docu_categories_id' => $validatedData['document_id'],
        //     'doc_amount' => $document->DocPrice,
        //     'name_request' => Auth::user()->std_students_id,
        //     'time_request' => Carbon::now()
        // ]);

        // Step 7: Create document request
        DocumentRequestModel::create([
            'id' => random_int(10000, 99999),
            'clm_claimers_id' => $claimer->id,
            'std_students_id' => Auth::user()->std_students_id,
            'doc_categories_id' => $validatedData['document_id'],
            'request_time' => Carbon::now()->format('H:i:s'),
            'request_date' => Carbon::now()->toDateString(),
            'request_schl_entity' => $validatedData['request_schl_entity'],
            'request_mode' => "Online",
            'release_mode' => $validatedData['release_mode'],
            'supporting_document' => $supportingDocumentPath,
            'reason' => $validatedData['reason'], // ✅ CHANGED from remarks to reason
            'remarks' => "Pending",
            'status' => "Pending",
            // 'receipt_no' => $receipt->receipt_no
        ]);

        // Step 8: Redirect with success message
        return redirect()->route('st.page')->with('Success', 'Document request submitted successfully!');
    }

    public function replaceFile(Request $request, $id)
    {
        // Set current user in SQL session
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        // Validate the incoming request
        $request->validate([
            'supporting_document' => 'required|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240', // 10MB max
            'reason' => 'required|string|max:1000', // ✅ ADDED reason validation
        ]);

        // Find the document request by ID
        $docRequest = DocumentRequestModel::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            // Generate a unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Define upload path
            $uploadPath = public_path('uploads/supporting_documents');

            // Create directory if it doesn't exist
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Move file to the upload directory
            $file->move($uploadPath, $filename);

            // Store relative path for database
            $supportingDocumentPath = 'uploads/supporting_documents/' . $filename;

            // ✅ Before replacing, move old supporting_document into "image"
            if ($docRequest->supporting_document) {
                $docRequest->image = $docRequest->supporting_document; // store old value in `image` column
            }

            // Optionally delete old file from disk (up to you)
            // if ($docRequest->supporting_document && file_exists(public_path($docRequest->supporting_document))) {
            //     unlink(public_path($docRequest->supporting_document));
            // }

            // Update the document request with the new file path and reason
            $docRequest->supporting_document = $supportingDocumentPath;
            $docRequest->reason = $request->input('reason'); // ✅ ADDED reason update
            $docRequest->save();
        }

        // Redirect back with success message
        return redirect()->back()->with('Success', 'Document re-requested successfully! Your request is now pending review.');
    }

}
