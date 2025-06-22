<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequestModel;
use App\Exports\ExportRequest;
use App\Models\PermissionRoleModel;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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

        // Get total count (unfiltered)
        $totalCount = DocumentRequestModel::count();

        // Build query with relationships
        $query = DocumentRequestModel::with(['claimer', 'studentInformation', 'documents']);

        // Apply status filter if provided
        $statusFilter = $request->input('status');
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Get paginated results
        $DocRequests = $query->orderBy('request_date', 'desc')->paginate(10);

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
            // Build query
            $query = DocumentRequestModel::with(['claimer', 'studentInformation', 'documents'])
                ->whereBetween('request_date', [$startDate, $endDate]);

            // Apply status filter
            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $DocRequests = $query->orderBy('request_date', 'desc')->get();
            $totalCount = $DocRequests->count();

            if ($totalCount === 0) {
                return redirect()->back()->with('error', 'No requests found for the selected date range and status.');
            }

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

        $DocRequests = $query->orderBy('request_date', 'desc')->get();

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
