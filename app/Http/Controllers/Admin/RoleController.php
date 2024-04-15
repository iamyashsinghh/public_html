<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller {
    public function list() {
        $page_heading = "Roles & Permissions";
        $roles = Role::select('id', 'name', 'permissions', 'created_at')->get();
        return view('admin.venueCrm.role.list', compact('page_heading', 'roles'));
    }

    // Ajax function
    public function manage_ajax($role_id = null) {
        $role = Role::find($role_id);
       
        if ($role) {
            if (sizeof($role->get_assigned_members) > 0) {
                $role_assigned = true;
            } else {
                $role_assigned = false;
            }
            return response()->json(['success' => true, 'role' => $role, 'role_assigned' => $role_assigned]);
        } else {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    //Ajax function
    public function manage_process($role_id = 0, Request $request) {
        $is_role_name_validate = $role_id == 0 ? 'required' : '';
        $validate = Validator::make($request->all(), [
            'role_name' => $is_role_name_validate,
            'permissions' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()], 400);
        }

        if ($role_id > 0) {
            $role = Role::find($role_id);
            $msg = "Role updated successfully.";
        } else {
            $role = new Role();
            $msg = "Role added successfully.";
            $role->name = $request->role_name;
        }

        $role->permissions = json_encode($request->permissions);
        $role->save();

        return response()->json(['success' => true, 'alert_type' => 'success', 'message' => $msg], 200);
    }
}
