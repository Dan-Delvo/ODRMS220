<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequestModel;
use App\Exports\ExportRequest;
use App\Models\BulkRequest;
use App\Models\BulkStudent;
use App\Models\PermissionRoleModel;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateRequestController extends Controller
{
    /**
     * Display the generate request page with optional status filtering
     */
    public function display(Request $request)
    {
        // Check permissions
        $PermissionGen = PermissionRoleModel::getPermission('generateReports', Auth::user()->role_id);
        if (empty($PermissionGen)) {
            abort(404);
        }

        // Get total count before filters
        $totalCount = DocumentRequestModel::unionDocumentReqTable()
            ->union(BulkStudent::unionStudentTable())
            ->count();

        // Get status filter
        $statusFilter = $request->input('status', 'all');
        $search = $request->input('search');
        $sortBy = $request->input('sort', 'default');

        // Build individual requests query with filters
        $individualQuery = DocumentRequestModel::query()
            ->join('clm_claimers', 'doc_requests.clm_claimers_id', '=', 'clm_claimers.id')
            ->join('std_students', 'doc_requests.std_students_id', '=', 'std_students.id')
            ->join('doc_categories', 'doc_requests.doc_categories_id', '=', 'doc_categories.id')
            ->select(
                'doc_requests.id as id',
                'doc_requests.req_no as req_no',
                DB::raw("CONCAT(std_students.FirstName, ' ', std_students.LastName) as full_name"),
                'doc_categories.DocType as DocType',
                'doc_requests.request_schl_entity as request_schl_entity',
                'doc_requests.request_mode as request_mode',
                'doc_requests.release_mode as release_mode',
                'doc_requests.remarks as remarks',
                'doc_requests.status',
                'doc_requests.request_date',
                'doc_requests.approve_date',
                'doc_requests.forRelease_date',
                'doc_requests.claimed_date'
            );

        // Apply status filter to individual requests
        if ($statusFilter && $statusFilter !== 'all') {
            $individualQuery->where('doc_requests.status', $statusFilter);
        }

        // Apply search filter to individual requests
        if ($search) {
            $individualQuery->where(function($q) use ($search) {
                $q->where('doc_requests.req_no', 'like', '%' . $search . '%')
                ->orWhere(DB::raw("CONCAT(std_students.FirstName, ' ', std_students.LastName)"), 'like', '%' . $search . '%')
                ->orWhere('doc_categories.DocType', 'like', '%' . $search . '%')
                ->orWhere('doc_requests.request_schl_entity', 'like', '%' . $search . '%')
                ->orWhere('doc_requests.request_mode', 'like', '%' . $search . '%')
                ->orWhere('doc_requests.release_mode', 'like', '%' . $search . '%')
                ->orWhere('doc_requests.remarks', 'like', '%' . $search . '%');
            });
        }

        // Build bulk requests query with filters
        $bulkQuery = BulkStudent::query()
            ->join('bulk_requests', 'bulk_students.Request_ID', '=', 'bulk_requests.Request_ID')
            ->select(
                'bulk_students.Student_ID as id',
                'bulk_students.Request_ID as req_no',
                'bulk_students.Student_Name as full_name',
                'bulk_requests.Doc_Type as DocType',
                'bulk_requests.School_Name as request_schl_entity',
                DB::raw("'Bulk Request' as request_mode"),
                DB::raw("'Walk In' as release_mode"),
                DB::raw("NULL as remarks"),
                'bulk_requests.Status as status',
                'bulk_requests.request_date as request_date',
                'bulk_requests.approve_date as approve_date',
                'bulk_requests.forRelease_date as forRelease_date',
                'bulk_requests.claimed_date as claimed_date'
            );

        // Apply status filter to bulk requests
        if ($statusFilter && $statusFilter !== 'all') {
            $bulkQuery->where('bulk_requests.Status', $statusFilter);
        }

        // Apply search filter to bulk requests
        if ($search) {
            $bulkQuery->where(function($q) use ($search) {
                $q->where('bulk_students.Request_ID', 'like', '%' . $search . '%')
                ->orWhere('bulk_students.Student_Name', 'like', '%' . $search . '%')
                ->orWhere('bulk_requests.Doc_Type', 'like', '%' . $search . '%')
                ->orWhere('bulk_requests.School_Name', 'like', '%' . $search . '%');
            });
        }

        // Union the queries
        $unionQuery = $individualQuery->union($bulkQuery);

        // Get the base query builder to merge bindings properly
        $baseQuery = $unionQuery->getQuery();

        // Wrap in a subquery for sorting
        $query = DB::table(DB::raw("({$baseQuery->toSql()}) as combined_requests"))
            ->mergeBindings($baseQuery);

        // Apply sorting
        switch ($sortBy) {
            case 'asc':
                // Sort by request number ascending (numeric)
                $query->orderBy('req_no', 'asc');
                break;
            case 'desc':
                // Sort by request number descending (numeric)
                $query->orderBy('req_no', 'desc');
                break;
            default:
                // Default order by ID (most recent first)
                $query->orderBy('req_no', 'desc');
                break;
        }

        // Get paginated results
        $DocRequests = $query->paginate(10)->appends($request->except('page'));

        return view('generation.generateRequest', compact('DocRequests', 'totalCount', 'statusFilter'));
    }
    /**
     * Generate PDF report with date range and status filtering
     */
    public function pdfGenerator(Request $request)
    {
        // Validate date inputs
        $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
            'status_filter' => 'nullable|string|in:all,Pending,Processing,For Release,Claimed'
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $statusFilter = $request->input('status_filter', 'all');

        try {

            $individualQuery = DocumentRequestModel::query()
            ->join('clm_claimers', 'doc_requests.clm_claimers_id', '=', 'clm_claimers.id')
            ->join('std_students', 'doc_requests.std_students_id', '=', 'std_students.id')
            ->join('doc_categories', 'doc_requests.doc_categories_id', '=', 'doc_categories.id')
            ->select(
                'doc_requests.id as id',
                'doc_requests.req_no as req_no',
                DB::raw("CONCAT(std_students.FirstName, ' ', std_students.LastName) as full_name"),
                'doc_categories.DocType as DocType',
                'doc_requests.request_schl_entity as request_schl_entity',
                'doc_requests.request_mode as request_mode',
                'doc_requests.release_mode as release_mode',
                'doc_requests.remarks as remarks',
                'doc_requests.status',
                'doc_requests.request_date',
                'doc_requests.approve_date',
                'doc_requests.forRelease_date',
                'doc_requests.claimed_date'
            );

            // Build bulk requests query with filters
            $bulkQuery = BulkStudent::query()
                ->join('bulk_requests', 'bulk_students.Request_ID', '=', 'bulk_requests.Request_ID')
                ->select(
                    'bulk_students.Student_ID as id',
                    'bulk_students.Request_ID as req_no',
                    'bulk_students.Student_Name as full_name',
                    'bulk_requests.Doc_Type as DocType',
                    'bulk_requests.School_Name as request_schl_entity',
                    DB::raw("'Bulk Request' as request_mode"),
                    DB::raw("'Walk In' as release_mode"),
                    DB::raw("NULL as remarks"),
                    'bulk_requests.Status as status',
                    'bulk_requests.request_date as request_date',
                    'bulk_requests.approve_date as approve_date',
                    'bulk_requests.forRelease_date as forRelease_date',
                    'bulk_requests.claimed_date as claimed_date'
                );
            // Build query
            $query = $individualQuery->union($bulkQuery)
                ->whereBetween('request_date', [$startDate, $endDate]);

            // Apply status filter
            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $DocRequests = $query->orderBy('req_no', 'desc')->get();
            $totalCount = $DocRequests->count();

            if ($totalCount === 0) {
                return redirect()->back()->with('error', 'No requests found for the selected date range and status.');
            }

            set_time_limit(300);

            $data = [
                'title' => 'Document Requests Report',
                'date' => Carbon::now()->format('F d, Y'),
                'start_date' => Carbon::parse($startDate)->format('F d, Y'),
                'end_date' => Carbon::parse($endDate)->format('F d, Y'),
                'status_filter' => $statusFilter,
                'DocRequests' => $DocRequests,
                'totalCount' => $totalCount
            ];

            $filename = 'requests_report_' . $startDate . '_to_' . $endDate;
            if ($statusFilter !== 'all') {
                $filename .= '_' . strtolower(str_replace(' ', '_', $statusFilter));
            }
            $filename .= '.pdf';

            $pdf = FacadePdf::loadView('myPDF', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Excel report with date range and status filtering
     */
    /**
 * Export Excel report with date range and status filtering
 */
    public function exportExcel(Request $request)
    {
        // Validate date inputs
        $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
            'status_filter' => 'nullable|string|in:all,Pending,Processing,For Release,Claimed'
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $statusFilter = $request->input('status_filter', 'all');

        try {
            // Build query
            $query = DocumentRequestModel::with(['claimer', 'studentInformation', 'documents'])
                ->whereBetween('request_date', [$startDate, $endDate]);

            // Apply status filter
            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $DocRequests = $query->orderBy('req_no', 'desc')->get();

            if ($DocRequests->isEmpty()) {
                return redirect()->back()->with('error', 'No requests found for the selected date range and status.');
            }

            // Transform data for Excel export - Updated to match new column structure
            $filteredData = $DocRequests->map(function ($item) {
                return [
                    $item->req_no ?? 'N/A',                                                    // Req #
                    $item->studentInformation->full_name ?? 'N/A',                           // Student
                    $item->documents->DocType ?? 'N/A',                                      // Doc
                    $item->request_schl_entity ?? 'N/A',                                     // School
                    $item->request_mode ?? 'N/A',                                            // Via
                    $item->release_mode ?? 'N/A',                                            // Rel Mode
                    $item->remarks ?? 'N/A',                                                 // Remarks
                    $item->status ?? 'N/A',                                                  // Status
                    $item->request_date ? Carbon::parse($item->request_date)->format('Y-m-d') : 'N/A',     // Req Date
                    $item->approve_date ? Carbon::parse($item->approve_date)->format('Y-m-d') : 'N/A',     // App Date
                    $item->forRelease_date ? Carbon::parse($item->forRelease_date)->format('Y-m-d') : 'N/A', // Rel Date
                    $item->claimed_date ? Carbon::parse($item->claimed_date)->format('Y-m-d') : 'N/A',     // Clm Date
                ];
            })->toArray();

            $filename = 'requests_report_' . $startDate . '_to_' . $endDate;
            if ($statusFilter !== 'all') {
                $filename .= '_' . strtolower(str_replace(' ', '_', $statusFilter));
            }
            $filename .= '.xlsx';

            return Excel::download(new ExportRequest($filteredData), $filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating Excel: ' . $e->getMessage());
        }
    }

    /**
     * Handle report generation requests
     */
    public function handleReports(Request $request)
    {
        $action = $request->input('action');

        if ($action === 'pdf') {
            return $this->pdfGenerator($request);
        } elseif ($action === 'excel') {
            return $this->exportExcel($request);
        }

        return redirect()->back()->with('error', 'Invalid action specified.');
    }

    /**
     * Get status statistics for dashboard or reporting
     */
    public function getStatusStatistics()
    {
        $statistics = [
            'total' => DocumentRequestModel::count(),
            'pending' => DocumentRequestModel::where('status', 'Pending')->count(),
            'processing' => DocumentRequestModel::where('status', 'Processing')->count(),
            'for_release' => DocumentRequestModel::where('status', 'For Release')->count(),
            'claimed' => DocumentRequestModel::where('status', 'Claimed')->count(),
        ];

        return response()->json($statistics);
    }
}
