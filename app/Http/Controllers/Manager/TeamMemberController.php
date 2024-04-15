<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth;

class TeamMemberController extends Controller {
    public function list() {
        $page_heading = "My Team";
        return view('manager.venueCrm.team.list', compact('page_heading'));
    }

    public function ajax_list() {
        $auth_user = Auth::guard('manager')->user();
        $members = TeamMember::select(
            'team_members.id',
            'team_members.profile_image',
            'team_members.name',
            'team_members.mobile',
            'team_members.email',
            'team_members.venue_name',
            'r.name as role_name',
            'team_members.status',
            'team_members.created_at',
        )->leftJoin("roles as r", 'team_members.role_id', '=', 'r.id')->where('team_members.role_id', '!=', 1)->where('parent_id', $auth_user->id)->get();
        return datatables($members)->toJson();
    }
}
