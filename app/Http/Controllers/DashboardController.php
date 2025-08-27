<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequestModel;
use Illuminate\Support\Facades\Auth;
use App\Models\PermissionRoleModel;

class DashboardController extends Controller
{
    //

    public function dashboard(){
        $PermissionDashboard = PermissionRoleModel::getPermission('dashboard', Auth::user()->role_id);
        if(empty($PermissionDashboard))
        {
            abort(404);
        }
        $totalPending = DocumentRequestModel::where('status', 'pending')->count();
        $totalOngoing = DocumentRequestModel::where('status', 'Processing')->count();
        $totalRelease = DocumentRequestModel::where('status', 'For Release')->count();
        $totalClaimed = DocumentRequestModel::where('status', 'claimed')->count();
        $totalDeclined = DocumentRequestModel::where('status', 'Declined')->count();
        $username = Auth::user()->username;
        return view('common.admin', [
            'totalPending' => $totalPending,
            'totalOngoing' => $totalOngoing,
            'totalRelease' => $totalRelease,
            'totalClaimed' => $totalClaimed,
            'totalDeclined' => $totalDeclined,
            'username' => $username
        ]);
    }
}
