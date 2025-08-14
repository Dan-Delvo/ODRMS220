<?php

namespace App\Http\Controllers;

use App\Models\StudentInformationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    // Show the student information form
    public function create()
    {
        $grade = ['7', '8', '9', '10', '11', '12'];
        $stat = ['Alumni', 'Regular', 'ALS'];
        return view('common.studentregister', compact('grade', 'stat'));
    }

    // Store the student information and save the ID in session
    public function store(Request $request)
    {
        $request->validate([
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'LRN' => 'required|digits:12|unique:std_students,LRN',
            'Grade_level' => 'required|string|max:10',
            'Std_status' => 'required|string|max:50',
            'Last_sy_attended' => 'required|digits:4',
        ], [
            'FirstName.required' => 'Please enter your first name.',
            'LastName.required' => 'Please enter your last name.',
            'LRN.digits' => 'LRN must be exactly 12 digits.',
            'LRN.unique' => 'LRN must be unique',
            'Last_sy_attended.digits' => 'Last school year must be 4 digits (e.g. 2024).',
        ]);

        Log::info($request);

        // Set SQL session user variable
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));

        // Save student information in the database
        $studentInformation = StudentInformationModel::create($request->all());

        // Store the student information ID in session
        Session::put('std_students_id', $studentInformation->id);

        // Redirect to the account creation form
        return redirect()->route('account.create');
    }
}
