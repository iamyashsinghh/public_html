<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeadForward;
use App\Models\TeamMember;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisitsController extends Controller
{
    public function list(Request $request, $dashboard_filters = null) {
        $filter_params = "";
        if ($request->visit_status != null) {
            $filter_params =  ['visit_status' => $request->visit_status];
        }
        if ($request->visits_source != null) {
            $filter_params = ['visits_source' => $request->visits_source];
        }
        if ($request->event_from_date != null) {
            $filter_params = ['event_from_date' => $request->event_from_date, 'event_to_date' => $request->event_to_date];
        }
        if ($request->visit_created_from_date != null) {
            $filter_params = ['visit_created_from_date' => $request->visit_created_from_date, 'visit_created_to_date' => $request->visit_created_to_date];
        }
        if ($request->visit_done_from_date != null) {
            $filter_params = ['visit_done_from_date' => $request->visit_done_from_date, 'visit_done_to_date' => $request->visit_done_to_date];
        }
        if ($request->visit_schedule_from_date != null) {
            $filter_params = ['visit_schedule_from_date' => $request->visit_schedule_from_date, 'visit_schedule_to_date' => $request->visit_schedule_to_date];
        }
        if ($request->pax_min_value != null) {
            $filter_params = ['pax_min_value' => $request->pax_min_value, 'pax_max_value' => $request->pax_max_value];
        }

        $page_heading = $filter_params ? "Visits - Filtered" : "Visits";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }

        return view('manager.venueCrm.visit.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('manager')->user();
        $vm_members = TeamMember::select('id', 'name', 'venue_name')->where('parent_id', $auth_user->id)->get();
        $vms_id = [];
        foreach ($vm_members as $list) {
            array_push($vms_id, $list->id);
        }
        $visits = LeadForward::select(
            'lead_forwards.lead_id',
            'lead_forwards.lead_datetime',
            'lead_forwards.source',
            'team_members.venue_name as venue_name',
            'team_members.name as vm_name',
            'lead_forwards.name',
            'lead_forwards.mobile',
            'lead_forwards.lead_status',
            'visits.visit_schedule_datetime',
            'lead_forwards.event_datetime',
            'visits.created_at as visit_created_datetime',
            'visits.done_datetime as visit_done_datetime',
            'visits.event_name as event_name',
            'ne.pax as pax',
        )->join('visits', ['lead_forwards.lead_id' => 'visits.lead_id'])
        ->leftJoin('vm_events as ne', 'ne.lead_id', '=', 'lead_forwards.lead_id')
        ->join('team_members', 'team_members.id', 'lead_forwards.forward_to')
        ->where(['visits.deleted_at' => null])->whereIn('lead_forwards.forward_to', $vms_id)->groupBy('lead_forwards.lead_id');

        $current_date = date('Y-m-d');
        $current_month = date('Y-m');
        if ($request->visit_status == "Upcoming") {
            $visits->where('visits.visit_schedule_datetime', '>', Carbon::today()->endOfDay())->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Today") {
            $visits->where('visits.visit_schedule_datetime', 'like', "%$current_date%")->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Overdue") {
            $visits->where('visits.visit_schedule_datetime', '<', Carbon::today())->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Done") {
            $visits->whereNotNull('visits.done_datetime');
        }elseif($request->visits_source) {
            $visits->where('lead_forwards.source' , $request->visits_source);
        } elseif ($request->visit_created_from_date) {
            $from = Carbon::make($request->visit_created_from_date);
            if ($request->visit_created_to_date != null) {
                $to = Carbon::make($request->visit_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_created_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.created_at', [$from, $to]);
        }elseif ($request->event_from_date != null) {
            $from = Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $visits->whereBetween('lead_forwards.event_datetime', [$from, $to]);
        } elseif ($request->visit_done_from_date) {
            $from = Carbon::make($request->visit_done_from_date);
            if ($request->visit_done_to_date != null) {
                $to = Carbon::make($request->visit_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_done_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.done_datetime', [$from, $to]);
        }elseif ($request->pax_min_value != null) {
            $min = $request->pax_min_value;
            if ($request->pax_max_value != null) {
                $max = $request->pax_max_value;
            } else {
                $max = $request->pax_min_value;
            }
            $visits->whereBetween('ne.pax', [$min, $max]);
        } elseif ($request->visit_schedule_from_date) {
            $from = Carbon::make($request->visit_schedule_from_date);
            if ($request->visit_schedule_to_date != null) {
                $to = Carbon::make($request->visit_schedule_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_schedule_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
        } elseif ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "recce_schedule_this_month") {
                $visits->where([
                    'lead_forwards.source' => 'WB|Team',
                ])
                ->where('visits.visit_schedule_datetime',  'like', "%$current_month%")
                ->whereDate('visits.created_at', '>', '2025-01-1');
            } elseif ($request->dashboard_filters == "recce_schedule_today") {
                $visits->where([
                    'visits.clh_status' => null,
                    'lead_forwards.source' => 'WB|Team',
                ])
                ->whereDate('visits.visit_schedule_datetime', $current_date)
                ->whereDate('visits.created_at', '>', '2025-01-1');
            } elseif ($request->dashboard_filters == "recce_overdue") {
                $visits->where([
                    'lead_forwards.source' => 'WB|Team',
                    'visits.clh_status' => null
                ])
                ->whereDate('visits.visit_schedule_datetime', '<', $current_date)
                ->whereDate('visits.created_at', '>', '2025-01-1');
                }
        }
        $visits = $visits->get();
        return datatables($visits)->toJson();
    }

    public function update(Request $request){
        $auth_user = Auth::guard('manager')->user();
        $exist_visit = Visit::where(['id' => $request->visit_id])->first();
        if (!$exist_visit) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Invalid Visit.']);
            return redirect()->back();
        }

        $exist_visit->clh_status = $request->clh_status;
        $exist_visit->clh_follow_up_date = $request->clh_follow_up_date;
        $exist_visit->clh_dropped_reason = $request->clh_dropped_reason;
        $exist_visit->clh_action_step_taken = $request->clh_action_step_taken;
        $exist_visit->clh_action_step_taken_by = $auth_user->id;

        Log::info($request);
        if($exist_visit->save()){
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Visit created successfully.']);
            return redirect()->back();
        }else{
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Internal Server Error.']);
            return redirect()->back();
        }
    }
}
