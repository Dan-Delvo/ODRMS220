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
        $totalOngoing = DocumentRequestModel::where('status', 'ongoing')->count();
        $totalCompleted = DocumentRequestModel::where('status', 'completed')->count();
        $username = Auth::user()->username;
        return view('common.admin', [
            'totalPending' => $totalPending,
            'totalOngoing' => $totalOngoing,
            'totalCompleted' => $totalCompleted,
            'username' => $username
        ]);
    }
}
