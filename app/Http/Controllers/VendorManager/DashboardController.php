<?php

namespace App\Http\Controllers\VendorManager;

use App\Http\Controllers\Controller;
use App\Models\nvLeadForward;
use App\Models\nvMeeting;
use App\Models\nvTask;
use App\Models\PVendorLead;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller {
    public function index() {
        $auth_user = Auth::guard('vendormanager')->user();

        $v_members = Vendor::select('id', 'name', 'business_name', 'start_date', 'end_date')->where(['parent_id' => $auth_user->id, 'status' => 1])->get();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');

        $from =  Carbon::today()->startOfMonth();
        $to =  Carbon::today()->endOfMonth();

        foreach ($v_members as $v) {
            $v['total_leads_received'] = nvLeadForward::where(['forward_to' => $v->id])->count();
            $v['leads_received_this_month'] = nvLeadForward::where(['forward_to' => $v->id])->where('lead_datetime', 'like', "%$current_month%")->count();
            $v['leads_received_today'] = nvLeadForward::where(['forward_to' => $v->id])->where('lead_datetime', 'like', "%$current_date%")->count();
            $v['unread_leads_this_month'] = nvLeadForward::where(['forward_to' => $v->id, 'read_status' => false])->where('lead_datetime', 'like', "%$current_month%")->count();
            $v['unread_leads_today'] = nvLeadForward::where(['forward_to' => $v->id, 'read_status' => false])->where('lead_datetime', 'like', "%$current_date%")->count();
            $v['unread_leads_overdue'] = nvLeadForward::where(['forward_to' => $v->id, 'read_status' => false])->where('lead_datetime',  '<', Carbon::today())->count();

            $v['task_schedule_this_month'] = nvTask::where(['created_by' => $v->id, 'done_datetime' => null])->where('task_schedule_datetime', 'like', "%$current_month%")->count();
            $v['task_schedule_today'] = nvTask::where(['created_by' => $v->id, 'done_datetime' => null])->where('task_schedule_datetime', 'like', "%$current_date%")->count();
            $v['task_overdue'] = nvTask::where(['created_by' => $v->id, 'done_datetime' => null])->where('task_schedule_datetime', '<', Carbon::today())->count();

            $v['meeting_schedule_this_month'] = nvMeeting::where(['created_by' => $v->id, 'done_datetime' => null])->where('meeting_schedule_datetime', 'like', "%$current_month%")->count();
            $v['meeting_schedule_today'] = nvMeeting::where(['created_by' => $v->id, 'done_datetime' => null])->where('meeting_schedule_datetime', 'like', "%$current_date%")->count();
            $v['meeting_overdue'] = nvMeeting::where(['created_by' => $v->id, 'done_datetime' => null])->where('meeting_schedule_datetime', '<', Carbon::today())->count();
            $v['created_lead'] = PVendorLead::where('created_by', $v->id)->count();
            if (isset($v->start_date) && isset($v->end_date)) {
                $v['time_period_lead'] = nvLeadForward::where('forward_to', $v->id)
                                                      ->whereBetween('lead_datetime', [new Carbon($v->start_date), new Carbon($v->end_date)])
                                                      ->count();
            } else {
                $v['time_period_lead'] = 0;
            }        }

        $vs_id = [];
        foreach ($v_members as $list) {
            array_push($vs_id, $list->id);
        }

        $total_leads_received = nvLeadForward::whereIn('forward_to', $vs_id)->count();
        return view('vendormanager.dashboard', compact('v_members', 'total_leads_received'));
    }

    public function update_profile_image(Request $request) {
        $auth_user = Auth::guard('vendormanager')->user();

        $validate = Validator::make($request->all(), [
            'profile_image' => 'mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $member = TeamMember::find($auth_user->id);
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
}
