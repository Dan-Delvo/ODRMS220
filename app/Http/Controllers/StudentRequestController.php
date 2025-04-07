<?php

namespace App\Http\Controllers;

use App\Models\ClaimerModel;
use App\Models\DocumentRequestModel;
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
            'Fname' => 'required|string|max:255',
            'Lname' => 'required|string|max:255',
            'contact_no' => 'required|string|max:15',
            'document_id' => 'required|integer',
            'request_schl_entity' => 'required|string|max:255',
            'release_mode' => 'required|string|max:255',
        ]);

        // Insert a new Claimer
        $claimer = ClaimerModel::create([
            'Fname' => $validatedData['Fname'],
            'Lname' => $validatedData['Lname'],
            'contact_no' => $validatedData['contact_no'],
        ]);

        // Insert a new Document Request with the claimer_id from the inserted claimer
        DocumentRequestModel::create([
            'id' => random_int(10000, 99999),
            'clm_claimers_id' => $claimer->id, // Use the id from the newly created Claimer
            'std_students_id' => Auth::user()->std_students_id,
            'doc_categories_id' => $validatedData['document_id'],

            'request_time' => Carbon::now()->format('H:i:s'),
            'request_date' => Carbon::now()->toDateString(),

            'request_schl_entity' => $validatedData['request_schl_entity'],
            'request_mode' => "Online",
            'release_mode' => $validatedData['release_mode'],

            'remarks' => "N/A",
            'status' => "Pending",
        ]);

        // Redirect or respond with success
        return redirect()->route('st.page')->with('success', 'Document request submitted successfully!');
    }

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




}
