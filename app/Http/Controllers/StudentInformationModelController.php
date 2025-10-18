<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentInformationModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\DocumentRequestModel;
use Illuminate\Validation\Rule;

class StudentInformationModelController extends Controller
{
    public function display(Request $request)
    {
        // Check permission
        $PermissionStud = PermissionRoleModel::getPermission('student', Auth::user()->role_id);
        if (empty($PermissionStud)) {
            abort(404);
        }

        // Get search and sort parameters
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');

        // Validate sort column to prevent SQL injection
        $allowedSortColumns = ['id', 'LastName', 'FirstName', 'LRN', 'Grade_level', 'Last_sy_attended'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }

        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        // Build query
        $query = StudentInformationModel::query();

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('LastName', 'like', '%' . $search . '%')
                  ->orWhere('FirstName', 'like', '%' . $search . '%')
                  ->orWhere('MiddleName', 'like', '%' . $search . '%')
                  ->orWhere('LRN', 'like', '%' . $search . '%')
                  ->orWhere('Grade_level', 'like', '%' . $search . '%')
                  ->orWhere('Std_status', 'like', '%' . $search . '%')
                  ->orWhere('Last_sy_attended', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results (10 per page to match your original)
        $user = $query->paginate(10);

        // Get permissions
        $data = PermissionRoleModel::getPermission('studentEdit', Auth::user()->role_id);
        $data1 = PermissionRoleModel::getPermission('studentDelete', Auth::user()->role_id);
        $data2 = PermissionRoleModel::getPermission('studentInfo', Auth::user()->role_id);

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
            'LRN' => 'nullable|string|max:255|unique:std_students,LRN',
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

    public function showProfile()
    {
        $studID = Auth::user()->std_students_id;
        $studInfo = StudentInformationModel::where('id', $studID)
            ->with('documentRequests', 'documentRequests.claimer', 'account')
            ->first(); // Execute the query

        $grade = ['7', '8', '9', '10', '11', '12'];
        $stat = ['Alumni', 'Regular', 'ALS'];

        if (!$studInfo) {
            return redirect()->route('st.page')->with('error', 'Student information not found.');
        }

        return view('common.studentProfile', compact('studInfo', 'grade', 'stat'));
    }

    public function updateProfile(Request $request, $id)
    {
        $studInfo = StudentInformationModel::find($id);

        if (!$studInfo) {
            return redirect()->route('st.page')->with('Error', 'Student information not found.');
        }

        $validatedData = $request->validate([
            'FirstName' => 'required|string|max:255',
            'MiddleName' => 'nullable|string|max:255',
            'LastName' => 'required|string|max:255',
            'LRN' => [
                'sometimes',
                'filled',
                'string',
                'digits:12',
                Rule::unique('std_students', 'LRN')->ignore($studInfo->id),
            ],
            'Grade_level' => 'required|string|max:255',
            'Suffix' => 'nullable|string|max:10',
            'Std_status' => 'required|string',  // ✅ Changed from 'status'
            'Last_sy_attended' => 'nullable|string|max:255',
            'Id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'LRN.digits' => 'LRN must be exactly 12 digits.',
            'LRN.unique' => 'LRN must be unique.',
            'Std_status.required' => 'Status is required.',
        ]);

        $fieldMap = [
            'FirstName' => 'FirstName',
            'MiddleName' => 'MiddleName',
            'LastName' => 'LastName',
            'LRN' => 'LRN',
            'Grade_level' => 'Grade_level',
            'Suffix' => 'Suffix',
            'Std_status' => 'Std_status',  // ✅ Changed from 'status' => 'Std_status'
            'Last_sy_attended' => 'Last_sy_attended',
        ];

        $changes = [];

        foreach ($fieldMap as $formField => $dbField) {
            if (array_key_exists($formField, $validatedData)) {
                $oldValue = trim((string)($studInfo->{$dbField} ?? ''));
                $newValue = trim((string)($validatedData[$formField] ?? ''));

                if ($oldValue !== $newValue) {
                    $changes[$dbField] = $validatedData[$formField];
                }
            }
        }

        // Handle file upload
        if ($request->hasFile('Id_image')) {
            $image = $request->file('Id_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/supporting_documents'), $imageName);

            // Delete old image if exists
            if (!empty($studInfo->Id_image) && file_exists(public_path($studInfo->Id_image))) {
                @unlink(public_path($studInfo->Id_image));
            }

            $changes['Id_image'] = 'uploads/supporting_documents/' . $imageName;
        }

        if (empty($changes)) {
            return redirect()->back()->with('Info', 'No changes were made.');
        }

        $studInfo->update($changes);

        return redirect()->route('student.profile')->with('Success', 'Profile updated successfully.');
    }
}
