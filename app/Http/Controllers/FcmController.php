<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

class FcmController extends Controller
{
    // Method to update the device token (remember_token)
    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'user_account_id' => 'required|exists:acc_users,user_account_id',
            'fcm_token' => 'required|string',
        ]);

        $account = Account::find($request->user_account_id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $account->fcm_token = $request->fcm_token;
        $account->save();  // Save the updated token

        return response()->json(['message' => 'Device token (fcm_token) updated successfully']);
    }

    // Method to send FCM notification
    public function sendFcmNotification(Request $request)
    {
        $request->validate([
            'user_account_id' => 'required|exists:acc_users,user_account_id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $account = Account::find($request->user_account_id);
        $fcm = $account->fcm_token;

        if (!$fcm) {
            return response()->json(['message' => 'Account does not have a device token'], 400);
        }

        $title = $request->title;
        $description = $request->body;
        $projectId = config('services.fcm.project_id');

        $credentialsFilePath = Storage::path('app/json/file.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body" => $description,
                ],
            ]
        ];

        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json(['message' => 'Curl Error: ' . $err], 500);
        } else {
            return response()->json([
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        }
    }
}


