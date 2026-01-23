<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/analytics/data', [AnalyticsController::class, 'getAnalyticsData']);
});

Route::post('/generate-token', function (Request $request) {
    try {
        // Use 'user_account_id' instead of 'id'
        $user = \App\Models\Account::where('user_account_id', 17501405911790)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $token = $user->createToken('python-analytics-script')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user_id' => $user->user_account_id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }
});
