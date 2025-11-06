<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBulkRequestFormRequest;
use App\Mail\RequestApprovedMail;
use App\Models\BulkRequest as ModelsBulkRequest;
use App\Models\BulkStudent;
use App\Models\ClaimerModel;
use App\Models\PermissionRoleModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BulkRequest extends Controller
{
    public function index()
    {
        $PermissionBulkRequest = PermissionRoleModel::getPermission('bulkRequest', Auth::user()->role_id);
        if (empty($PermissionBulkRequest)) {
            abort(404);
        }

        // ✅ Fixed: Load claimer relationship properly
        $bulk = ModelsBulkRequest::with('claimer')->withCount('students')->get();
        $requests = $bulk; // Same data, no need to query twice
        $student = BulkStudent::getStudent();

        return view('bulk_request.bulk_request', [
            'requests' => $bulk,
            'students' => $student,
        ]);
    }

    public function show()
    {
        $PermissionAddBulkRequest = PermissionRoleModel::getPermission('addBulkRequest', Auth::user()->role_id);
        if (empty($PermissionAddBulkRequest)) {
            abort(404);
        }
        return view('bulk_request.bulk_request_add');
    }

    public function store(StoreBulkRequestFormRequest $request)
    {
        try {
            $validated = $request->validated();
            ModelsBulkRequest::createWithStudents($validated, $validated['students']);
            Mail::to($validated['email'])->queue(new RequestApprovedMail($validated['school_name'], 'Form 137 Request to UBNHS', 'emails.toRequest'));

            return redirect()->route('bulk_request.index')->with('success', 'Bulk Request saved successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to save bulk request. Please try again.');
        }
    }

    public function moveToProcessing($Request_ID) 
    {
        ModelsBulkRequest::moveRequest('Processing', $Request_ID);
        $this->emailNotif($Request_ID, 'emails.bulk_requests.to_approved');
        return redirect('/bulk-request')->with('success', 'Moved to Processing successfully!');
    }

    public function moveToForRelease($Request_ID) 
    {
        ModelsBulkRequest::moveRequest('For Release', $Request_ID);
        $this->emailNotif($Request_ID, 'emails.bulk_requests.to_for_release');
        return redirect('/bulk-request')->with('success', 'Moved to For Release successfully!');
    }

    // ✅ Updated moveToClaimed method with claimer validation
    public function moveToClaimed(Request $request, $Request_ID) 
    {
        // Validate claimer input
        $validated = $request->validate([
            'claimer_fname' => 'required|string|max:255',
            'claimer_lname' => 'required|string|max:255',
            'contact_no' => 'required|digits:11',
            'claimed_date' => 'required|date|before_or_equal:today',
        ]);

        try {
            // Create claimer record
            $claimer = ClaimerModel::create([
                'Fname' => $validated['claimer_fname'],
                'Lname' => $validated['claimer_lname'],
                'contact_no' => $validated['contact_no'],
                'claimed_date' => $validated['claimed_date'],
            ]);

            // Update bulk request with claimer ID
            ModelsBulkRequest::moveRequestWithClaimer('Claimed', $Request_ID, $claimer->id, $validated['claimed_date']);

            // Send email notification
            $this->emailNotif($Request_ID, 'emails.bulk_requests.to_claimed');

            return redirect('/bulk-request')->with('success', 'Request marked as Claimed successfully!');
        } catch (\Exception $e) {
            Log::error('Error moving to claimed: ' . $e->getMessage());
            return redirect('/bulk-request')->with('error', 'Failed to process claim. Please try again.');
        }
    }

    public function emailNotif($Request_ID, string $view) 
    {
        try {
            $bulkRequest = ModelsBulkRequest::findOrFail($Request_ID);
            $name = $bulkRequest->School_Name;
            $email = $bulkRequest->School_Email;
            $subject = "Request Status Update";

            Mail::to($email)->queue(new RequestApprovedMail($name, $subject, $view));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
