<?php

namespace App\Http\Controllers;

use App\Services\SyncService;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Get sync status
     */
    public function status()
    {
        $status = $this->syncService->getSyncStatus();
        return response()->json($status);
    }

    /**
     * Trigger sync
     */
    public function sync()
    {
        $results = $this->syncService->syncToOnline();

        if ($results['success']) {
            return response()->json([
                'message' => 'Sync completed successfully',
                'data' => $results
            ], 200);
        }

        return response()->json([
            'message' => 'Sync completed with errors',
            'data' => $results
        ], 207); // 207 Multi-Status
    }

    /**
     * Check if online
     */
    public function checkConnection()
    {
        $isOnline = $this->syncService->isOnline();

        return response()->json([
            'online' => $isOnline,
            'message' => $isOnline ? 'Connected' : 'Offline'
        ]);
    }
}
