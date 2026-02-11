<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentRequestModel;
use Illuminate\Support\Facades\Auth;
use App\Models\PermissionRoleModel;

class DashboardController extends Controller
{
    //

    public function dashboard(Request $request){
        $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', Auth::user()->role_id);
        if(empty($PermissionDashboard))
        {
            abort(404);
        }

        // Get date range from request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build base query
        $query = DocumentRequestModel::query();

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween('request_date', [$startDate, $endDate]);
        }

        $totalPending = (clone $query)->where('status', 'pending')->count();
        $totalOngoing = (clone $query)->where('status', 'Processing')->count();
        $totalRelease = (clone $query)->where('status', 'For Release')->count();
        $totalClaimed = (clone $query)->where('status', 'claimed')->count();
        $totalDeclined = (clone $query)->where('status', 'Declined')->count();
        $username = Auth::user()->username;

        return view('common.admin', [
            'totalPending' => $totalPending,
            'totalOngoing' => $totalOngoing,
            'totalRelease' => $totalRelease,
            'totalClaimed' => $totalClaimed,
            'totalDeclined' => $totalDeclined,
            'username' => $username,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
