<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginInfo;
use App\Models\Role;
use App\Models\TeamMember;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeamMemberController extends Controller {
    public function list() {
        $page_heading = "Team Members";
        $roles = Role::get();
        return view('admin.venueCrm.team.list', compact('page_heading', 'roles',));
    }

    public function ajax_list(Request $request) {
        $role_id = $request->input('role_id');

        $membersQuery = TeamMember::select(
            'team_members.id',
            'team_members.profile_image',
            'team_members.name',
            'team_members.mobile',
            'team_members.email',
            'team_members.venue_name',
            'team_members.role_id',
            'r.name as role_name',
            'team_members.status',
            'team_members.created_at',
            'team_members.is_active',
        )->leftJoin("roles as r", 'team_members.role_id', '=', 'r.id');

        if ($role_id) {
            $membersQuery->where('team_members.role_id', $role_id);
        }
        $members = $membersQuery->get();
        return datatables($members)->toJson();
    }


    public function manage($id = 0) {
        $roles = Role::select('id', 'name')->get();
        $venues = Venue::select('id', 'name')->get();
        $manager_role = Role::select('id')->where('name', 'like', '%Manager%')->first();
        if ($manager_role) {
            $managers = TeamMember::select('id', 'name')->where('role_id', $manager_role->id)->get();
        } else {
            $managers = [];
        }

        if ($id > 0) {
            $page_heading = "Edit Member";
            $member = TeamMember::find($id);
        } else {
            $page_heading = "New Member";
            $member = json_decode(json_encode(['id' => 0, 'role_id' => '', 'name' => '', 'email' => '', 'mobile' => '', 'venue_name' => '', 'status' => '', 'nvrm_id' => '']));
        }
        $getRm = TeamMember::select('id', 'name')->where('venue_name', 'RM >< Non Venue')->get();
        return view('admin.venueCrm.team.manage', compact('page_heading', 'roles', 'managers', 'member', 'getRm', 'venues'));
    }

    public function manage_process($id = 0, Request $request) {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'profile_image' => 'mimes:jpg,jpeg,png,webp,gif|max:1024',
            'email' => "required|email",
            'mobile_number' => "required|digits:10",
            'role' => 'required|integer|exists:roles,id',
            'status' => 'required|boolean',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($id > 0) {
            $member = TeamMember::find($id);
            $msg = "Member updated successfully.";
        }

        if ($id == 0) {
            $member = TeamMember::where('mobile', $request->mobile_number)->withTrashed()->first();
            if ($member) {
                if ($member->deleted_at != null) {
                    $member->deleted_at = null;
                    $msg = "This user is already exist in our records, We just have updated it's profile info.";
                } else {
                    session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Mobile number is already exist.']);
                    return redirect()->back();
                }
            } else {
                $member = new TeamMember();
                $msg = "Member added successfully.";
            }
        }

        if (is_file($request->profile_image)) {
            $file = $request->file('profile_image');
            $ext = $file->getClientOriginalExtension();

            $sub_str =  substr($request->name, 0, 5);
            $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_profile" . date('dmyHis') . "." . $ext;
            $path = "memberProfileImages/$file_name";

            Storage::put("public/" . $path, file_get_contents($file));
            $profile_image = asset("storage/" . $path);
            $member->profile_image = $profile_image;
        }

        $member->parent_id = $request->manager;
        $member->role_id = $request->role;
        $member->name = $request->name;
        $member->email = $request->email;
        $member->mobile = $request->mobile_number;
        $member->venue_name = $request->venue_name;
        $member->venue_id = $request->venue_id;
        $member->status = $request->status;
        $member->nvrm_id = $request->nvrm_id;
        $member->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->route('admin.team.list');
    }

    public function update_status($member_id, $status) {
        $member = TeamMember::find($member_id);
        if (!$member) {
            return abort(404);
        }

        $member->status = $status;
        $member->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Status updated."]);
        return redirect()->back();
    }

    public function update_is_active($member_id, $status) {
        $member = TeamMember::find($member_id);
        if (!$member) {
            return abort(404);
        }

        $member->is_active = $status;
        $member->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Status updated."]);
        return redirect()->back();
    }

    public function delete($member_id) {
        $member = TeamMember::find($member_id);
        if (!$member) {
            return abort(404);
        }

        $member->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Member deleted."]);
        return redirect()->route('admin.team.list');
    }

    public function view($member_id) {
        $member = TeamMember::find($member_id);
        return view('admin.venueCrm.team.view', compact('member'));
    }

    public function update_profile_image($member_id, Request $request) {
        $validate = Validator::make($request->all(), [
            'profile_image' => 'mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $member = TeamMember::find($member_id);
        if (!$member) {
            abort(404);
        }

        if (is_file($request->profile_image)) {
            $file = $request->file('profile_image');
            $ext = $file->getClientOriginalExtension();

            $sub_str =  substr($member->name, 0, 5);
            $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_profile" . date('dmyHis') . "." . $ext;
            $path = "memberProfileImages/$file_name";
            Storage::put("public/" . $path, file_get_contents($file));
            $profile_image = asset("storage/" . $path);

            $member->profile_image = $profile_image;
            $member->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Image updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong, please contact to administrator.']);
        }

        return redirect()->back();
    }

    public function get_member_login_info() {
        $infos = LoginInfo::select(
            'login_infos.user_id',
            'login_infos.request_otp_at',
            'login_infos.request_otp_count',
            'login_infos.login_at',
            'login_infos.login_count',
            'login_infos.logout_at',
            'login_infos.ip_address',
            'login_infos.browser',
            'login_infos.platform',
            'login_infos.status',
            'tm.name as team_name',
            'roles.name as role_name',
            'roles.id as role_id',
        )->join('team_members as tm', 'tm.id', 'login_infos.user_id')
            ->join('roles', 'roles.id', 'tm.role_id')
            ->where(['login_type' => 'team', 'tm.deleted_at' => null])->whereNot('roles.id', 1)->orderBy('tm.name', 'asc')->get();

        $rm = [];
        $nvrm = [];
        $manager_and_vm = [];
        foreach ($infos as $list) {
            if ($list->role_id == 3) {
                array_push($rm, $list);
            } elseif ($list->role_id == 4) {
                array_push($nvrm, $list);
            } elseif ($list->role_id == 2) {
                array_push($manager_and_vm, $list);
                $vm_members = DB::table('login_infos')->select(
                    'login_infos.user_id',
                    'login_infos.request_otp_at',
                    'login_infos.request_otp_count',
                    'login_infos.login_at',
                    'login_infos.login_count',
                    'login_infos.logout_at',
                    'login_infos.ip_address',
                    'login_infos.browser',
                    'login_infos.platform',
                    'login_infos.status',
                    'tm.name as team_name',
                    'roles.name as role_name',
                    'roles.id as role_id',
                )->join('team_members as tm', 'tm.id', 'login_infos.user_id')
                    ->join('roles', 'roles.id', 'tm.role_id')
                    ->where(['tm.role_id' => 5, 'tm.parent_id' => $list->user_id, 'login_infos.login_type' => 'team', 'tm.deleted_at' => null])->get();
                foreach ($vm_members as $member) {
                    array_push($manager_and_vm, $member);
                }
            }
        }
        $login_infos = array_merge($rm, $nvrm, $manager_and_vm);
        return view('admin.venueCrm.team.login_info', compact('login_infos'));
    }
}
