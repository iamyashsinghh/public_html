<?php

namespace App\Console\Commands;

use App\Models\DashboardStatistics;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\LeadForwardInfo;
use App\Models\nvLead;
use App\Models\nvLeadForward;
use App\Models\nvLeadForwardInfo;
use App\Models\nvMeeting;
use App\Models\nvrmLeadForward;
use App\Models\nvTask;
use App\Models\PVendorLead;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\Visit;
use App\Models\VmProductivity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PrecomputeDashboardData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:precompute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Precompute dashboard data and store it to avoid recalculating on every load';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
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
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->whereIn('source', ['WB|Call', 'WB|Form', 'WB|WhatsApp'])->count();
            array_push($venue_leads_for_this_month, $count);
        }
        $venue_leads_for_this_month = implode(",", $venue_leads_for_this_month);
        $average_leads_for_month = array_sum(explode(",", $venue_leads_for_this_month)) / date('d');


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
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->whereIn('source', ['WB|Call', 'WB|Form', 'WB|WhatsApp'])->count();
            array_push($venue_leads_for_this_year, $count);
        }
        $venue_leads_for_this_year = implode(",", $venue_leads_for_this_year);

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

        $nv_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = nvLead::where('nv_leads.lead_datetime', 'like', "%$datetime%")
                ->count();
            array_push($nv_leads_for_this_month, $count);
        }
        $nv_leads_for_this_month = implode(",", $nv_leads_for_this_month);

        $nv_forward_leads_for_this_month_wb_venue = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = $lead_count = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')
                ->join('vendors', 'vendors.id', '=', 'nv_lead_forward_infos.forward_to')
                ->where('vendors.category_id', 4)
                ->where('nv_lead_forward_infos.updated_at', 'like', "%$datetime%")
                ->groupBy('nv_lead_forward_infos.lead_id')
                ->get()
                ->count();
            array_push($nv_forward_leads_for_this_month_wb_venue, $count);
        }
        $nv_forward_leads_for_this_month_wb_venue = implode(",", $nv_forward_leads_for_this_month_wb_venue);

        $nv_forward_leads_for_this_month_photography = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = $lead_count = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')
                ->join('vendors', 'vendors.id', '=', 'nv_lead_forward_infos.forward_to')
                ->where('vendors.category_id', 1)
                ->where('nv_lead_forward_infos.updated_at', 'like', "%$datetime%")
                ->groupBy('nv_lead_forward_infos.lead_id')
                ->get()
                ->count();
            array_push($nv_forward_leads_for_this_month_photography, $count);
        }
        $nv_forward_leads_for_this_month_photography = implode(",", $nv_forward_leads_for_this_month_photography);

        $nv_forward_leads_for_this_month_makeup_artist = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = $lead_count = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')
                ->join('vendors', 'vendors.id', '=', 'nv_lead_forward_infos.forward_to')
                ->where('vendors.category_id', 2)
                ->where('nv_lead_forward_infos.updated_at', 'like', "%$datetime%")
                ->groupBy('nv_lead_forward_infos.lead_id')
                ->get()
                ->count();
            array_push($nv_forward_leads_for_this_month_makeup_artist, $count);
        }
        $nv_forward_leads_for_this_month_makeup_artist = implode(",", $nv_forward_leads_for_this_month_makeup_artist);

        $nv_forward_leads_for_this_month_mehndi_artist = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = $lead_count = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')
                ->join('vendors', 'vendors.id', '=', 'nv_lead_forward_infos.forward_to')
                ->where('vendors.category_id', 3)
                ->where('nv_lead_forward_infos.updated_at', 'like', "%$datetime%")
                ->groupBy('nv_lead_forward_infos.lead_id')
                ->get()
                ->count();
            array_push($nv_forward_leads_for_this_month_mehndi_artist, $count);
        }
        $nv_forward_leads_for_this_month_mehndi_artist = implode(",", $nv_forward_leads_for_this_month_mehndi_artist);

        $nv_forward_leads_for_this_month_band_baja = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = $lead_count = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')
                ->join('vendors', 'vendors.id', '=', 'nv_lead_forward_infos.forward_to')
                ->where('vendors.category_id', 5)
                ->where('nv_lead_forward_infos.updated_at', 'like', "%$datetime%")
                ->groupBy('nv_lead_forward_infos.lead_id')
                ->get()
                ->count();
            array_push($nv_forward_leads_for_this_month_band_baja, $count);
        }
        $nv_forward_leads_for_this_month_band_baja = implode(",", $nv_forward_leads_for_this_month_band_baja);

        $nv_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = nvLead::where('nv_leads.lead_datetime', 'like', "%$datetime%")
                ->count();
            array_push($nv_leads_for_this_year, $count);
        }
        $nv_leads_for_this_year = implode(",", $nv_leads_for_this_year);

        $vm_members = TeamMember::select('id', 'parent_id', 'name', 'venue_name')->where(['role_id' => 5, 'status' => 1])->orderBy('parent_id')->orderBy('venue_name')->get();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');

        $from = Carbon::today()->startOfMonth();
        $to = Carbon::today()->endOfMonth();
        foreach ($vm_members as $vm) {
            $vm['leads_received_this_month'] = LeadForward::where('lead_datetime', 'like', "%$current_month%")->where('forward_to', $vm->id)->count();
            $vm['leads_received_today'] = LeadForward::where('lead_datetime', 'like', "%$current_date%")->where('forward_to', $vm->id)->count();
            $vm['unread_leads_this_month'] = LeadForward::where('lead_datetime', 'like', "%$current_month%")->where(['forward_to' => $vm->id, 'read_status' => false])->count();
            $vm['unread_leads_today'] = LeadForward::where('lead_datetime', 'like', "%$current_date%")->where(['forward_to' => $vm->id, 'read_status' => false])->count();
            $vm['unread_leads_overdue'] = LeadForward::where('read_status', false)->where('lead_datetime', '<', Carbon::today())->where('forward_to', $vm->id)->count();

            $vm['task_schedule_this_month'] = Task::selectRaw('count(distinct(lead_id)) as count')
                ->join('leads', 'tasks.lead_id', '=', 'leads.lead_id')
                ->where('task_schedule_datetime', 'like', "%$current_month%")
                ->where(['tasks.created_by' => $vm->id])
                ->whereNull('tasks.done_datetime')->count();
            $vm['task_schedule_today'] = Task::selectRaw('count(distinct(lead_id)) as count')
                ->join('leads', 'tasks.lead_id', '=', 'leads.lead_id')
                ->where('task_schedule_datetime', 'like', "%$current_date%")
                ->where(['tasks.created_by' => $vm->id])
                ->whereNull('tasks.done_datetime')->count();
            $vm['task_overdue'] = Task::selectRaw('count(distinct(lead_id)) as count')
                ->join('leads', 'tasks.lead_id', '=', 'leads.lead_id')
                ->where('task_schedule_datetime', '<', Carbon::today())
                ->where(['tasks.created_by' => $vm->id])
                ->whereNull('tasks.done_datetime')->count();

            $vm['recce_schedule_this_month'] = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $vm->id, 'lead_forwards.source' => 'WB|Team', 'visits.done_datetime' => null, 'visits.deleted_at' => null])->whereBetween('visits.visit_schedule_datetime', [$from, $to])->count();
            $vm['recce_schedule_today'] = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $vm->id, 'lead_forwards.source' => 'WB|Team', 'visits.done_datetime' => null, 'visits.deleted_at' => null])->where('visits.visit_schedule_datetime', 'like', "%$current_date%")->count();
            $vm['recce_done_this_month'] = LeadForward::join('visits', ['visits.id' => 'lead_forwards.visit_id'])->where(['lead_forwards.forward_to' => $vm->id, 'lead_forwards.source' => 'WB|Team', 'visits.deleted_at' => null])->whereBetween('visits.done_datetime', [$from, $to])->count();
            $vm['recce_overdue'] = Visit::selectRaw('count(distinct(lead_id)) as count')->where('visit_schedule_datetime', '<', Carbon::today())->where(['created_by' => $vm->id, 'done_datetime' => null])->first()->count;

            $vm['get_manager'] = $vm->get_manager;

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


        $currentDateTime = Carbon::today();
        $currentDateStart = Carbon::now()->startOfDay();
        $currentDateEnd = Carbon::now()->endOfDay();
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $rm_members = TeamMember::select('id', 'name',)->where(['role_id' => 4, 'status' => 1])->get();
        $current_month = date('Y-m');
        $seven_days_ago = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');

        foreach ($rm_members as $rm) {
            $rm['total_leads_received_this_month'] = Lead::where('lead_datetime', 'like', "%$current_month%")->where('assign_id', $rm->id)->count();
            $rm['total_leads_received_today'] =  Lead::where('lead_datetime', 'like', "%$current_date%")->where('assign_id', $rm->id)->count();

            $rm['unread_leads_this_month'] =   Lead::where('lead_datetime', 'like', "%$current_month%")
                ->where('read_status', false)
                ->where('assign_id', $rm->id)
                ->where(function ($query) use ($seven_days_ago) {
                    $query->whereNull('last_forwarded_by')
                        ->orWhere('last_forwarded_by', '<=', $seven_days_ago);
                })
                ->count();
            $rm['unread_leads_today'] = Lead::where('lead_datetime', 'like', "%$current_date%")
                ->where('read_status', false)
                ->where('assign_id', $rm->id)
                ->where(function ($query) use ($seven_days_ago) {
                    $query->whereNull('last_forwarded_by')
                        ->orWhere('last_forwarded_by', '<=', $seven_days_ago);
                })
                ->count();
            $rm['total_unread_leads_overdue'] = Lead::where('lead_datetime', '<', Carbon::today())
                ->where('read_status', false)
                ->where('assign_id', $rm->id)
                ->where('lead_id', '>', '54262')
                ->where(function ($query) use ($seven_days_ago) {
                    $query->whereNull('last_forwarded_by')
                        ->orWhere('last_forwarded_by', '<=', $seven_days_ago);
                })
                ->count();
            $rm['forward_leads_this_month'] =   LeadForwardInfo::whereBetween('updated_at', [$from, $to])->where('forward_from', $rm->id)->groupBy('lead_id')->get()->count();
            $rm['forward_leads_today'] = LeadForwardInfo::where('updated_at', 'like', "%$current_date%")->where('forward_from', $rm->id)->groupBy('lead_id')->get()->count();

            $rm['rm_task_overdue_leads'] = Lead::join('tasks', 'leads.lead_id', '=', 'tasks.lead_id')
                ->where('leads.lead_status', '!=', 'Done')
                ->where('tasks.task_schedule_datetime', '<', $currentDateTime)
                ->whereNull('leads.deleted_at')
                ->whereNull('tasks.deleted_at')
                ->where('tasks.created_by', $rm->id)
                ->whereNull('tasks.done_datetime')
                ->groupBy('leads.lead_id')
                ->get()->count();

            $rm['rm_today_task_leads'] = Lead::join('tasks', 'leads.lead_id', '=', 'tasks.lead_id')
                ->where('leads.lead_status', '!=', 'Done')
                ->whereBetween('tasks.task_schedule_datetime', [$currentDateStart, $currentDateEnd])
                ->whereNull('leads.deleted_at')
                ->whereNull('tasks.deleted_at')
                ->whereNull('tasks.done_datetime')
                ->where('tasks.created_by', $rm->id)->groupBy('leads.lead_id')
                ->get()->count();

            $rm['rm_month_task_leads'] = Lead::join('tasks', 'leads.lead_id', '=', 'tasks.lead_id')
                ->where('leads.lead_status', '!=', 'Done')
                ->whereBetween('tasks.task_schedule_datetime', [$currentMonthStart, $currentMonthEnd])
                ->whereNull('leads.deleted_at')
                ->whereNull('tasks.deleted_at')
                ->where('tasks.created_by', $rm->id)
                ->whereNull('tasks.done_datetime')
                ->groupBy('tasks.lead_id')->get()->count();

            $rm['rm_unfollowed_leads'] =  DB::table('leads')
                ->leftJoin('team_members as tm', 'tm.id', '=', 'leads.created_by')
                ->whereNull('leads.deleted_at')
                ->leftJoin(DB::raw("
            (SELECT tasks.lead_id
            FROM tasks
            WHERE tasks.deleted_at IS NULL
            AND tasks.created_by = $rm->id
            GROUP BY tasks.lead_id
            HAVING COUNT(CASE WHEN tasks.done_datetime IS NULL THEN 1 END) = 0) as completed_tasks
            "), 'completed_tasks.lead_id', '=', 'leads.lead_id')
                ->whereNotNull('completed_tasks.lead_id')
                ->where('leads.lead_status', '!=', 'Done')
                ->where('leads.assign_id',  $rm->id)
                ->count();
        }


        $v_members = Vendor::select('id', 'name', 'business_name', 'category_id', 'start_date', 'end_date')->where('status', 1)->get();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');
        $from = Carbon::today()->startOfMonth();
        $to = Carbon::today()->endOfMonth();
        foreach ($v_members as $v) {
            $v['total_leads_received'] = nvLeadForward::where(['forward_to' => $v->id])->count();
            $v['leads_received_this_month'] = nvLeadForward::where(['forward_to' => $v->id])->where('lead_datetime', 'like', "%$current_month%")->count();
            $v['leads_received_today'] = nvLeadForward::where(['forward_to' => $v->id])->where('lead_datetime', 'like', "%$current_date%")->count();
            $v['unread_leads_this_month'] = nvLeadForward::where(['forward_to' => $v->id, 'read_status' => false])->where('lead_datetime', 'like', "%$current_month%")->count();
            $v['unread_leads_today'] = nvLeadForward::where(['forward_to' => $v->id, 'read_status' => false])->where('lead_datetime', 'like', "%$current_date%")->count();
            $v['unread_leads_overdue'] = nvLeadForward::where(['forward_to' => $v->id, 'read_status' => false])->where('lead_datetime', '<', Carbon::today())->count();

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

        $nv_members = TeamMember::select('id', 'name')->where('status', 1)->where('role_id', '3')->get();
        $current_month = date('Y-m');
        $current_date = date('Y-m-d');
        $from = Carbon::today()->startOfMonth();
        $to = Carbon::today()->endOfMonth();
        $currentDateTime = Carbon::today();
        $currentDateStart = Carbon::now()->startOfDay();
        $currentDateEnd = Carbon::now()->endOfDay();
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        foreach ($nv_members as $key => $v) {
            $v['leads_received_this_month'] = nvrmLeadForward::where('lead_datetime', 'like', "%$current_month%")->whereNull('deleted_at')->where('forward_to', $v->id)->distinct('lead_id')->count();
            $v['leads_received_today'] = nvrmLeadForward::where('lead_datetime', 'like', "%$current_date%")->whereNull('deleted_at')->where('forward_to', $v->id)->distinct('lead_id')->count();
            $v['unread_leads_this_month'] = nvrmLeadForward::where('lead_datetime', 'like', "%$current_month%")->whereNull('deleted_at')->where(['read_status' => false])->where('forward_to', $v->id)->distinct('lead_id')->count();
            $v['unread_leads_today'] = nvrmLeadForward::where('lead_datetime', 'like', "%$current_date%")->where(['read_status' => false])->whereNull('deleted_at')->where('forward_to', $v->id)->distinct('lead_id')->count();
            $v['unread_leads_overdue'] = nvrmLeadForward::where('lead_datetime', '>=', Carbon::parse('2024-02-01')->startOfDay())
                ->where('lead_datetime', '<=', Carbon::now())
                ->where('read_status', false)
                ->whereNull('deleted_at')
                ->where('forward_to', $v->id)
                ->distinct('lead_id')
                ->count('lead_id');

            $v['forward_leads_this_month'] = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')->where('nv_lead_forward_infos.updated_at', 'like', "%$current_month%")->whereNull('nvrm_lead_forwards.deleted_at')->where(['nv_lead_forward_infos.forward_from' => $v->id])->groupBy('nv_lead_forward_infos.lead_id')->get()->count();
            $v['forward_leads_today'] = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')->where('nv_lead_forward_infos.updated_at', 'like', "%$current_date%")->whereNull('nvrm_lead_forwards.deleted_at')->where(['nv_lead_forward_infos.forward_from' => $v->id])->groupBy('nv_lead_forward_infos.lead_id')->get()->count();

            $v['nvrm_unfollowed_leads'] = nvrmLeadForward::query()
                ->where('lead_status', '!=', 'Done')
                ->whereNull('deleted_at')
                ->whereExists(function ($query) use ($v) {
                    $query->select(DB::raw(1))
                        ->from('nvrm_tasks')
                        ->whereColumn('nvrm_tasks.lead_id', 'nvrm_lead_forwards.lead_id')
                        ->whereNotNull('nvrm_tasks.done_datetime')
                        ->whereNull('nvrm_tasks.deleted_at')
                        ->where('nvrm_tasks.created_by', $v->id);
                })
                ->whereDoesntHave('nvrm_tasks', function ($query) {
                    $query->whereNull('done_datetime');
                })
                ->distinct('lead_id')
                ->count();


            $v['task_schedule_this_month'] = nvrmLeadForward::join('nvrm_tasks', 'nvrm_lead_forwards.lead_id', '=', 'nvrm_tasks.lead_id')
                ->whereBetween('nvrm_tasks.task_schedule_datetime', [$currentMonthStart, $currentMonthEnd])
                ->where('nvrm_lead_forwards.lead_status', '!=', 'Done')
                ->whereNull('nvrm_lead_forwards.deleted_at')
                ->whereNull('nvrm_tasks.done_datetime')
                ->whereNull('nvrm_tasks.deleted_at')
                ->groupBy('nvrm_tasks.lead_id')
                ->where('nvrm_tasks.created_by', $v->id)
                ->get()
                ->count();

            $v['task_schedule_today'] =  nvrmLeadForward::join('nvrm_tasks', 'nvrm_lead_forwards.lead_id', '=', 'nvrm_tasks.lead_id')
                ->where('nvrm_lead_forwards.lead_status', '!=', 'Done')
                ->whereBetween('nvrm_tasks.task_schedule_datetime', [$currentDateStart, $currentDateEnd])
                ->whereNull('nvrm_tasks.done_datetime')
                ->whereNull('nvrm_lead_forwards.deleted_at')
                ->whereNull('nvrm_tasks.deleted_at')
                ->groupBy('nvrm_tasks.lead_id')
                ->where('nvrm_tasks.created_by', $v->id)
                ->get()
                ->count();

            $v['task_overdue'] = nvrmLeadForward::join('nvrm_tasks', 'nvrm_lead_forwards.lead_id', '=', 'nvrm_tasks.lead_id')
                ->where('nvrm_lead_forwards.lead_status', '!=', 'Done')
                ->where('nvrm_tasks.task_schedule_datetime', '<', $currentDateTime)
                ->whereNull('nvrm_tasks.done_datetime')
                ->whereNull('nvrm_lead_forwards.deleted_at')
                ->whereNull('nvrm_tasks.deleted_at')
                ->groupBy('nvrm_tasks.lead_id')
                ->where('nvrm_tasks.created_by', $v->id)
                ->get()
                ->count();

            $categories = VendorCategory::whereIn('id', [1, 2, 3, 4, 5])->get();
            $forward_leads_by_category = [];
            foreach ($categories as $category) {
                $category_name = $category->name;
                $lead_count = nvLeadForwardInfo::join('nvrm_lead_forwards', 'nv_lead_forward_infos.lead_id', '=', 'nvrm_lead_forwards.lead_id')
                    ->join('vendors', 'vendors.id', '=', 'nv_lead_forward_infos.forward_to')
                    ->where('vendors.category_id', $category->id)
                    ->where('nv_lead_forward_infos.updated_at', 'like', "%$current_month%")
                    ->where(['nv_lead_forward_infos.forward_from' => $v->id])
                    ->groupBy('nv_lead_forward_infos.lead_id')
                    ->get()
                    ->count();
                $forward_leads_by_category[$category_name] = $lead_count;
            }
            $nv_members[$key]->forward_leads_by_category = $forward_leads_by_category;
        }
        $nv_id = [];
        foreach ($nv_members as $list) {
            array_push($nv_id, $list->id);
        }

        $data = [
            'total_vendors' => $total_vendors,
            'total_team'  => $total_team,
            'total_venue_leads'  => $total_venue_leads,
            'total_nv_leads'  => $total_nv_leads,
            'average_leads_for_month' => $average_leads_for_month,
            'venue_leads_for_this_month' => $venue_leads_for_this_month,
            'venue_form_leads_for_this_month' => $venue_form_leads_for_this_month,
            'venue_ads_leads_for_this_month' => $venue_ads_leads_for_this_month,
            'venue_organic_leads_for_this_month' => $venue_organic_leads_for_this_month,
            'venue_ads_leads_for_this_year' => $venue_ads_leads_for_this_year,
            'venue_organic_leads_for_this_year' => $venue_organic_leads_for_this_year,
            'venue_call_leads_for_this_month' => $venue_call_leads_for_this_month,
            'venue_whatsapp_leads_for_this_month' => $venue_whatsapp_leads_for_this_month,
            'venue_leads_for_this_year' => $venue_leads_for_this_year,
            'nv_leads_for_this_month' => $nv_leads_for_this_month,
            'nv_forward_leads_for_this_month_wb_venue' => $nv_forward_leads_for_this_month_wb_venue,
            'nv_forward_leads_for_this_month_band_baja' => $nv_forward_leads_for_this_month_band_baja,
            'nv_forward_leads_for_this_month_mehndi_artist' => $nv_forward_leads_for_this_month_mehndi_artist,
            'nv_forward_leads_for_this_month_makeup_artist' => $nv_forward_leads_for_this_month_makeup_artist,
            'nv_forward_leads_for_this_month_photography' => $nv_forward_leads_for_this_month_photography,
            'nv_leads_for_this_year' => $nv_leads_for_this_year,
            'vm_members' => $vm_members,
            'rm_members' => $rm_members,
            'yearly_calendar' => $yearly_calendar,
            'v_members' => $v_members,
            'nv_members' => $nv_members,
            'categories' => $categories
        ];

        DashboardStatistics::updateOrCreate(
            ['type' => 'dashboard'],
            ['data' => $data]
        );

        // Success message
        $this->info('Dashboard data has been precomputed and stored.');
    }
}
