<?php

namespace App\Http\Controllers;

use App\Models\RolesModel;
use App\Models\PermissionModel;
use App\Models\PermissionRoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function list()
    {
        $roles = RolesModel::paginate(10);
        return view('maintenance.roles', compact('roles'));
    }

    public function add()
    {
        $getPermission = PermissionModel::getRecord();
        $data = $getPermission;
        return view('maintenance.addRole', compact('data'));
    }

    public function insert(Request $request)
    {
        // Set current_user before DB write
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));

        request()->validate([
            'role' => 'required|unique:role,name',
        ],[
            'role.required' => 'The role field is required.',
            'role.unique' => 'The role name has already been taken.',
        ]);
        $save = new RolesModel();
        $save->name = $request->role;
        $save->save();

        PermissionRoleModel::insertUpdateRecord($request->permission_id, $save->id);

        return redirect('panel/role')->with('status', "Role Successfully created");
    }

    public function edit($id)
    {
        $roles = RolesModel::getSingle($id);
        $getPermission = PermissionModel::getRecord();
        $getRolePermission = PermissionRoleModel::getRolePermission($id);
        return view('maintenance.editRole', compact('roles', 'getPermission', 'getRolePermission'));
    }

    public function update(Request $request, $id)
    {
        // Set current_user before DB update
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));
        $request->validate([
            'role' => [
                'required',
                Rule::unique('role', 'name')->ignore($id),
            ],
        ], [
            'role.required' => 'The role field is required.',
            'role.unique' => 'This role name already exists.',
        ]);

        $save = RolesModel::getSingle($id);
        $save->name = $request->role;
        $save->save();

        PermissionRoleModel::insertUpdateRecord($request->permission_id, $save->id);

        return redirect('panel/role')->with('status', "Role Successfully updated");
    }

    public function delete($id)
    {
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));
        // Check if role has related users
        $relatedUsersCount = DB::table('acc_users')
            ->where('role_id', $id)
            ->count();

        // If there are related users, prevent deletion
        if ($relatedUsersCount > 0) {
            return redirect('panel/role')->with('error', "Cannot delete this role. It is currently assigned to {$relatedUsersCount} user(s). Please reassign or remove the users first.");
        }

        // Set current_user before DB delete

        $save = RolesModel::getSingle($id);
        $save->delete();

        return redirect('panel/role')->with('success', "Role successfully deleted");
    }
}
