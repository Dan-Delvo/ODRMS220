<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequestModel;
use App\Models\StudentInformationModel;
use App\Models\DocumentsModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class declinedController extends Controller
{
    //
        public function index()
    {
        $PermissionDeclined = PermissionRoleModel::getPermission('pending', Auth::user()->role_id);
        if (empty($PermissionDeclined)) {
            abort(404);
        }

        $totalCount = DocumentRequestModel::where('status', 'Declined')->count();
        $DocRequests = DocumentRequestModel::where('status', 'Declined')
            ->with('claimer')
            ->with('studentInformation')
            ->orderBy('req_no', 'asc')
            ->paginate(9);

        return view('requestTables.declined.declined', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
        ]);
    }

}
