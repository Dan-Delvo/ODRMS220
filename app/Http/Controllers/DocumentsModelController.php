<?php

namespace App\Http\Controllers;

use App\Models\DocumentsModel;
use App\Models\DocumentRequestModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DocumentsModelController extends Controller
{
    public function display()
    {
        $Doc = DocumentsModel::paginate(9);
        $count = DocumentsModel::count();
        return view('maintenance.docs', compact('Doc', 'count'));
    }

    public function edit($id)
    {
        // Set current user
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $document = DocumentsModel::findOrFail($id);
        return view('maintenance.editDocs', compact('document'));
    }

    public function add()
    {
        // Set current user
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        return view('maintenance.addDocs');
    }

    public function insert(Request $request)
    {
        // Set current user
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        // Validate the request data
        $request->validate([
            'Type' => 'required|string|max:255|unique:doc_categories,DocType',
            'Price' => 'required|numeric|min:0',
        ], [
            'Type.unique' => 'This document type already exists.',
        ]);

        DocumentsModel::create([
            'DocType' => $request->input('Type'),
            'DocPrice' => $request->input('Price'),
        ]);

        return redirect()->route('doc')->with('success', 'Document added successfully.');
    }

    public function update(Request $request, $id)
    {
        // Set current user
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $validated = $request->validate([
            'Type' => 'required|string|max:255|unique:doc_categories,DocType,' . $id,
            'Price' => 'required|numeric|min:0',
        ], [
            'DocType.unique' => 'This document type already exists.',
        ]);

        $document = DocumentsModel::findOrFail($id);
        $document->update([
            'DocType' => $validated['Type'],
            'DocPrice' => $validated['Price'],
        ]);

        return redirect()->route('doc')->with('success', 'Document updated successfully.');
    }

    public function destroy($id)
    {
        try {
            // Set current user
            $pdo = DB::connection()->getPdo();
            $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

            $document = DocumentsModel::findOrFail($id);
            
            // ✅ Store document name FIRST before any operations
            $documentName = $document->DocType;
            
            // ✅ Debug: Log what we're checking
            \Log::info('Attempting to delete document', [
                'id' => $id,
                'name' => $documentName
            ]);

            // Get detailed request statistics
            $stats = $document->getRequestStatistics();
            
            // ✅ Debug: Log statistics
            \Log::info('Document statistics', $stats);

            // Check if there are active requests blocking deletion
            if ($stats['active'] > 0) {
                $breakdown = [];
                if ($stats['pending'] > 0) $breakdown[] = "{$stats['pending']} Pending";
                if ($stats['processing'] > 0) $breakdown[] = "{$stats['processing']} Processing";
                if ($stats['for_release'] > 0) $breakdown[] = "{$stats['for_release']} For Release";

                \Log::info('Deletion blocked - Active requests found', [
                    'active' => $stats['active'],
                    'breakdown' => $breakdown
                ]);

                return redirect()->route('doc')->with([
                    'warning' => "Cannot delete this document. There are active requests that must be completed first.",
                    'document_name' => $documentName,
                    'active_requests' => $stats['active'],
                    'request_breakdown' => implode(', ', $breakdown),
                ]);
            }

            // Check if there are any historical requests
            if ($stats['total'] > 0) {
                $history = [];
                if ($stats['claimed'] > 0) $history[] = "{$stats['claimed']} completed";
                if ($stats['declined'] > 0) $history[] = "{$stats['declined']} declined";

                \Log::info('Deletion blocked - Historical requests found', [
                    'total' => $stats['total'],
                    'history' => $history
                ]);

                return redirect()->route('doc')->with([
                    'warning' => "Cannot delete this document. Deleting it may affect historical records and reports.",
                    'document_name' => $documentName,
                    'total_requests' => $stats['total'],
                    'history_breakdown' => implode(' and ', $history),
                ]);
            }

            // ✅ Safe to delete - No requests found
            \Log::info('Deleting document - No requests found', [
                'id' => $id,
                'name' => $documentName
            ]);

            $document->delete();

            return redirect()->route('doc')->with('success', 
                "Document {$documentName} has been successfully deleted.");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Document not found', ['id' => $id]);
            return redirect()->route('doc')->with('error', 
                'Document not found. It may have already been deleted.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database constraint error deleting document', [
                'id' => $id,
                'error' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A'
            ]);
            
            return redirect()->route('doc')->with('error', 
                'Unable to delete document due to database constraints. There may be related records that prevent deletion. Please contact the administrator.');
                
        } catch (\Exception $e) {
            \Log::error('Unexpected error deleting document', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('doc')->with('error', 
                'An unexpected error occurred. Please try again or contact the administrator.');
        }
    }

    public function generateCertificate($id)
    {
        $document = DocumentRequestModel::findOrFail($id);
        $name = $document->studentInformation->full_name;
        $gradeSection = '7 - Grow A Garden';
        $date = Carbon::now();
        $year = Carbon::now()->year;
        $nextYr = Carbon::now()->addYear()->year;
        $principal = 'Shallum Gil Salazar';
        $lrn = $document->studentInformation->LRN;

        $templatePath = storage_path('\CERTIFICATE OF GOOD MORAL CHARACTER.docx');
        $templateProcessor = new TemplateProcessor($templatePath);

        $templateProcessor->setValue('name', $name);
        $templateProcessor->setValue('grade_section', $gradeSection);
        $templateProcessor->setValue('date', $date);
        $templateProcessor->setValue('year', $year);
        $templateProcessor->setValue('nextYr', $nextYr);
        $templateProcessor->setValue('principal', $principal);
        $templateProcessor->setValue('lrn', $lrn);

        $fileName = 'certificate_' . time() . '.docx';
        $outputPath = storage_path("app/generated/{$fileName}");
        $templateProcessor->saveAs($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}
