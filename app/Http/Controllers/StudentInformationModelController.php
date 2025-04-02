<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentInformationModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class StudentInformationModelController extends Controller
{
    //
    public function display()
    {
        $PermissionStud = PermissionRoleModel::getPermission('student', Auth::user()->role_id);
        if(empty($PermissionStud))
        {
            abort(404);
        }

        $data = PermissionRoleModel::getPermission('studentEdit', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('studentDelete', Auth::user()->role_id);
        $data2 = PermissionRoleModel::getPermission('studentInfo', Auth::user()->role_id);
        $user = StudentInformationModel::paginate(10);
        return view('maintenance.student', compact('user'))
        ->with([
            'PermissionEdit' => $data,
            'PermissionDelete' => $data1,
            'PermissionInfo' => $data2,
        ]);
    }

    public function edit($id)
    {
        $student = StudentInformationModel::find($id);
        $gradeLevels = ['7', '8', '9', '10', '11', '12']; // Example grade levels

        if (!$student) {
            return redirect()->route('students.index')->with('error', 'Student not found.');
        }

        return view('maintenance.editStudent', compact('student', 'gradeLevels'));
    }

    public function update(Request $request, $id)
    {

        // Find the student record
        $student = StudentInformationModel::find($id);

        // Check if student exists
        if (!$student) {
            return redirect()->route('students.index')->with('error', 'Student not found.');
        }

        // Validate incoming data
        $validatedData = $request->validate([
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'LRN' => 'nullable|string|max:255',
            'Grade_level' => 'required|string|max:255',
            'Std_status' => 'required|in:Active,Inactive',
            'Last_sy_attended' => 'nullable|string|max:255',
        ]);



        // Update the student record
        $student->FirstName = $validatedData['FirstName'];
        $student->LastName = $validatedData['LastName'];
        $student->LRN = $validatedData['LRN'];
        $student->Grade_level = $validatedData['Grade_level'];
        $student->Std_status = $validatedData['Std_status'];
        $student->Last_sy_attended = $validatedData['Last_sy_attended'];

        // Save changes to the database
        $student->save();



        // Redirect back to the students list with a success message
        return redirect()->route('student')->with('Success', 'Student updated successfully.');
    }

    public function delete($id)
    {
        // Find the user by ID
        $user = Account::find($id);
        $stud = StudentInformationModel::find($id);

        // Check if the user exists
        if ($user) {
            // Delete the user and associated student information
            $user->delete();
        }

        // Always delete the student information regardless of user presence
        if ($stud) {
            $stud->delete();
        }

        // Redirect back with success message
        return redirect('panel/student')->with('Danger', 'User successfully deleted');
    }

    public function show($id)
    {

        $student = StudentInformationModel::find($id);

        return view('maintenance.showStudent', compact('student'));

    }

}
