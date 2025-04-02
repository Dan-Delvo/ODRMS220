<?php

namespace App\Http\Controllers;
use App\Models\DocumentRequestModel;
use App\Exports\ExportRequest;
use App\Models\PermissionRoleModel;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use pBarryvdh\DomPDF\Facade\Pdf;

class GenerateRequestController extends Controller
{
    //

    public function display()
    {
        $PermissionGen = PermissionRoleModel::getPermission('generateReports', Auth::user()->role_id);
        if(empty($PermissionGen))
        {
            abort(404);
        }
        $totalCount = DocumentRequestModel::count();
        $DocRequests = DocumentRequestModel::with('claimer')
        ->with('studentInformation')
        ->paginate(9);


        return view('generation.generateRequest', compact('DocRequests', 'totalCount'));

    }

    public function pdfGenerator(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $DocRequests = DocumentRequestModel::with('claimer', 'studentInformation')
            ->whereBetween('request_date', [$startDate, $endDate])
            ->get();

        $totalCount = $DocRequests->count();

        $data = [
            'title' => 'Requests Report',
            'date' => date('m/d/y'),
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount
        ];

        $pdf = FacadePdf::loadView('myPDF', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('requests_report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filteredData = DocumentRequestModel::with('claimer', 'studentInformation', 'documents')
            ->whereBetween('request_date', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'ID' => $item->id,
                    'Claimer' => $item->claimer->full_name ?? 'N/A',
                    'Student' => $item->studentInformation->full_name ?? 'N/A',
                    'Document' => $item->documents->DocType ?? 'N/A',
                    'School' => $item->request_schl_entity,
                    'Requested Via' => $item->request_mode,
                    'Release Mode' => $item->release_mode,
                    'Remarks' => $item->remarks,
                    'Status' => $item->status,
                ];
            });


        return Excel::download(new ExportRequest($filteredData), 'report.xlsx');
    }


    public function handleReports(Request $request)
    {
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $action = $request->query('action');

    if ($action === 'pdf') {
        return $this->pdfGenerator($request);
    } elseif ($action === 'excel') {
        return $this->exportExcel($request);
    }
    }

}
