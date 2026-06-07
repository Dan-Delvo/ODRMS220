<?php

namespace App\Service\requests;

use App\Models\DocumentRequestModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RevertEmailNotification
{
    public function send(DocumentRequestModel $documentRequest, string $targetStatus, string $reason): string
    {
        Log::info('Preparing revert email notification.', [
            'request_id' => $documentRequest->id,
            'request_number' => $documentRequest->req_no,
            'target_status' => $targetStatus,
        ]);

        $documentRequest->loadMissing(['account', 'studentInformation', 'guest']);

        $recipientEmail = $documentRequest->guest?->email_address
            ?? $documentRequest->account?->email_address;
        $email = $recipientEmail ?? 'nubzman123@gmail.com';

        if (empty($recipientEmail)) {
            Log::warning('Revert email is using the fallback recipient.', [
                'request_id' => $documentRequest->id,
                'request_number' => $documentRequest->req_no,
                'fallback_email' => $email,
                'target_status' => $targetStatus,
            ]);
        }

        $name = $documentRequest->studentInformation?->full_name
            ?? $documentRequest->guest?->name
            ?? 'Requestor';

        $subject = "Document Request Reverted to {$targetStatus}";
        $requestNumber = $documentRequest->req_no;

        try {
            Mail::send(
                'emails.reverted',
                compact('name', 'targetStatus', 'reason', 'requestNumber'),
                function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                }
            );

            Log::info('Revert email sent successfully.', [
                'request_id' => $documentRequest->id,
                'email' => $email,
                'target_status' => $targetStatus,
            ]);

            return 'sent';
        } catch (\Throwable $exception) {
            Log::error('Failed to send revert email.', [
                'request_id' => $documentRequest->id,
                'email' => $email,
                'target_status' => $targetStatus,
                'error' => $exception->getMessage(),
            ]);

            return 'failed';
        }
    }
}
