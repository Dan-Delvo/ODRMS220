<?php

namespace App\Http\Controllers;

use App\Models\StudentInformationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    //
        // Show the student information form
        public function create()
        {
            $grade = ['7', '8', '9', '10', '11', '12'];
            $stat = ['Alumni', 'Regular', 'ALS'];
            return view('common.studentregister', compact('grade', 'stat'));  // This is the view where the student fills in the information
        }

        // Store the student information and save the ID in session
        public function store(Request $request)
        {

            $request->validate([
                'FirstName' => 'required|string|max:255',
                'LastName' => 'required|string|max:255',
                'LRN' => 'required|string|max:12',
                'Grade_level' => 'required|string|max:10',
                'Std_status' => 'required|string|max:50',
                'Last_sy_attended' => 'required|string|max:4',
            ]);

            Log::info($request);


            // Save student information in the database
            $studentInformation = StudentInformationModel::create($request->all());

            // Store the student information ID in session
            Session::put('std_students_id', $studentInformation->id);

            // Redirect to the account creation form
            return redirect()->route('account.create');
        }
}
