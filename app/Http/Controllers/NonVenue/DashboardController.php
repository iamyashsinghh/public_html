<?php

namespace App\Http\Controllers\NonVenue;

use App\Http\Controllers\Controller;
use App\Models\nvLeadForwardInfo;
use App\Models\nvNote;
use App\Models\nvrmLeadForward;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        $auth_user = Auth::guard('nonvenue')->user();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');

        $total_leads_received_this_month = nvrmLeadForward::where('lead_datetime', 'like', "%$current_month%")->whereNull('deleted_at')->where('forward_to', $auth_user->id)->distinct('lead_id')->count();
        $total_leads_received_today = nvrmLeadForward::where('lead_datetime', 'like', "%$current_date%")->whereNull('deleted_at')->where('forward_to', $auth_user->id)->distinct('lead_id')->count();
        $unread_leads_this_month = nvrmLeadForward::where('lead_datetime', 'like', "%$current_month%")->whereNull('deleted_at')->where(['read_status' => false])->where('forward_to', $auth_user->id)->distinct('lead_id')->count();
        $unread_leads_today = nvrmLeadForward::where('lead_datetime', 'like', "%$current_date%")->where(['read_status' => false])->whereNull('deleted_at')->where('forward_to', $auth_user->id)->distinct('lead_id')->count();
        $total_unread_leads_overdue = nvrmLeadForward::where('lead_datetime', '>=', Carbon::parse('2024-02-01')->startOfDay())
        ->where('lead_datetime', '<=', Carbon::now())
        ->where('read_status', false)
        ->whereNull('deleted_at')
        ->where('forward_to', $auth_user->id)
        ->distinct('lead_id')
        ->count('lead_id');
        $forward_leads_this_month = nvLeadForwardInfo::join('nvrm_lead_forwards',  'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')->where('nv_lead_forward_infos.updated_at', 'like', "%$current_month%")->whereNull('nvrm_lead_forwards.deleted_at')->where(['nv_lead_forward_infos.forward_from' => $auth_user->id])->groupBy('nv_lead_forward_infos.lead_id')->get()->count();
        $forward_leads_today = nvLeadForwardInfo::join('nvrm_lead_forwards',  'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')->where('nv_lead_forward_infos.updated_at', 'like', "%$current_date%")->whereNull('nvrm_lead_forwards.deleted_at')->where(['nv_lead_forward_infos.forward_from' => $auth_user->id])->groupBy('nv_lead_forward_infos.lead_id')->get()->count();
        // $total_leads_received_this_month = nvrmLeadForward::where('lead_datetime', 'like', "%$current_month%")->where('forward_to', $auth_user->id)->count();
        // $total_leads_received_today = nvrmLeadForward::where('lead_datetime', 'like', "%$current_date%")->where('forward_to', $auth_user->id)->count();
        // $unread_leads_this_month = nvrmLeadForward::where('lead_datetime', 'like', "%$current_month%")->where(['forward_to' => $auth_user->id, 'read_status' => false])->count();
        // $unread_leads_today = nvrmLeadForward::where('lead_datetime', 'like', "%$current_date%")->where(['forward_to' => $auth_user->id, 'read_status' => false])->count();
        // $total_unread_leads_overdue = nvrmLeadForward::where('lead_datetime', '<', Carbon::today())->where(['forward_to' => $auth_user->id, 'read_status' => false])->count();
        // $forward_leads_this_month = nvLeadForwardInfo::where('updated_at', 'like', "%$current_month%")->where(['forward_from' => $auth_user->id])->groupBy('lead_id')->get()->count();
        // $forward_leads_today = nvLeadForwardInfo::where('updated_at', 'like', "%$current_date%")->where(['forward_from' => $auth_user->id])->groupBy('lead_id')->get()->count();

        $currentDateTime = Carbon::today();
        $currentDateStart = Carbon::now()->startOfDay();
        $currentDateEnd = Carbon::now()->endOfDay();
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $nvrm_unfollowed_leads = nvrmLeadForward::query()
        ->where('lead_status', '!=', 'Done')
        ->whereNull('deleted_at')
        ->whereExists(function ($query) use ($auth_user) {
            $query->select(DB::raw(1))
                  ->from('nvrm_tasks')
                  ->whereColumn('nvrm_tasks.lead_id', 'nvrm_lead_forwards.lead_id')
                  ->whereNotNull('nvrm_tasks.done_datetime')
                  ->whereNull('nvrm_tasks.deleted_at')
                  ->where('nvrm_tasks.created_by', $auth_user->id);
        })
        ->whereDoesntHave('nvrm_tasks', function ($query) {
            $query->whereNull('done_datetime');
        })
        ->distinct('lead_id')
        ->count();
        $nvrm_task_overdue_leads = nvrmLeadForward::join('nvrm_tasks', 'nvrm_lead_forwards.lead_id', '=', 'nvrm_tasks.lead_id')
            ->where('nvrm_lead_forwards.lead_status', '!=', 'Done')
            ->where('nvrm_tasks.task_schedule_datetime', '<', $currentDateTime)
            ->whereNull('nvrm_tasks.done_datetime')
            ->whereNull('nvrm_lead_forwards.deleted_at')
            ->whereNull('nvrm_tasks.deleted_at')
            ->where('nvrm_tasks.created_by', $auth_user->id)
            ->count();
        $nvrm_today_task_leads = nvrmLeadForward::join('nvrm_tasks', 'nvrm_lead_forwards.lead_id', '=', 'nvrm_tasks.lead_id')
            ->where('nvrm_lead_forwards.lead_status', '!=', 'Done')
            ->whereBetween('nvrm_tasks.task_schedule_datetime', [$currentDateStart, $currentDateEnd])
            ->whereNull('nvrm_tasks.done_datetime')
            ->whereNull('nvrm_lead_forwards.deleted_at')
            ->whereNull('nvrm_tasks.deleted_at')
            ->where('nvrm_tasks.created_by', $auth_user->id)
            ->count();
        $nvrm_month_task_leads = nvrmLeadForward::join('nvrm_tasks', 'nvrm_lead_forwards.lead_id', '=', 'nvrm_tasks.lead_id')
            ->whereBetween('nvrm_tasks.task_schedule_datetime', [$currentMonthStart, $currentMonthEnd])
            ->whereNull('nvrm_lead_forwards.deleted_at')
            ->whereNull('nvrm_tasks.deleted_at')
            ->where('nvrm_tasks.created_by', $auth_user->id)
            ->count();

        // vendor help counts
        $vendor_all_help = nvNote::count();
        $vendor_non_responsed_help = nvNote::whereNull('done_by')->count();
        $vendor_total_helped = nvNote::where('done_by', $auth_user->id)->count();

        $response_data = compact(
            'total_leads_received_this_month',
            'total_leads_received_today',
            'unread_leads_this_month',
            'unread_leads_today',
            'total_unread_leads_overdue',
            'forward_leads_this_month',
            'forward_leads_today',
            'nvrm_month_task_leads',
            'nvrm_today_task_leads',
            'nvrm_task_overdue_leads',
            'nvrm_unfollowed_leads',
            'vendor_all_help',
            'vendor_non_responsed_help',
            'vendor_total_helped'
        );
        return view('nonvenue.dashboard', $response_data);
    }

    public function update_profile_image(Request $request)
    {
        $auth_user = Auth::guard('nonvenue')->user();

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

            //update profile link in database;
            $member->profile_image = $profile_image;
            $member->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Image updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong, please contact to administrator.']);
        }

        return redirect()->back();
    }
}
