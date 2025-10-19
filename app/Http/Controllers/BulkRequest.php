<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBulkRequestFormRequest;
use App\Mail\RequestApprovedMail;
use App\Models\BulkRequest as ModelsBulkRequest;
use App\Models\BulkStudent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BulkRequest extends Controller
{

    //
    public function index()
    {
        $bulk = ModelsBulkRequest::getBulkRequest();

        $requests = ModelsBulkRequest::getBulkRequest();
        $student = BulkStudent::getStudent();

        return view('bulk_request.bulk_request', ['requests' => $bulk], [
            'requests' => $requests,
            'students' => $student,
        ]);
    }

    public function show()
    {
        return view('bulk_request.bulk_request_add');
    }

    public function store(StoreBulkRequestFormRequest $request)
    {
        try {
            // Validate the fields
            $validated = $request->validated();

            // Insert to BulkRequest and BulkStudent
            ModelsBulkRequest::createWithStudents($validated, $validated['students']);

            Mail::to($validated['email'])->queue(new RequestApprovedMail($validated['school_name'], 'Form 137 Request to UBNHS', 'emails.toRequest'));

            return redirect()->route('bulk_request.index')->with('success', 'Bulk Request saved successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to save bulk request. Please try again.');
        }
    }

    public function moveToProcessing($Request_ID) {

        ModelsBulkRequest::moveRequest('Processing', $Request_ID);

        $this->emailNotif($Request_ID, 'emails.bulk_requests.to_approved');

        return redirect('/bulk-request')->with('Success', 'Moved Successfully');
    }

    public function moveToForRelease($Request_ID) {

        ModelsBulkRequest::moveRequest('For Release', $Request_ID);

        $this->emailNotif($Request_ID, 'emails.bulk_requests.to_for_release');


        return redirect('/bulk-request')->with('Success', 'Moved Successfully');
    }

    public function moveToClaimed($Request_ID) {

        ModelsBulkRequest::moveRequest('Claimed', $Request_ID);

        $this->emailNotif($Request_ID, 'emails.bulk_requests.to_claimed');

        return redirect('/bulk-request')->with('Success', 'Moved Successfully');
    }

    public function emailNotif($Request_ID, string $view) {
        try{
            $bulkRequest = ModelsBulkRequest::findOrFail($Request_ID);

            $name = $bulkRequest->School_Name;
            $email = $bulkRequest->School_Email;
            $subject = "Request Status Update";

            Mail::to($email)->queue(new RequestApprovedMail($name, $subject, $view));
        } catch (Exception $e){
            Log::error($e->getMessage());
        }
    }

}
