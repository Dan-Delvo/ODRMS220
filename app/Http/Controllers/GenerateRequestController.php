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
            $individualQuery->where(function ($q) use ($search) {
                $q->where('doc_requests.req_no', 'like', '%' . $search . '%')
                    ->orWhere(DB::raw("CONCAT(std_students.FirstName, ' ', std_students.LastName)"), 'like', '%' . $search . '%')
                    ->orWhere('doc_categories.DocType', 'like', '%' . $search . '%')
                    ->orWhere('doc_requests.request_schl_entity', 'like', '%' . $search . '%')
                    ->orWhere('doc_requests.request_mode', 'like', '%' . $search . '%')
                    ->orWhere('doc_requests.release_mode', 'like', '%' . $search . '%')
                    ->orWhere('doc_requests.remarks', 'like', '%' . $search . '%')
                    // Search dates using DATE_FORMAT for flexible matching
                    ->orWhereRaw("DATE_FORMAT(doc_requests.request_date, '%M %d, %Y') like ?", ['%' . $search . '%'])
                    ->orWhereRaw("DATE_FORMAT(doc_requests.approve_date, '%M %d, %Y') like ?", ['%' . $search . '%'])
                    ->orWhereRaw("DATE_FORMAT(doc_requests.forRelease_date, '%M %d, %Y') like ?", ['%' . $search . '%'])
                    ->orWhereRaw("DATE_FORMAT(doc_requests.claimed_date, '%M %d, %Y') like ?", ['%' . $search . '%'])
                    // Also search raw date format (YYYY-MM-DD)
                    ->orWhere('doc_requests.request_date', 'like', '%' . $search . '%')
                    ->orWhere('doc_requests.approve_date', 'like', '%' . $search . '%')
                    ->orWhere('doc_requests.forRelease_date', 'like', '%' . $search . '%')
                    ->orWhere('doc_requests.claimed_date', 'like', '%' . $search . '%');
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
            $bulkQuery->where(function ($q) use ($search) {
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

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            // Generate HTML for table
            $html = '';

            if ($DocRequests->total() > 0) {
                $html .= '<div class="table-responsive">';
                $html .= '<table class="table table-striped table-bordered table-hover">';
                $html .= '<thead class="table-dark"><tr>';
                $html .= '<th title="Request Number">Req #</th>';
                $html .= '<th>Student</th>';
                $html .= '<th>Doc</th>';
                $html .= '<th title="School/Entity">School</th>';
                $html .= '<th title="Requested Via">Via</th>';
                $html .= '<th title="Release Mode">Rel Mode</th>';
                $html .= '<th>Remarks</th>';
                $html .= '<th>Status</th>';
                $html .= '<th title="Request Date">Req Date</th>';
                $html .= '<th title="Approved Date">App Date</th>';
                $html .= '<th title="For Release Date">Rel Date</th>';
                $html .= '<th title="Claimed Date">Clm Date</th>';
                $html .= '</tr></thead>';
                $html .= '<tbody id="tableBody">';

                foreach ($DocRequests as $item) {
                    $html .= '<tr class="table-row">';
                    $html .= '<td>' . ($item->req_no ?? 'N/A') . '</td>';
                    $html .= '<td>' . strtoupper($item->full_name ?? 'N/A') . '</td>';
                    $html .= '<td>' . ($item->DocType ?? 'N/A') . '</td>';
                    $html .= '<td>' . ($item->request_schl_entity ?? 'N/A') . '</td>';
                    $html .= '<td>' . ($item->request_mode ?? 'Bulk Request') . '</td>';
                    $html .= '<td>' . ($item->release_mode ?? 'Walk In') . '</td>';
                    $html .= '<td>' . ($item->remarks ?? 'N/A') . '</td>';

                    // Status badge
                    $html .= '<td>';
                    switch ($item->status) {
                        case 'Pending':
                            $html .= '<span class="badge bg-warning text-dark">' . $item->status . '</span>';
                            break;
                        case 'Processing':
                            $html .= '<span class="badge bg-info">' . $item->status . '</span>';
                            break;
                        case 'For Release':
                            $html .= '<span class="badge bg-primary">' . $item->status . '</span>';
                            break;
                        case 'Claimed':
                            $html .= '<span class="badge bg-success">' . $item->status . '</span>';
                            break;
                        default:
                            $html .= '<span class="badge bg-danger">' . ($item->status ?? 'Unknown') . '</span>';
                    }
                    $html .= '</td>';

                    // Dates
                    $html .= '<td>' . ($item->request_date ? \Carbon\Carbon::parse($item->request_date)->format('M d, Y') : 'N/A') . '</td>';
                    $html .= '<td>' . ($item->approve_date ? \Carbon\Carbon::parse($item->approve_date)->format('M d, Y') : 'N/A') . '</td>';
                    $html .= '<td>' . ($item->forRelease_date ? \Carbon\Carbon::parse($item->forRelease_date)->format('M d, Y') : 'N/A') . '</td>';
                    $html .= '<td>' . ($item->claimed_date ? \Carbon\Carbon::parse($item->claimed_date)->format('M d, Y') : 'N/A') . '</td>';

                    $html .= '</tr>';
                }

                $html .= '</tbody></table></div>';

                // Pagination
                $html .= '<div class="d-flex flex-column justify-content-center align-items-center mt-3">';
                $html .= $DocRequests->appends($request->query())->render();
                $html .= '<small class="text-muted">Showing ' . $DocRequests->firstItem() . ' - ' . $DocRequests->lastItem() . ' of ' . $DocRequests->total() . '</small>';
                $html .= '</div>';
            } else {
                // No results
                $html .= '<div class="text-center py-5">';
                $html .= '<i class="fas fa-inbox fa-3x text-muted mb-3"></i>';
                $html .= '<h5 class="text-muted">No requests found</h5>';
                $html .= '<p class="text-muted">';

                if ($search) {
                    $html .= 'No requests matching "' . htmlspecialchars($search) . '" found.';
                } elseif ($statusFilter && $statusFilter !== 'all') {
                    $html .= 'No requests with status "' . htmlspecialchars($statusFilter) . '" found.';
                } else {
                    $html .= 'No requests available at the moment.';
                }

                $html .= '</p>';

                if ($search || ($statusFilter && $statusFilter !== 'all')) {
                    $html .= '<a href="#" id="clearFiltersBtn" class="btn btn-outline-primary">';
                    $html .= '<i class="fas fa-redo me-2"></i>Clear Filters';
                    $html .= '</a>';
                }

                $html .= '</div>';
            }

            return response()->json([
                'html' => $html,
                'showing' => $DocRequests->lastItem() - $DocRequests->firstItem() + 1,
                'total' => $DocRequests->total(),
                'totalCount' => $totalCount,
                'currentPage' => $DocRequests->currentPage(),
                'lastPage' => $DocRequests->lastPage()
            ]);
        }

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
            if ($statusFilter && $statusFilter !== 'all') {
                $individualQuery->where('doc_requests.status', $statusFilter);
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

            if ($statusFilter && $statusFilter !== 'all') {
                $bulkQuery->where('bulk_requests.Status', $statusFilter); // Note the capital 'S'
            }

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
            if ($statusFilter && $statusFilter !== 'all') {
                $individualQuery->where('doc_requests.status', $statusFilter);
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

            if ($statusFilter && $statusFilter !== 'all') {
                $bulkQuery->where('bulk_requests.Status', $statusFilter); // Note the capital 'S'
            }

            $query = $individualQuery->union($bulkQuery)
                ->whereBetween('request_date', [$startDate, $endDate]);

            $DocRequests = $query->orderBy('req_no', 'desc')->get();

            if ($DocRequests->isEmpty()) {
                return redirect()->back()->with('error', 'No requests found for the selected date range and status.');
            }

            // Transform data for Excel export - Updated to match new column structure
            $filteredData = $DocRequests->map(function ($item) {
                return [
                    $item->req_no ?? 'N/A',                                                  // Req #
                    $item->full_name ?? 'N/A',                                               // Student
                    $item->DocType ?? 'N/A',                                                 // Doc
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
