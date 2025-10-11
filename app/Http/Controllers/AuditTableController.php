<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTable;


class AuditTableController extends Controller
{
    //
// In your AuditTrailController or wherever you handle the audit trail route

    public function index(Request $request)
    {
        $query = AuditTable::query(); // Replace with your actual model name

        // Get filter parameters
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');
        $tableFilter = $request->input('table_filter', 'all');

        // Apply table filter first
        if ($tableFilter !== 'all') {
            $query->where('fromTableName', 'LIKE', '%' . $tableFilter . '%');
        }

        // Apply search based on filter type
        if ($search) {
            switch ($filter) {
                case 'type':
                    $query->where('type', 'LIKE', '%' . $search . '%');
                    break;
                case 'user':
                    $query->where('changedBy', 'LIKE', '%' . $search . '%');
                    break;
                case 'table':
                    $query->where('fromTableName', 'LIKE', '%' . $search . '%');
                    break;
                case 'date':
                    $query->whereDate('time', 'LIKE', '%' . $search . '%');
                    break;
                case 'all':
                default:
                    $query->where(function($q) use ($search) {
                        $q->where('type', 'LIKE', '%' . $search . '%')
                        ->orWhere('description', 'LIKE', '%' . $search . '%')
                        ->orWhere('changedBy', 'LIKE', '%' . $search . '%')
                        ->orWhere('fromTableName', 'LIKE', '%' . $search . '%')
                        ->orWhere('old_data', 'LIKE', '%' . $search . '%')
                        ->orWhere('new_data', 'LIKE', '%' . $search . '%');
                    });
                    break;
            }
        }

        // Order by latest first and paginate
        $auditTrail = $query->orderBy('time', 'desc')->paginate(15);

        // Append query parameters to pagination links
        $auditTrail->appends([
            'search' => $search,
            'filter' => $filter,
            'table_filter' => $tableFilter
        ]);

        return view('common.auditTrail', compact('auditTrail'));
    }

    public function activityLog()
    {
        return view('common.activityLog');
    }
}
