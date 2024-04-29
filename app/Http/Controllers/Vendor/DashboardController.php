<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\LeadForwardInfo;
use App\Models\nvLead;
use App\Models\nvLeadForward;
use App\Models\nvMeeting;
use App\Models\nvTask;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Vendor;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller {
    public function index() {
        $auth_user = Auth::guard('vendor')->user();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');

        $total_leads = nvLeadForward::where('forward_to', $auth_user->id)->count();
        $leads_of_the_month = nvLeadForward::where('lead_datetime', 'like', "%$current_month%")->where('forward_to', $auth_user->id)->count();
        $leads_of_the_day = nvLeadForward::where('lead_datetime', 'like', "%$current_date%")->where('forward_to', $auth_user->id)->count();
        $unreaded_leads = nvLeadForward::where(['forward_to' => $auth_user->id, 'read_status' => false])->count();

        $schedule_tasks = nvTask::where(['created_by' => $auth_user->id, 'done_datetime' => null])->count();
        $schedule_meetings = nvMeeting::where(['created_by' => $auth_user->id, 'done_datetime' => null])->count();
        
        $leads_booked = nvLeadForward::where('forward_to', $auth_user->id)->where('lead_status', 'Booked')->count();
        return view('vendor.dashboard', compact('total_leads', 'leads_of_the_month', 'leads_of_the_day', 'unreaded_leads', 'schedule_tasks', 'schedule_meetings', 'leads_booked'));
    }

    public function update_profile_image(Request $request) {
        $auth_user = Auth::guard('vendor')->user();

        $validate = Validator::make($request->all(), [
            'profile_image' => 'mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $vendor = Vendor::find($auth_user->id);
        if (!$vendor) {
            abort(404);
        }

        if (is_file($request->profile_image)) {
            $file = $request->file('profile_image');
            $ext = $file->getClientOriginalExtension();

            $sub_str =  substr($vendor->name, 0, 5);
            $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_profile" . date('dmyHis') . "." . $ext;
            $path = "vendorProfileImages/$file_name";
            Storage::put("public/" . $path, file_get_contents($file));
            $profile_image = asset("storage/" . $path);

            $vendor->profile_image = $profile_image;
            $vendor->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Image updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong, please contact to administrator.']);
        }

        return redirect()->back();
    }
}
