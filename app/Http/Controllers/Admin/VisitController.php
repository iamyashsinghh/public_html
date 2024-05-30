<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadForward;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VisitController extends Controller
{
    public function list(Request $request, $dashboard_filters = null)
    {
        $filter_params = "";
        if ($request->visit_status != null) {
            $filter_params = ['visit_status' => $request->visit_status];
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

        return view('admin.venueCrm.visit.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request)
    {
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
        )->join('visits', ['lead_forwards.visit_id' => 'visits.id'])
            ->join('team_members', 'team_members.id', 'lead_forwards.forward_to')
            ->leftJoin('vm_events as ne', 'ne.lead_id', '=', 'lead_forwards.lead_id')
            ->groupBy('lead_forwards.id')
            ->where(['visits.deleted_at' => null]);

        $current_date = date('Y-m-d');
        if ($request->visit_status == "Upcoming") {
            $visits->where('visits.visit_schedule_datetime', '>', Carbon::today()->endOfDay())->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Today") {
            $visits->where('visits.visit_schedule_datetime', 'like', "%$current_date%")->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Overdue") {
            $visits->where('visits.visit_schedule_datetime', '<', Carbon::today())->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Done") {
            $visits->whereNotNull('visits.done_datetime');
        }
        if ($request->visits_source) {
            $visits->where('lead_forwards.source', $request->visits_source);
        }
        if ($request->visit_created_from_date) {
            $from = Carbon::make($request->visit_created_from_date);
            if ($request->visit_created_to_date != null) {
                $to = Carbon::make($request->visit_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_created_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.created_at', [$from, $to]);
        }
        if ($request->event_from_date != null) {
            $from = Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $visits->whereBetween('lead_forwards.event_datetime', [$from, $to]);
        }
        if ($request->visit_done_from_date) {
            $from = Carbon::make($request->visit_done_from_date);
            if ($request->visit_done_to_date != null) {
                $to = Carbon::make($request->visit_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_done_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.done_datetime', [$from, $to]);
        }
        if ($request->visit_schedule_from_date) {
            $from = Carbon::make($request->visit_schedule_from_date);
            if ($request->visit_schedule_to_date != null) {
                $to = Carbon::make($request->visit_schedule_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_schedule_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
        }
        if ($request->pax_min_value != null) {
            $min = $request->pax_min_value;
            if ($request->pax_max_value != null) {
                $max = $request->pax_max_value;
            } else {
                $max = $request->pax_min_value;
            }
            $visits->whereBetween('ne.pax', [$min, $max]);
        }
        if ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "recce_schedule_this_month") {
                $from = Carbon::today()->startOfMonth();
                $to = Carbon::today()->endOfMonth();
                $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == "recce_schedule_today") {
                $from = Carbon::today()->startOfDay();
                $to = Carbon::today()->endOfDay();
                $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == "total_recce_overdue") {
                $visits->where('visits.visit_schedule_datetime', '<', Carbon::today())->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == "recce_done_this_month") {
                $from = Carbon::today()->startOfMonth();
                $to = Carbon::today()->endOfMonth();
                $visits->whereBetween('visits.done_datetime', [$from, $to]);
            }
        }
        $visits = $visits->get();
        return datatables($visits)->toJson();
    }
}
