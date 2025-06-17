<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentInformationModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\DocumentRequestModel;

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
            'Std_status' => 'required|in:Regular,Alumni,ALS',
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
        // Find the student by ID
        $stud = StudentInformationModel::find($id);

        // Check if the student exists
        if (!$stud) {
            return redirect('panel/student')->with('error', 'Student not found.');
        }

        // Check if student has existing document requests
        $hasRequests = DocumentRequestModel::where('std_students_id', $id)->exists();

        if ($hasRequests) {
            // Get the count of document requests for more detailed notification
            $requestCount = DocumentRequestModel::where('std_students_id', $id)->count();

            return redirect('panel/student')->with([
                'warning' => 'Cannot delete student: ' . $stud->FirstName . ' ' . $stud->LastName,
                'warning_details' => 'This student has ' . $requestCount . ' existing document request(s). Please complete or cancel all document requests before deleting the student record.',
                'student_name' => $stud->FirstName . ' ' . $stud->LastName,
                'request_count' => $requestCount
            ]);
        }

        // Store student name for success message
        $studentName = $stud->FirstName . ' ' . $stud->LastName;

        // Find the associated user account
        $user = Account::find($id);

        try {
            // Delete the user account if it exists
            if ($user) {
                $user->delete();
            }

            // Delete the student information
            $stud->delete();

            // Redirect back with success message
            return redirect('panel/student')->with([
                'success' => 'Student successfully deleted',
                'success_details' => $studentName . ' has been removed from the system.',
            ]);

        } catch (\Exception $e) {
            // Handle any database errors
            return redirect('panel/student')->with([
                'error' => 'Failed to delete student',
                'error_details' => 'An error occurred while trying to delete ' . $studentName . '. Please try again or contact the administrator.',
            ]);
        }
    }

    public function show($id)
    {
        $student = StudentInformationModel::find($id);

        return view('maintenance.showStudent', compact('student'));
    }

    /**
     * Check if student can be deleted (AJAX endpoint for frontend confirmation)
     */
    public function checkDeletable($id)
    {
        $student = StudentInformationModel::find($id);

        if (!$student) {
            return response()->json([
                'deletable' => false,
                'message' => 'Student not found.'
            ]);
        }

        $hasRequests = DocumentRequestModel::where('std_students_id', $id)->exists();
        $requestCount = DocumentRequestModel::where('std_students_id', $id)->count();

        return response()->json([
            'deletable' => !$hasRequests,
            'student_name' => $student->FirstName . ' ' . $student->LastName,
            'request_count' => $requestCount,
            'message' => $hasRequests
                ? 'This student has ' . $requestCount . ' existing document request(s) and cannot be deleted.'
                : 'Student can be deleted.'
        ]);
    }
}
