<?php

namespace App\Http\Controllers;

use App\Models\ClaimerModel;
use App\Models\DocumentRequestModel;
use App\Models\DocuPaymentFee;
use App\Models\DocumentsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentRequestController extends Controller
{
    public function viewRequest(){

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

    public function create(){
        $DocType = DocumentsModel::all();
        $ReleaseMode = ['Pickup', 'Online'];
        return view('common.studentrequest', compact('DocType', 'ReleaseMode'));
    }

    public function store(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'document_id' => 'required|integer',
        'request_schl_entity' => 'required|string|max:255',
        'release_mode' => 'required|max:255',
        'supporting_document' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240', // 10MB max
    ]);

    $supportingDocumentPath = null;

    // Handle file upload if present
    if ($request->hasFile('supporting_document')) {
        $file = $request->file('supporting_document');

        // Generate a unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store the file in storage/app/public/supporting_documents
        $supportingDocumentPath = $file->storeAs('supporting_documents', $filename, 'public');

        // Alternative: Store in public/uploads/supporting_documents (accessible via web)
        // $supportingDocumentPath = $file->move(public_path('uploads/supporting_documents'), $filename);
        // $supportingDocumentPath = 'uploads/supporting_documents/' . $filename;
    }

    // Insert a new Claimer
    $claimer = ClaimerModel::create([
        'Fname' => 'Blank',
        'Lname' => 'Blank',
        'contact_no' => '000000',
    ]);

    $document = DocumentsModel::find($validatedData['document_id']);

    $receipt = DocuPaymentFee::create([
        "receipt_no" => random_int(10000, 99999),
        'docu_categories_id' => $validatedData['document_id'],
        'doc_amount' => $document->DocPrice,
        'name_request' => Auth::user()->std_students_id,
        'time_request' => Carbon::now()
    ]);

    // Insert a new Document Request with the claimer_id from the inserted claimer
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
        'image' => $supportingDocumentPath, // Add this field to store the file path
        'remarks' => "N/A",
        'status' => "Pending",
        'receipt_no' => $receipt->receipt_no
    ]);

    // Redirect or respond with success
    return redirect()->route('st.page')->with('success', 'Document request submitted successfully!');
}




}
