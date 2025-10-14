<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTable;


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
        $query = AuditTable::query(); // Replace AuditTable with your actual model name

        // Get filter parameters from request
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');
        $actionType = $request->input('action_type');

        // Apply action type filter (independent filter)
        if ($actionType) {
            // Handle different variations of action types
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
                    // Search in action type field only (case-insensitive, space-insensitive)
                    $query->where(function($q) use ($search) {
                        $q->whereRaw('UPPER(REPLACE(type, " ", "")) LIKE ?', ['%' . strtoupper(str_replace(' ', '', $search)) . '%']);
                    });
                    break;

                case 'user':
                    // Search in changed by field only
                    $query->where('changedBy', 'LIKE', '%' . $search . '%');
                    break;

                case 'table':
                    // Search in table name field only
                    $query->where('fromTableName', 'LIKE', '%' . $search . '%');
                    break;

                case 'date':
                    // Search by date - supports multiple formats
                    $query->where(function($q) use ($search) {
                        $q->whereDate('time', $search)
                        ->orWhere('time', 'LIKE', '%' . $search . '%');
                    });
                    break;

                case 'all':
                default:
                    // Search across all fields
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

        // Order by latest first and paginate
        $auditTrail = $query->orderBy('time', 'desc')->paginate(15);

        // Append query parameters to pagination links to maintain filters
        $auditTrail->appends($request->only(['search', 'filter', 'action_type']));

        return view('common.auditTrail', compact('auditTrail'));
    }

    public function activityLog()
    {
        return view('common.activityLog');
    }
}
