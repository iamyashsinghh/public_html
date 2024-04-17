<?php

namespace App\Http\Controllers\Bdm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BdmLead;
use App\Models\BdmMeeting;
use App\Models\BdmTask;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class DashboardController extends Controller
{
    public function index(){
        $auth_user = Auth::guard('bdm')->user();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');
        $from = Carbon::today()->startOfMonth();
        $to = Carbon::today()->endOfMonth();
        $currentDateTime = Carbon::today();
        $currentDateStart = Carbon::now()->startOfDay();
        $currentDateEnd = Carbon::now()->endOfDay();
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $bdm_unfollowed_leads = BdmLead::query()
        ->where('lead_status', '!=', 'Done')
        ->whereNull('deleted_at')
        ->whereExists(function ($query) use ($auth_user) {
            $query->select(DB::raw(1))
                  ->from('bdm_tasks')
                  ->whereColumn('bdm_tasks.lead_id', 'bdm_leads.lead_id')
                  ->whereNotNull('bdm_tasks.done_datetime')
                  ->whereNull('bdm_tasks.deleted_at')
                  ->where('bdm_tasks.created_by', $auth_user->id);
        })
        ->whereDoesntHave('get_tasks', function ($query) {
            $query->whereNull('done_datetime');
        })
        ->distinct('lead_id')
        ->count();

        $bdm_task_overdue_leads = BdmLead::join('bdm_tasks', 'bdm_leads.lead_id', '=', 'bdm_tasks.lead_id')
            ->where('bdm_leads.lead_status', '!=', 'Done')
            ->where('bdm_tasks.task_schedule_datetime', '<', $currentDateTime)
            ->whereNull('bdm_tasks.done_datetime')
            ->whereNull('bdm_leads.deleted_at')
            ->whereNull('bdm_tasks.deleted_at')
            ->where('bdm_tasks.created_by', $auth_user->id)
            ->count();
        $bdm_today_task_leads = BdmLead::join('bdm_tasks', 'bdm_leads.lead_id', '=', 'bdm_tasks.lead_id')
            ->where('bdm_leads.lead_status', '!=', 'Done')
            ->whereBetween('bdm_tasks.task_schedule_datetime', [$currentDateStart, $currentDateEnd])
            ->whereNull('bdm_tasks.done_datetime')
            ->whereNull('bdm_leads.deleted_at')
            ->whereNull('bdm_tasks.deleted_at')
            ->where('bdm_tasks.created_by', $auth_user->id)
            ->count();
        $bdm_month_task_leads = BdmLead::join('bdm_tasks', 'bdm_leads.lead_id', '=', 'bdm_tasks.lead_id')
            ->whereBetween('bdm_tasks.task_schedule_datetime', [$currentMonthStart, $currentMonthEnd])
            ->whereNull('bdm_leads.deleted_at')
            ->whereNull('bdm_tasks.deleted_at')
            ->where('bdm_tasks.created_by', $auth_user->id)
            ->count();

        $bdm_meeting_overdue_leads = BdmLead::join('bdm_meetings', 'bdm_leads.lead_id', '=', 'bdm_meetings.lead_id')
            ->where('bdm_leads.lead_status', '!=', 'Done')
            ->where('bdm_meetings.meeting_schedule_datetime', '<', $currentDateTime)
            ->whereNull('bdm_meetings.done_datetime')
            ->whereNull('bdm_leads.deleted_at')
            ->whereNull('bdm_meetings.deleted_at')
            ->where('bdm_meetings.created_by', $auth_user->id)
            ->count();
        $bdm_today_meeting_leads = BdmLead::join('bdm_meetings', 'bdm_leads.lead_id', '=', 'bdm_meetings.lead_id')
            ->where('bdm_leads.lead_status', '!=', 'Done')
            ->whereBetween('bdm_meetings.meeting_schedule_datetime', [$currentDateStart, $currentDateEnd])
            ->whereNull('bdm_meetings.done_datetime')
            ->whereNull('bdm_leads.deleted_at')
            ->whereNull('bdm_meetings.deleted_at')
            ->where('bdm_meetings.created_by', $auth_user->id)
            ->count();
        $bdm_month_meeting_leads = BdmLead::join('bdm_meetings', 'bdm_leads.lead_id', '=', 'bdm_meetings.lead_id')
            ->whereBetween('bdm_meetings.meeting_schedule_datetime', [$currentMonthStart, $currentMonthEnd])
            ->whereNull('bdm_leads.deleted_at')
            ->whereNull('bdm_meetings.deleted_at')
            ->where('bdm_meetings.created_by', $auth_user->id)
            ->count();

        $total_leads_received_this_month = BdmLead::where('lead_datetime', 'like', "%$current_month%")->where('assign_id', $auth_user->id)->count();
        $total_leads_received_today = BdmLead::where('lead_datetime', 'like', "%$current_date%")->where('assign_id', $auth_user->id)->count();
        $unread_leads_this_month = BdmLead::where('lead_datetime', 'like', "%$current_month%")->where('read_status', false)->where('assign_id', $auth_user->id)->count();
        $unread_leads_today = BdmLead::where('lead_datetime', 'like', "%$current_date%")->where('read_status', false)->where('assign_id', $auth_user->id)->count();
        $total_unread_leads_overdue = BdmLead::where('read_status', false)->where('assign_id', $auth_user->id)->count();


        return view("bdm.dashboard", compact('bdm_unfollowed_leads',
        'bdm_task_overdue_leads',
        'bdm_today_task_leads',
        'bdm_month_task_leads',
        'bdm_meeting_overdue_leads',
        'bdm_today_meeting_leads',
        'bdm_month_meeting_leads',
        'total_leads_received_this_month',
        'total_leads_received_today',
        'unread_leads_this_month',
        'unread_leads_today',
        'total_unread_leads_overdue'
    ));
    }
    public function update_profile_image(Request $request)
    {
        $auth_user = Auth::guard('bdm')->user();

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

            $sub_str = substr($member->name, 0, 5);
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
