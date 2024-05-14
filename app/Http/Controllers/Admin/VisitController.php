<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadForward;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VisitController extends Controller
{
    public function list(Request $request, $dashboard_filters = null) {
        $filter_params = "";
        if ($request->visit_status != null) {
            $filter_params =  ['visit_status' => $request->visit_status];
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

        $page_heading = $filter_params ? "Visits - Filtered" : "Visits";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }

        return view('admin.venueCrm.visit.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $visits = LeadForward::select(
            'lead_forwards.lead_id',
            'lead_forwards.lead_datetime',
            'lead_forwards.name',
            'lead_forwards.mobile',
            'lead_forwards.lead_status',
            'visits.visit_schedule_datetime',
            'lead_forwards.event_datetime',
            'visits.created_at as visit_created_datetime',
            'visits.done_datetime as visit_done_datetime',
        )->join('visits', ['lead_forwards.visit_id' => 'visits.id'])->where(['visits.deleted_at' => null]);

        $current_date = date('Y-m-d');
        if ($request->visit_status == "Upcoming") {
            $visits->where('visits.visit_schedule_datetime', '>', Carbon::today()->endOfDay())->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Today") {
            $visits->where('visits.visit_schedule_datetime', 'like', "%$current_date%")->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Overdue") {
            $visits->where('visits.visit_schedule_datetime', '<', Carbon::today())->whereNull('visits.done_datetime');
        } elseif ($request->visit_status == "Done") {
            $visits->whereNotNull('visits.done_datetime');
        } elseif ($request->visit_created_from_date) {
            $from = Carbon::make($request->visit_created_from_date);
            if ($request->visit_created_to_date != null) {
                $to = Carbon::make($request->visit_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_created_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.created_at', [$from, $to]);
        } elseif ($request->visit_done_from_date) {
            $from = Carbon::make($request->visit_done_from_date);
            if ($request->visit_done_to_date != null) {
                $to = Carbon::make($request->visit_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->visit_done_from_date)->endOfDay();
            }
            $visits->whereBetween('visits.done_datetime', [$from, $to]);
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
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == "recce_schedule_today") {
                $from =  Carbon::today()->startOfDay();
                $to =  Carbon::today()->endOfDay();
                $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == "total_recce_overdue") {
                $visits->where('visits.visit_schedule_datetime', '<', Carbon::today())->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == "recce_done_this_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $visits->whereBetween('visits.done_datetime', [$from, $to]);
            }
        }
        $visits = $visits->get();
        return datatables($visits)->toJson();
    }
}
