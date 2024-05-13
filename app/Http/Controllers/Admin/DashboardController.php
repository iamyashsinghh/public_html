<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\nvLead;
use App\Models\nvLeadForward;
use App\Models\nvMeeting;
use App\Models\nvTask;
use App\Models\PVendorLead;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Vendor;
use App\Models\Visit;
use App\Models\VmProductivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        $yearly_calendar = [];
        for ($i = 12; $i >= 0; $i--) {
            $date = date('Y-M', strtotime("-$i month"));
            array_push($yearly_calendar, $date);
        }
        $yearly_calendar = implode(",", $yearly_calendar);

        $total_vendors = Vendor::count();
        $total_team = TeamMember::whereNot('role_id', 1)->count();
        $total_venue_leads = Lead::count();
        $total_nv_leads = nvLead::count();

        $venue_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->count();
            array_push($venue_leads_for_this_month, $count);
        }
        $venue_leads_for_this_month = implode(",", $venue_leads_for_this_month);

        $venue_form_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Form')->count();
            array_push($venue_form_leads_for_this_month, $count);
        }
        $venue_form_leads_for_this_month = implode(",", $venue_form_leads_for_this_month);

        $venue_whatsapp_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|WhatsApp')->count();
            array_push($venue_whatsapp_leads_for_this_month, $count);
        }
        $venue_whatsapp_leads_for_this_month = implode(",", $venue_whatsapp_leads_for_this_month);

        $venue_ads_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '1')->count();
            array_push($venue_ads_leads_for_this_month, $count);
        }
        $venue_ads_leads_for_this_month = implode(",", $venue_ads_leads_for_this_month);

        $venue_organic_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '0')->count();
            array_push($venue_organic_leads_for_this_month, $count);
        }
        $venue_organic_leads_for_this_month = implode(",", $venue_organic_leads_for_this_month);

        $venue_call_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Call')->count();
            array_push($venue_call_leads_for_this_month, $count);
        }
        $venue_call_leads_for_this_month = implode(",", $venue_call_leads_for_this_month);

        $venue_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->count();
            array_push($venue_leads_for_this_year, $count);
        }
        $venue_leads_for_this_year = implode(",", $venue_leads_for_this_year);

        $venue_call_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Call')->count();
            array_push($venue_call_leads_for_this_year, $count);
        }
        $venue_call_leads_for_this_year = implode(",", $venue_call_leads_for_this_year);

        $venue_organic_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '0')->count();
            array_push($venue_organic_leads_for_this_year, $count);
        }
        $venue_organic_leads_for_this_year = implode(",", $venue_organic_leads_for_this_year);

        $venue_ads_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '1')->count();
            array_push($venue_ads_leads_for_this_year, $count);
        }
        $venue_ads_leads_for_this_year = implode(",", $venue_ads_leads_for_this_year);

        $venue_whatsapp_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|WhatsApp')->count();
            array_push($venue_whatsapp_leads_for_this_year, $count);
        }
        $venue_whatsapp_leads_for_this_year = implode(",", $venue_whatsapp_leads_for_this_year);

        $venue_form_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Form')->count();
            array_push($venue_form_leads_for_this_year, $count);
        }
        $venue_form_leads_for_this_year = implode(",", $venue_form_leads_for_this_year);

        $nv_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = nvLead::where('lead_datetime', 'like', "%$datetime%")->count();
            array_push($nv_leads_for_this_month, $count);
        }
        $nv_leads_for_this_month = implode(",", $nv_leads_for_this_month);

        $nv_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = nvLead::where('lead_datetime', 'like', "%$datetime%")->count();
            array_push($nv_leads_for_this_year, $count);
        }
        $nv_leads_for_this_year = implode(",", $nv_leads_for_this_year);

        $vm_members = TeamMember::select('id', 'parent_id', 'name', 'venue_name')->where(['role_id' => 5, 'status' => 1])->orderBy('parent_id')->orderBy('venue_name')->get();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');

        $from =  Carbon::today()->startOfMonth();
        $to =  Carbon::today()->endOfMonth();
        foreach ($vm_members as $vm) {
            $vm['leads_received_this_month'] = LeadForward::where(['forward_to' => $vm->id])->where('lead_datetime', 'like', "%$current_month%")->count();
            $vm['leads_received_today'] = LeadForward::where(['forward_to' => $vm->id])->where('lead_datetime', 'like', "%$current_date%")->count();
            $vm['unread_leads_this_month'] = LeadForward::where(['forward_to' => $vm->id, 'read_status' => false])->where('lead_datetime', 'like', "%$current_month%")->count();
            $vm['unread_leads_today'] = LeadForward::where(['forward_to' => $vm->id, 'read_status' => false])->where('lead_datetime', 'like', "%$current_date%")->count();
            $vm['unread_leads_overdue'] = LeadForward::where(['forward_to' => $vm->id, 'read_status' => false])->where('lead_datetime',  '<', Carbon::today())->count();

            $vm['task_schedule_this_month'] = Task::where(['created_by' => $vm->id, 'done_datetime' => null])->where('task_schedule_datetime', 'like', "%$current_month%")->count();
            $vm['task_schedule_today'] = Task::where(['created_by' => $vm->id, 'done_datetime' => null])->where('task_schedule_datetime', 'like', "%$current_date%")->count();
            $vm['task_overdue'] = Task::where(['created_by' => $vm->id, 'done_datetime' => null])->where('task_schedule_datetime', '<', Carbon::today())->count();

            // $vm['recce_schedule_this_month'] = Visit::where(['created_by' => $vm->id, 'done_datetime' => null])->where('visit_schedule_datetime', 'like', "%$current_month%")->count();
            // $vm['recce_schedule_today'] = Visit::where(['created_by' => $vm->id, 'done_datetime' => null])->where('visit_schedule_datetime', 'like', "%$current_date%")->count();
            // $vm['recce_overdue'] = Visit::where(['created_by' => $vm->id, 'done_datetime' => null])->where('visit_schedule_datetime', '<', Carbon::today())->count();
            // $vm['recce_done_this_month'] = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $vm->id])->whereBetween('visits.done_datetime', [$from, $to])->count();
            // $vm['bookings_this_month'] = LeadForward::join('bookings', 'bookings.id', 'lead_forwards.booking_id')->where(['created_by' => $vm->id, 'bookings.deleted_at' => null])->whereBetween('bookings.created_at', [$from, $to])->count();

            $vm['recce_schedule_this_month'] = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $vm->id, 'lead_forwards.source' => 'WB|Team', 'visits.done_datetime' => null, 'visits.deleted_at' => null])->whereBetween('visits.visit_schedule_datetime', [$from, $to])->count();
            $vm['recce_schedule_today'] = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $vm->id, 'lead_forwards.source' => 'WB|Team', 'visits.done_datetime' => null, 'visits.deleted_at' => null])->where('visits.visit_schedule_datetime', 'like', "%$current_date%")->count();
            $vm['recce_done_this_month'] = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $vm->id, 'lead_forwards.source' => 'WB|Team', 'visits.deleted_at' => null])->whereBetween('visits.done_datetime', [$from, $to])->count();
            $vm['recce_overdue'] = Visit::where(['created_by' => $vm->id, 'done_datetime' => null])->where('visit_schedule_datetime', '<', Carbon::today())->count();

            $vm['bookings_this_month'] = LeadForward::join('bookings', 'bookings.id', 'lead_forwards.booking_id')->where(['lead_forwards.forward_to' => $vm->id, 'lead_forwards.source' => 'WB|Team', 'bookings.deleted_at' => null])->whereBetween('bookings.created_at', [$from, $to])->count();

            $l = (int) $vm->leads_received_this_month;
            $r = (int) $vm->recce_done_this_month;
            if ($l > 0 && $r > 0) {
                $l2r = ($r / $l) * 100;
            } else {
                $l2r = 0;
            }
            $vm['l2r'] = number_format($l2r);

            $r = (int) $vm->recce_done_this_month;
            $b = (int) $vm->bookings_this_month;
            if ($b > 0 && $r > 0) {
                $r2c = ($b / $r) * 100;
            } else {
                $r2c = 0;
            }
            $vm['r2c'] = number_format($r2c);

            $vm['unfollowed_leads'] = LeadForward::join('tasks', ['tasks.id' => 'lead_forwards.task_id'])->where(['lead_forwards.forward_to' => $vm->id, 'tasks.deleted_at' => null])->where('lead_forwards.lead_status', '!=', 'Done')->whereNotNull('tasks.done_datetime')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('bookings')
                        ->whereRaw('bookings.id = lead_forwards.booking_id');
                })->get()->count();

            $vm['wb_recce_target'] = VmProductivity::where('team_id', $vm->id)->where('date', 'like', "%$current_month%")->first()->wb_recce_target ?? 0;

            if ($vm->recce_done_this_month > 0 && $vm->wb_recce_target > 0) {
                $val = ($vm->recce_done_this_month / $vm->wb_recce_target) * 100;
                $vm['wb_recce_percentage'] = number_format($val);
            } else {
                $vm['wb_recce_percentage'] = 0;
            }
        }


        $v_members = Vendor::select('id', 'name', 'business_name', 'category_id', 'start_date', 'end_date')->where('status', 1)->get();
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
            if (isset($v->start_date) && $v->start_date) {
                $end_date = isset($v->end_date) && $v->end_date ? new Carbon($v->end_date) : Carbon::now();
                $v['time_period_lead'] = nvLeadForward::where('forward_to', $v->id)
                                                      ->whereBetween('lead_datetime', [new Carbon($v->start_date), $end_date])
                                                      ->count();
            } else {
                $v['time_period_lead'] = 0;
            }
        }
        $vs_id = [];
        foreach ($v_members as $list) {
            array_push($vs_id, $list->id);
        }
        return view('admin.dashboard', compact('total_vendors', 'total_team', 'total_venue_leads', 'total_nv_leads', 'venue_leads_for_this_month','venue_form_leads_for_this_month','venue_ads_leads_for_this_month','venue_whatsapp_leads_for_this_year','venue_organic_leads_for_this_month','venue_form_leads_for_this_year','venue_call_leads_for_this_year','venue_ads_leads_for_this_year','venue_organic_leads_for_this_year', 'venue_call_leads_for_this_month','venue_whatsapp_leads_for_this_month','venue_leads_for_this_year', 'nv_leads_for_this_month', 'nv_leads_for_this_year', 'vm_members', 'yearly_calendar', 'v_members'));
    }
}
