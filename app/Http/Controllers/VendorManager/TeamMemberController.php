<?php

namespace App\Http\Controllers\VendorManager;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class TeamMemberController extends Controller {
    public function list() {
        $page_heading = "My Team";
        return view('vendormanager.vendorCrm.team.list', compact('page_heading'));
    }

    public function ajax_list() {
        $auth_user = Auth::guard('vendormanager')->user();
        $members = Vendor::select(
            'vendors.id',
            'vendors.profile_image',
            'vendors.name',
            'vendors.business_name',
            'vendors.mobile',
            'vendors.email',
            'vendors.status',
            'vendors.created_at',
        )->where('vendors.parent_id', $auth_user->id)->get();
        return datatables($members)->toJson();
    }
}

