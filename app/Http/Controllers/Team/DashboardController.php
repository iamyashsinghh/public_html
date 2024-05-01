<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\LeadForwardInfo;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        $auth_user = Auth::guard('team')->user();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');

        $from = Carbon::today()->startOfMonth();
        $to = Carbon::today()->endOfMonth();

        $currentDateTime = Carbon::today();
        $currentDateStart = Carbon::now()->startOfDay();
        $currentDateEnd = Carbon::now()->endOfDay();
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $rm_unfollowed_leads = Lead::query()
        ->where('lead_status', '!=', 'Done')
        ->whereNull('deleted_at')
        ->whereExists(function ($query) use ($auth_user) {
            $query->select(DB::raw(1))
                  ->from('tasks')
                  ->whereColumn('tasks.lead_id', 'leads.lead_id')
                  ->whereNotNull('tasks.done_datetime')
                  ->whereNull('tasks.deleted_at')
                  ->where('tasks.created_by', $auth_user->id);
        })
        ->whereDoesntHave('get_tasks', function ($query) {
            $query->whereNull('done_datetime');
        })
        ->distinct('lead_id')
        ->count();
        $rm_task_overdue_leads = Lead::join('tasks', 'leads.lead_id', '=', 'tasks.lead_id')
            ->where('leads.lead_status', '!=', 'Done')
            ->where('tasks.task_schedule_datetime', '<', $currentDateTime)
            ->whereNull('tasks.done_datetime')
            ->whereNull('leads.deleted_at')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.created_by', $auth_user->id)
            ->count();
        $rm_today_task_leads = Lead::join('tasks', 'leads.lead_id', '=', 'tasks.lead_id')
            ->where('leads.lead_status', '!=', 'Done')
            ->whereBetween('tasks.task_schedule_datetime', [$currentDateStart, $currentDateEnd])
            ->whereNull('tasks.done_datetime')
            ->whereNull('leads.deleted_at')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.created_by', $auth_user->id)
            ->count();
        $rm_month_task_leads = Lead::join('tasks', 'leads.lead_id', '=', 'tasks.lead_id')
            ->whereBetween('tasks.task_schedule_datetime', [$currentMonthStart, $currentMonthEnd])
            ->whereNull('leads.deleted_at')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.created_by', $auth_user->id)
            ->count();
        if ($auth_user->role_id == 5) {
            $total_leads_received_this_month = LeadForward::where('lead_datetime', 'like', "%$current_month%")->where('forward_to', $auth_user->id)->count();
            $total_leads_received_today = LeadForward::where('lead_datetime', 'like', "%$current_date%")->where('forward_to', $auth_user->id)->count();
            $unread_leads_this_month = LeadForward::where('lead_datetime', 'like', "%$current_month%")->where(['forward_to' => $auth_user->id, 'read_status' => false])->count();
            $unread_leads_today = LeadForward::where('lead_datetime', 'like', "%$current_date%")->where(['forward_to' => $auth_user->id, 'read_status' => false])->count();
            $total_unread_leads_overdue = LeadForward::where('read_status', false)->where('lead_datetime', '<', Carbon::today())->where('forward_to' , $auth_user->id)->count();

            $task_schedule_this_month = Task::selectRaw('count(distinct(lead_id)) as count')->where('task_schedule_datetime', 'like', "%$current_month%")->where(['created_by' => $auth_user->id, 'done_datetime' => null])->first()->count;
            $task_schedule_today = Task::selectRaw('count(distinct(lead_id)) as count')->where('task_schedule_datetime', 'like', "%$current_date%")->where(['created_by' => $auth_user->id, 'done_datetime' => null])->first()->count;
            $total_task_overdue = Task::selectRaw('count(distinct(lead_id)) as count')->where('task_schedule_datetime', '<', Carbon::today())->where(['created_by' => $auth_user->id, 'done_datetime' => null])->first()->count;

            // $recce_schedule_this_month = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $auth_user->id, 'lead_forwards.source' => 'WB|Team', 'visits.done_datetime' => null, 'visits.deleted_at' => null])->whereBetween('visits.visit_schedule_datetime', [$from, $to])->count();
            $recce_schedule_this_month = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $auth_user->id, 'lead_forwards.source' => 'WB|Team', 'visits.done_datetime' => null, 'visits.deleted_at' => null])->whereBetween('visits.visit_schedule_datetime', [$from, $to])->count();

            $recce_schedule_today = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $auth_user->id, 'lead_forwards.source' => 'WB|Team', 'visits.done_datetime' => null, 'visits.deleted_at' => null])->where('visits.visit_schedule_datetime', 'like', "%$current_date%")->count();
            $recce_done_this_month = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $auth_user->id, 'lead_forwards.source' => 'WB|Team', 'visits.deleted_at' => null])->whereBetween('visits.done_datetime', [$from, $to])->count();
            $total_recce_overdue = Visit::selectRaw('count(distinct(lead_id)) as count')->where('visit_schedule_datetime', '<', Carbon::today())->where(['created_by' => $auth_user->id, 'done_datetime' => null])->first()->count;
            // $recce_schedule_this_month = Visit::selectRaw('count(distinct(lead_id)) as count')->where('visit_schedule_datetime', 'like', "%$current_month%")->where(['created_by' => $auth_user->id, 'done_datetime' => null])->first()->count;
            // $recce_schedule_today = Visit::selectRaw('count(distinct(lead_id)) as count')->where('visit_schedule_datetime', 'like', "%$current_date%")->where(['created_by' => $auth_user->id, 'done_datetime' => null])->first()->count;
            // $total_recce_overdue = Visit::selectRaw('count(distinct(lead_id)) as count')->where('visit_schedule_datetime', '<', Carbon::today())->where(['created_by' => $auth_user->id, 'done_datetime' => null])->first()->count;
            // $recce_done_this_month = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $auth_user->id])->whereBetween('visits.done_datetime', [$from, $to])->count();

            $bookings_this_month = LeadForward::join('bookings', 'bookings.id', 'lead_forwards.booking_id')->where(['lead_forwards.forward_to' => $auth_user->id, 'lead_forwards.source' => 'WB|Team', 'bookings.deleted_at' => null])->whereBetween('bookings.created_at', [$from, $to])->count();

            $l = (int) $total_leads_received_this_month;
            $r = (int) $recce_done_this_month;
            if ($r > 0 && $l > 0) {
                $l2r = ($r * 100) / $l;
            } else {
                $l2r = 0;
            }
            $l2r = number_format($l2r, 1);

            $r = (int) $recce_done_this_month;
            $b = (int) $bookings_this_month;
            if ($b > 0 && $r > 0) {
                $r2c = ($b * 100) / $r;
            } else {
                $r2c = 0;
            }
            $r2c = number_format($r2c, 1);

            $unfollowed_leads = LeadForward::join('tasks', ['tasks.id' => 'lead_forwards.task_id'])->where(['lead_forwards.forward_to' => $auth_user->id, 'tasks.deleted_at' => null])->where('lead_forwards.lead_status', '!=', 'Done')->whereNotNull('tasks.done_datetime')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('bookings')
                        ->whereRaw('bookings.id = lead_forwards.booking_id');
                })->get()->count();

            $forward_leads_this_month = $forward_leads_today = 0;
        } else {
            $total_leads_received_this_month = Lead::where('lead_datetime', 'like', "%$current_month%")->where('assign_id', $auth_user->id)->count();
            $total_leads_received_today = Lead::where('lead_datetime', 'like', "%$current_date%")->where('assign_id', $auth_user->id)->count();
            $unread_leads_this_month = Lead::where('lead_datetime', 'like', "%$current_month%")->where('read_status', false)->where('assign_id', $auth_user->id)->count();
            $unread_leads_today = Lead::where('lead_datetime', 'like', "%$current_date%")->where('read_status', false)->where('assign_id', $auth_user->id)->count();
            $total_unread_leads_overdue = Lead::where('lead_datetime', '<', Carbon::today())->where('read_status', false)->where('assign_id', $auth_user->id)->count();

            $forward_leads_this_month = LeadForwardInfo::whereBetween('updated_at', [$from, $to])->where('forward_from', $auth_user->id)->groupBy('lead_id')->get()->count();
            $forward_leads_today = LeadForwardInfo::where('updated_at', 'like', "%$current_date%")->where('forward_from', $auth_user->id)->groupBy('lead_id')->get()->count();

            $task_schedule_this_month = $task_schedule_today = $total_task_overdue = $recce_schedule_this_month = $recce_schedule_today = $total_recce_overdue = $unfollowed_leads = $recce_done_this_month = $l2r = $bookings_this_month = $r2c = 0;
        }

        $response_data = compact(
            'total_leads_received_this_month',
            'total_leads_received_today',
            'unread_leads_this_month',
            'unread_leads_today',
            'total_unread_leads_overdue',
            'forward_leads_this_month',
            'forward_leads_today',
            'task_schedule_this_month',
            'task_schedule_today',
            'total_task_overdue',
            'recce_schedule_this_month',
            'recce_schedule_today',
            'total_recce_overdue',
            'unfollowed_leads',
            'recce_done_this_month',
            'l2r',
            'rm_unfollowed_leads',
            'rm_month_task_leads',
            'rm_today_task_leads',
            'rm_task_overdue_leads',
            'bookings_this_month',
            'r2c'
        );

        return view('team.dashboard', $response_data);
    }

    public function update_profile_image(Request $request)
    {
        $auth_user = Auth::guard('team')->user();

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
