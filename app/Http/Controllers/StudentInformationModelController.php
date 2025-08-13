<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentInformationModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\DocumentRequestModel;

class StudentInformationModelController extends Controller
{
    public function display()
    {
        $PermissionStud = PermissionRoleModel::getPermission('student', Auth::user()->role_id);
        if (empty($PermissionStud)) {
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
        // Set @current_user before updating
        DB::connection()->getPdo()->exec("SET @current_user = " .
            DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));

        // Find the student record
        $student = StudentInformationModel::find($id);

        if (!$student) {
            return redirect()->route('students.index')->with('error', 'Student not found.');
        }

        $validatedData = $request->validate([
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'LRN' => 'nullable|string|max:255',
            'Grade_level' => 'required|string|max:255',
            'Std_status' => 'required|in:Regular,Alumni,ALS',
            'Last_sy_attended' => 'nullable|string|max:255',
        ]);

        $student->FirstName = $validatedData['FirstName'];
        $student->LastName = $validatedData['LastName'];
        $student->LRN = $validatedData['LRN'];
        $student->Grade_level = $validatedData['Grade_level'];
        $student->Std_status = $validatedData['Std_status'];
        $student->Last_sy_attended = $validatedData['Last_sy_attended'];
        $student->save();

        return redirect()->route('student')->with('Success', 'Student updated successfully.');
    }

    public function delete($id)
    {
        // Set @current_user before deleting
        DB::connection()->getPdo()->exec("SET @current_user = " .
            DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $stud = StudentInformationModel::find($id);

        if (!$stud) {
            return redirect('panel/student')->with('error', 'Student not found.');
        }

        $hasRequests = DocumentRequestModel::where('std_students_id', $id)->exists();

        if ($hasRequests) {
            $requestCount = DocumentRequestModel::where('std_students_id', $id)->count();

            return redirect('panel/student')->with([
                'warning' => 'Cannot delete student: ' . $stud->FirstName . ' ' . $stud->LastName,
                'warning_details' => 'This student has ' . $requestCount . ' existing document request(s). Please complete or cancel all document requests before deleting the student record.',
                'student_name' => $stud->FirstName . ' ' . $stud->LastName,
                'request_count' => $requestCount
            ]);
        }

        $studentName = $stud->FirstName . ' ' . $stud->LastName;
        $user = Account::find($id);

        try {
            if ($user) {
                $user->delete();
            }

            $stud->delete();

            return redirect('panel/student')->with([
                'success' => 'Student successfully deleted',
                'success_details' => $studentName . ' has been removed from the system.',
            ]);
        } catch (\Exception $e) {
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
