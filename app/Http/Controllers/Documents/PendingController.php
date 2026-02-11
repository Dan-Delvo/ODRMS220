<?php

namespace App\Http\Controllers\Documents;

use App\Models\DocumentRequestModel;
use App\Models\StudentInformationModel;
use App\Models\DocumentsModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\RequestApprovedMail;
use App\Service\requests\Pending;
use App\Traits\ControllerHelpers;
use Exception;
use Google\Service\CloudDebugger\Resource\Controller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PendingController extends Controller
{

    use ControllerHelpers;

    public function __construct()
    {
        $this->setCurrentUserVariable();
    }

    private function setCurrentUserVariable()
    {
        $username = Auth::check() ? Auth::user()->username : 'guest';
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote($username));
    }

    public function index(Request $request)
    {
        $PermissionPending = PermissionRoleModel::getPermission('pending', Auth::user()->role_id);
        if (empty($PermissionPending)) {
            abort(404);
        }

        $PermissionEdit = PermissionRoleModel::getPermission('editPending', Auth::user()->role_id);
        $approvePending = PermissionRoleModel::getPermission('approvePending', Auth::user()->role_id);

        $searchOptions = [
            'search' => $request->get('search'),
            'filter' => $request->get('filter', 'all'),
            'sort' => $request->get('sort', 'default'),
            'per_page' => 10
        ];

        $DocRequests = DocumentRequestModel::getDocumentRequests('pending', $searchOptions);
        $totalCount = DocumentRequestModel::getStatusCount('pending');
        $filteredCount = $DocRequests->total();

        $searchCounts = DocumentRequestModel::getSearchCountsAcrossAllStatuses($request->get('search') ?? '');

        return view('requestTables.pending.pending', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
            'searchCounts' => $searchCounts,
            'PermissionEdit' => $PermissionEdit,
            'approvePending' => $approvePending
        ]);
    }

    public function create()
    {
        return view('requestTables.pending.createTable');
    }

    public function store(Request $request, Pending $pending)
    {

        $request = $pending->validate($request);
        DocumentRequestModel::createDocumentRequest($request);

        return redirect('/pending')->with('Status', 'Created Successfully');
    }

    public function show($id)
    {
        $table = DocumentRequestModel::with(['claimer', 'studentInformation'])->find($id);

        if (!$table) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return view('requestTables.pending.showTable', compact('table'));
    }

    public function edit(DocumentRequestModel $pending)
    {
        if (!$pending) {
            abort(404, 'Document Request not found.');
        }
        $DocType = DocumentsModel::all();

        return view('requestTables.pending.editTable', compact('pending', 'DocType'));
    }


    public function update(Request $request, Pending $pending)
    {

        $validated = $pending->validate($request);
        DocumentRequestModel::updateOrCreateRequest($validated);

        return redirect('/pending')->with('Status', 'Updated Successfully');
    }

    public function destroy($id)
    {

        $table = DocumentRequestModel::find($id);
        if ($table) {
            $table->delete();
            return redirect('/pending')->with('Danger', 'Deleted Successfully');
        }

        return redirect('/pending')->with('error', 'Record not found');
    }

    public function decline(Request $request, $id, Pending $pending)
    {

        try {
            $pending->decline($request, $id);
        } catch (Exception $e) {
            Log::error('Decline failed: ' . $e->getMessage());
            return redirect('/pending')->with('error', 'Decline failed: ' . $e->getMessage());
        }

        if($request->indicator == 1){
            return redirect('/declined-documents')->with('success', 'Declined Successfully');
        }
        return redirect('/pending')->with('success', 'Declined Successfully');
    }

    public function completeRequest($id, Pending $pending)
    {

        $pending->complete($id);

        return redirect('/pending')->with('status', 'Updated Successfully');
    }


}
