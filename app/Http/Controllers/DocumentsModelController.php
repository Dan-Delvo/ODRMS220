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
            'DocType.unique' => 'This document type already exists.',
        ]);

        DocumentsModel::create([
            'DocType' => $request->input('DocType'),
            'DocPrice' => $request->input('DocPrice'),
        ]);

        return redirect()->route('doc')->with('status', 'Document added successfully.');
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
        // Set current user
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $document = DocumentsModel::findOrFail($id);
        $document->delete();

        return redirect()->route('doc')->with('success', 'Document deleted successfully.');
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
