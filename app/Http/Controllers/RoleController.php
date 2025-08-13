<?php

namespace App\Http\Controllers;

use App\Models\RolesModel;
use App\Models\PermissionModel;
use App\Models\PermissionRoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $save = new RolesModel();
        $save->name = $request->role;
        $save->save();

        PermissionRoleModel::insertUpdateRecord($request->permission_id, $save->id);

        return redirect('panel/role')->with('Status', "Role Successfully created");
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

        $save = RolesModel::getSingle($id);
        $save->name = $request->role;
        $save->save();

        PermissionRoleModel::insertUpdateRecord($request->permission_id, $save->id);

        return redirect('panel/role')->with('Status', "Role Successfully updated");
    }

    public function delete($id)
    {
        // Set current_user before DB delete
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote(Auth::check() ? Auth::user()->username : 'guest'));

        $save = RolesModel::getSingle($id);
        $save->delete();

        return redirect('panel/role')->with('Danger', "Role Successfully deleted");
    }
}
