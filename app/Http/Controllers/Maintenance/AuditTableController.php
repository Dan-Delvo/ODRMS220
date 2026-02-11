<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditTable;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;

class AuditTableController extends Controller
{
    //
// In your AuditTrailController or wherever you handle the audit trail route

/**
 * Display audit trail with search and filter functionality
 *
 * @param Request $request
 * @return \Illuminate\View\View
 */
public function index(Request $request)
{
    $PermissionAudit = PermissionRoleModel::getPermission('auditTrail', Auth::user()->role_id);
    if (empty($PermissionAudit)) {
        abort(404);
    }

    $query = AuditTable::query();

    // Get filter parameters
    $search = $request->input('search');
    $filter = $request->input('filter', 'all');
    $actionType = $request->input('action_type');

    // Apply action type filter
    if ($actionType) {
        $typeMapping = [
            'BACKUP' => 'Back Up',
            'RESTORE' => 'Restore',
            'CREATE' => 'Create',
            'UPDATE' => 'Update',
            'DELETE' => 'Delete',
            'LOGIN' => 'Login'
        ];

        $searchType = $typeMapping[$actionType] ?? $actionType;
        $query->where('type', $searchType);
    }

    // Apply search based on selected filter type
    if ($search) {
        switch ($filter) {
            case 'type':
                $query->where(function($q) use ($search) {
                    $q->whereRaw('UPPER(REPLACE(type, " ", "")) LIKE ?', ['%' . strtoupper(str_replace(' ', '', $search)) . '%']);
                });
                break;

            case 'user':
                $query->where('changedBy', 'LIKE', '%' . $search . '%');
                break;

            case 'table':
                $query->where('fromTableName', 'LIKE', '%' . $search . '%');
                break;

            case 'date':
                $query->where(function($q) use ($search) {
                    $q->whereDate('time', $search)
                    ->orWhere('time', 'LIKE', '%' . $search . '%');
                });
                break;

            case 'all':
            default:
                $query->where(function($q) use ($search) {
                    $searchUpper = strtoupper(str_replace(' ', '', $search));
                    $q->whereRaw('UPPER(REPLACE(type, " ", "")) LIKE ?', ['%' . $searchUpper . '%'])
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('changedBy', 'LIKE', '%' . $search . '%')
                    ->orWhere('fromTableName', 'LIKE', '%' . $search . '%')
                    ->orWhere('old_data', 'LIKE', '%' . $search . '%')
                    ->orWhere('new_data', 'LIKE', '%' . $search . '%')
                    ->orWhereDate('time', $search)
                    ->orWhere('time', 'LIKE', '%' . $search . '%');
                });
                break;
        }
    }

    $auditTrail = $query->orderBy('time', 'desc')->paginate(15);
    $auditTrail->appends($request->only(['search', 'filter', 'action_type']));

    // Check if AJAX request
    if ($request->ajax() || $request->input('ajax')) {
        return response()->json([
            'html' => view('common.audit_table', compact('auditTrail'))->render(),
            'total' => $auditTrail->total(),
            'from' => $auditTrail->firstItem(),
            'to' => $auditTrail->lastItem(),
            'count' => $auditTrail->count()
        ]);
    }

    return view('common.auditTrail', compact('auditTrail'));
}

    public function activityLog()
    {
        return view('common.activityLog');
    }
}
