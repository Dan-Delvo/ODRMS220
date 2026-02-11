<?php

namespace App\Service\requests;

use App\Mail\RequestApprovedMail;
use App\Models\DocumentRequestModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Pending
{

    public function decline($request, $id) {
        $documentRequest = DocumentRequestModel::findOrFail($id);
        $account = $documentRequest->account;
        $stud = $documentRequest->studentInformation;

        $email = $account->email_address ?? 'nubzman123@gmail.com';
        $name = $stud->full_name;
        $subject = 'Your Request is Declined!';
        $reason = $request->remarks;

        Log::info("Sending email to: " . $email);

        Mail::send('emails.Decline', compact('subject', 'name', 'reason'), function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });

        $documentRequest->update([
            'status' => 'Declined',
            'remarks' => $reason
        ]);
    }

    public function complete($id){
        $documentRequest = DocumentRequestModel::findOrFail($id);
        $account = $documentRequest->account;
        $stud = $documentRequest->studentInformation;

        $email = $account->email_address ?? 'nubzman123@gmail.com';
        $name = $stud->full_name;
        $subject = 'Your Request is Approved!';
        $view = 'emails.toOngoing';

        Log::info("Sending email to: " . $email);

        Mail::to($email)->queue(new RequestApprovedMail($name, $subject, $view));

        $documentRequest->update([
            'remarks' => 'Processing',
            'status' => 'Processing',
            'approve_date' => Carbon::now(),
        ]);
    }

    public static function validate($request) {
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
}
