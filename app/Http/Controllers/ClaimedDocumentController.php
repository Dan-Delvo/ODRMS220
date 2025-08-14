<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequestModel;
use App\Models\StudentInformationModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ClaimerModel;
use App\Models\DocumentsModel;

class ClaimedDocumentController extends Controller
{
    public function index()
    {
        $totalCount = DocumentRequestModel::where('status', 'Claimed')->count();
        $DocRequests = DocumentRequestModel::where('status', 'Claimed')
            ->with('claimer')
            ->with('studentInformation')
            ->with('documents')
            ->orderBy('claimed_date', 'desc')
            ->paginate(9);

        return view('requestTables.claimed.claimed', [
            'DocRequests' => $DocRequests,
            'totalCount' => $totalCount,
        ]);
    }

    public function create()
    {
        return view('requestTables.claimed.createTable');
    }

    public function store(Request $request)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $request = $this->validateDocumentRequest($request);
        DocumentRequestModel::createDocumentRequest($request);
        return redirect('/claimed-documents')->with('Status', 'Created Successfully');
    }

    public function show($id)
    {
        Log::info('Requested ID: ' . $id);
        $table = DocumentRequestModel::with(['claimer', 'studentInformation', 'documents'])->find($id);

        if (!$table) {
            Log::error('No record found for ID: ' . $id);
            return response()->json(['error' => 'Record not found'], 404);
        }

        return view('requestTables.claimed.showTable', compact('table'));
    }

    public function edit(DocumentRequestModel $table)
    {
        if (!$table) {
            abort(404, 'Document Request not found.');
        }

        $DocType = DocumentsModel::all();
        return view('requestTables.claimed.editTable', compact('table','DocType'));
    }

    public function update(Request $request, DocumentRequestModel $documentRequestModel)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $validated = $this->validateDocumentRequest($request);
        DocumentRequestModel::updateOrCreateRequest($validated);

        $studentId = $documentRequestModel->student_information_id;
        $student = StudentInformationModel::find($studentId);

        return redirect('/claimed-documents')->with('Status', 'Updated Successfully');
    }

    public function validateDocumentRequest(Request $request)
    {
        return $request->validate([
            'id' => 'required',
            'claimer_id' => 'required',
            'document_id' => 'required',
            'request_schl_entity' => 'required|string|max:255',
            'request_mode' => 'required|string|max:255',
            'release_mode' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'status' => 'required|string',
        ]);
    }

    public function destroy($id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $table = DocumentRequestModel::find($id);

        if ($table) {
            $table->delete();
            return redirect('/claimed-documents')->with('Danger', 'Deleted Successfully');
        }

        return redirect('/claimed-documents')->with('error', 'Record not found');
    }

    public function revertToForRelease(Request $request, $id)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        try {
            $request->validate([
                'revert_reason' => 'required|string|max:500',
            ]);

            $documentRequest = DocumentRequestModel::findOrFail($id);

            if ($documentRequest->status !== 'Claimed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request is not in claimed status.',
                ], 400);
            }

            $documentRequest->update([
                'status' => 'For Release',
                'claimed_date' => null,
                'remarks' => $request->revert_reason,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document has been successfully reverted to For Release status.',
                    'redirect' => route('claimed-documents.index')
                ]);
            }

            return redirect('/claimed-documents')->with('Status', 'Document reverted to For Release successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in revertToForRelease: ' . json_encode($e->errors()));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('Danger', 'Please check the form for errors.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Model not found in revertToForRelease: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found.',
                ], 404);
            }

            return redirect()->back()->with('Danger', 'Document request not found.');

        } catch (\Exception $e) {
            Log::error('Error in revertToForRelease: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing the request. Please try again.',
                ], 500);
            }

            return redirect()->back()->with('Danger', 'An error occurred while processing the request. Please try again.');
        }
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $claimedDocuments = DocumentRequestModel::where('status', 'Claimed')
            ->whereBetween('claimed_date', [$startDate, $endDate])
            ->with(['claimer', 'studentInformation', 'documents'])
            ->orderBy('claimed_date', 'desc')
            ->get();

        return view('requestTables.claimed.report', compact('claimedDocuments', 'startDate', 'endDate'));
    }

    public function exportToCsv(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $claimedDocuments = DocumentRequestModel::where('status', 'Claimed')
            ->whereBetween('claimed_date', [$startDate, $endDate])
            ->with(['claimer', 'studentInformation', 'documents'])
            ->orderBy('claimed_date', 'desc')
            ->get();

        $filename = 'claimed_documents_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($claimedDocuments) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Request No',
                'Student Name',
                'Document Type',
                'School/Entity',
                'Request Mode',
                'Release Mode',
                'Claimer Name',
                'Claimer Contact',
                'Request Date',
                'Approved Date',
                'For Release Date',
                'Claimed Date',
                'Remarks'
            ]);

            foreach ($claimedDocuments as $document) {
                fputcsv($file, [
                    $document->req_no,
                    $document->studentInformation->full_name ?? 'N/A',
                    $document->documents->DocType ?? 'N/A',
                    $document->request_schl_entity,
                    $document->request_mode,
                    $document->release_mode,
                    ($document->claimer->Fname ?? '') . ' ' . ($document->claimer->Lname ?? ''),
                    $document->claimer->contact_no ?? 'N/A',
                    $document->request_date,
                    $document->approve_date,
                    $document->forRelease_date,
                    $document->claimed_date,
                    $document->remarks
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
