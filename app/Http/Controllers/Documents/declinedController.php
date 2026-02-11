<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
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
        public function index(Request $request)
    {
        $PermissionDeclined = PermissionRoleModel::getPermission('pending', Auth::user()->role_id);
        if (empty($PermissionDeclined)) {
            abort(404);
        }

        // Prepare search options
        $searchOptions = [
            'search' => $request->get('search'),
            'filter' => $request->get('filter', 'all'),
            'sort' => $request->get('sort', 'default'),
            'per_page' => 10
        ];

        // Get document requests with search/filter/sort
        $DocRequests = DocumentRequestModel::getDocumentRequests('Declined', $searchOptions);

        // Get total count (unfiltered)
        $totalCount = DocumentRequestModel::getStatusCount('Declined');

        // Get filtered count (with search/filter applied)
        $filteredCount = $DocRequests->total();

        // Get search counts across all statuses
        $searchCounts = DocumentRequestModel::getSearchCountsAcrossAllStatuses($request->get('search') ?? '');

        return view('requestTables.declined.declined', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
            'searchCounts' => $searchCounts,
        ]);
    }

}
