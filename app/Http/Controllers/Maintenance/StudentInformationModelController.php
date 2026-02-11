<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentInformationModel;
use App\Models\PermissionRoleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\DocumentRequestModel;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class StudentInformationModelController extends Controller
{
    public function display(Request $request)
    {
        try {
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
                $searchTerm = trim($search);
                $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(LastName) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw('LOWER(FirstName) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw('LOWER(COALESCE(MiddleName, "")) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw('LOWER(COALESCE(Suffix, "")) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw('LOWER(LRN) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw('LOWER(Grade_level) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw('LOWER(COALESCE(Std_status, "")) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw('LOWER(COALESCE(Last_sy_attended, "")) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw("LOWER(CONCAT(FirstName, ' ', LastName)) LIKE ?", ['%' . strtolower($searchTerm) . '%'])
                    ->orWhereRaw("LOWER(CONCAT(FirstName, ' ', COALESCE(MiddleName, ''), ' ', LastName)) LIKE ?", ['%' . strtolower($searchTerm) . '%']);
            });
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results (10 per page)
            $user = $query->paginate(10);

            // Get permissions
            $PermissionEdit = PermissionRoleModel::getPermission('studentEdit', Auth::user()->role_id);
            $PermissionDelete = PermissionRoleModel::getPermission('studentDelete', Auth::user()->role_id);
            $PermissionInfo = PermissionRoleModel::getPermission('studentInfo', Auth::user()->role_id);

            // Define table columns for dynamic table
            $tableColumns = [
                [
                    'label' => 'Last Name',
                    'field' => 'LastName'
                ],
                [
                    'label' => 'First Name',
                    'field' => 'FirstName'
                ],
                [
                    'label' => 'Middle Name',
                    'field' => 'MiddleName'
                ],
                [
                    'label' => 'Suffix',
                    'field' => 'Suffix'
                ],
                [
                    'label' => 'LRN',
                    'field' => 'LRN'
                ],
                [
                    'label' => 'Grade Level',
                    'field' => 'Grade_level'
                ],
                [
                    'label' => 'Status',
                    'field' => 'Std_status'
                ],
                [
                    'label' => 'Last SY Attended',
                    'field' => 'Last_sy_attended'
                ]
            ];

            // Check if it's an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('maintenance.table', [
                        'items' => $user,
                        'columns' => $tableColumns,
                        'routePrefix' => 'student',
                        'primaryKey' => 'id',
                        'permissions' => [
                            'edit' => $PermissionEdit,
                            'delete' => $PermissionDelete,
                            'info' => $PermissionInfo
                        ],
                        'emptyMessage' => 'No students found matching your search criteria.'
                    ])->render(),
                    'pagination' => view('maintenance.pagination', ['items' => $user])->render(),
                    'total' => $user->total(),
                    'showing' => [
                        'from' => $user->firstItem(),
                        'to' => $user->lastItem(),
                        'total' => $user->total()
                    ]
                ]);
            }

            return view('maintenance.student', compact('user'))
                ->with([
                    'PermissionEdit' => $PermissionEdit,
                    'PermissionDelete' => $PermissionDelete,
                    'PermissionInfo' => $PermissionInfo,
                    'tableColumns' => $tableColumns
                ]);
        } catch (Exception $e) {
            Log::error('Error in student display method: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while loading students.'
                ], 500);
            }

            return redirect()->back()->with('Danger', 'An error occurred while loading students.');
        }
    }




    public function edit($id)
    {
        DB::connection()->getPdo()->exec("SET @current_user = " .
            DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));
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

        if ($request->id != $id) {
            // Return with a SweetAlert flash message instead of abort(403)
            return redirect()->back()->with([
                'swal_error_title' => 'Unauthorized Action',
                'swal_error_text' => 'You are not allowed to modify the Student ID or Account ID.',
                'swal_error_icon' => 'error'
            ]);
        }
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
        try {
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
                'Id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
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
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('st.page')->with('Error', 'An unexpected error occurred while updating the profile.');
        }
    }
}
