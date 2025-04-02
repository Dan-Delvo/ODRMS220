<?php

namespace App\Http\Controllers;

use App\Models\DocumentsModel;
use Illuminate\Http\Request;

class DocumentsModelController extends Controller
{
    //
    public function display()
    {
        $Doc = DocumentsModel::paginate(9);

        return view('maintenance.docs', compact('Doc'));
    }

    public function edit($id)
    {
        // Find the document by ID or throw a 404 error if not found
        $document = DocumentsModel::findOrFail($id);

        // Return the edit view and pass the document data to it
        return view('maintenance.editDocs', compact('document'));
    }

    public function add()
    {
        // Return the view to add a new document
        return view('maintenance.addDocs');
    }

    public function insert(Request $request)
    {
        // Validate the request data
        $request->validate([
            'DocType' => 'required|string|max:255', // Ensure DocType is provided and is a valid string
        ]);

        // Create a new document record
        DocumentsModel::create([
            'DocType' => $request->input('DocType'),
        ]);

        // Redirect back to the documents list with a success message
        return redirect()->route('doc')->with('Status', 'Document added successfully.');
    }


    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'DocType' => 'required|string|max:255', // Ensure DocType is provided and is a valid string
        ]);

        // Find the document by ID or throw a 404 error if not found
        $document = DocumentsModel::findOrFail($id);

        // Update the document with the validated data
        $document->update([
            'DocType' => $request->input('DocType'),
        ]);

        // Redirect back to the documents list with a success message
        return redirect()->route('doc')->with('Success', 'Document updated successfully.');
    }

    public function destroy($id)
    {
        // Find the document by ID or throw a 404 error if not found
        $document = DocumentsModel::findOrFail($id);

        // Delete the document
        $document->delete();

        // Redirect back to the documents list with a success message
        return redirect()->route('doc')->with('Danger', 'Document deleted successfully.');
    }


}
