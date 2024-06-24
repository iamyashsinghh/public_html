<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadForward;
use App\Models\TeamMember;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function list(Request $request, $dashboard_filters = null)
    {
        $filter_params = [];

        if ($request->visit_status != null) {
            $filter_params['visit_status'] = $request->visit_status;
        }
        if ($request->visits_source != null) {
            $filter_params['visits_source'] = $request->visits_source;
        }
        if ($request->venues_source != null) {
            $filter_params['venues_source'] = $request->venues_source;
        }
        if ($request->vm_source != null) {
            $filter_params['vm_source'] = $request->vm_source;
        }
        if ($request->event_from_date != null) {
            $filter_params['event_from_date'] = $request->event_from_date;
            $filter_params['event_to_date'] = $request->event_to_date;
        }
        if ($request->visit_created_from_date != null) {
            $filter_params['visit_created_from_date'] = $request->visit_created_from_date;
            $filter_params['visit_created_to_date'] = $request->visit_created_to_date;
        }
        if ($request->visit_done_from_date != null) {
            $filter_params['visit_done_from_date'] = $request->visit_done_from_date;
            $filter_params['visit_done_to_date'] = $request->visit_done_to_date;
        }
        if ($request->visit_schedule_from_date != null) {
            $filter_params['visit_schedule_from_date'] = $request->visit_schedule_from_date;
            $filter_params['visit_schedule_to_date'] = $request->visit_schedule_to_date;
        }
        if ($request->pax_min_value != null) {
            $filter_params['pax_min_value'] = $request->pax_min_value;
            $filter_params['pax_max_value'] = $request->pax_max_value;
        }

        $page_heading = $filter_params ? 'Visits - Filtered' : 'Visits';

        if ($dashboard_filters !== null) {
            $filter_params['dashboard_filters'] = $dashboard_filters;
            $page_heading = ucwords(str_replace('_', ' ', $dashboard_filters));
        }

        $vm_id_name = TeamMember::select('id', 'name')->where('role_id', 5)->get();
        $all_venues = Venue::select('id', 'name')->get();
        return view('admin.venueCrm.visit.list', compact('page_heading', 'filter_params', 'vm_id_name', 'all_venues'));
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
            'visits.created_at as visit_created_datetime',
            'visits.done_datetime as visit_done_datetime',
            'visits.event_name as event_name',
            'ne.pax as pax',
            DB::raw('DATE(lead_forwards.event_datetime) as event_datetime'),
        )
        ->join('visits', 'visits.id', '=', 'lead_forwards.visit_id')
        ->join('team_members', 'team_members.id', '=', 'lead_forwards.forward_to')
        ->leftJoin('vm_events as ne', 'ne.lead_id', '=', 'lead_forwards.lead_id')
        ->groupBy('lead_forwards.id')
        ->whereNull('visits.deleted_at');

        if ($request->has('visit_status')) {
            $visits->where(function ($query) use ($request) {
                foreach ($request->visit_status as $status) {
                    switch ($status) {
                        case 'Upcoming':
                            $query->orWhere(function ($q) {
                                $q->where('visits.visit_schedule_datetime', '>', Carbon::today()->endOfDay())
                                    ->whereNull('visits.done_datetime');
                            });
                            break;
                        case 'Today':
                            $current_date = date('Y-m-d');
                            $query->orWhere(function ($q) use ($current_date) {
                                $q->where('visits.visit_schedule_datetime', 'like', "%$current_date%")
                                    ->whereNull('visits.done_datetime');
                            });
                            break;
                        case 'Overdue':
                            $query->orWhere(function ($q) {
                                $q->where('visits.visit_schedule_datetime', '<', Carbon::today())
                                    ->whereNull('visits.done_datetime');
                            });
                            break;
                        case 'Done':
                            $query->orWhereNotNull('visits.done_datetime');
                            break;
                    }
                }
            });
        }

        if ($request->visits_source) {
            $visits->whereIn('lead_forwards.source', $request->visits_source);
        }
        
        if ($request->vm_source) {
            $visits->whereIn('team_members.name', $request->vm_source);
        }
        if ($request->venues_source) {
            $visits->whereIn('team_members.venue_name', $request->venues_source);
        }

        if ($request->visit_created_from_date) {
            $from = Carbon::make($request->visit_created_from_date);
            $to = $request->visit_created_to_date ? Carbon::make($request->visit_created_to_date)->endOfDay() : $from->endOfDay();
            $visits->whereBetween('visits.created_at', [$from, $to]);
        }
        if ($request->event_from_date) {
            $from = Carbon::make($request->event_from_date);
            $to = $request->event_to_date ? Carbon::make($request->event_to_date)->endOfDay() : $from->endOfDay();
            $visits->whereBetween('lead_forwards.event_datetime', [$from, $to]);
        }
        if ($request->visit_done_from_date) {
            $from = Carbon::make($request->visit_done_from_date);
            $to = $request->visit_done_to_date ? Carbon::make($request->visit_done_to_date)->endOfDay() : $from->endOfDay();
            $visits->whereBetween('visits.done_datetime', [$from, $to]);
        }
        if ($request->visit_schedule_from_date) {
            $from = Carbon::make($request->visit_schedule_from_date);
            $to = $request->visit_schedule_to_date ? Carbon::make($request->visit_schedule_to_date)->endOfDay() : $from->endOfDay();
            $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
        }
        if ($request->pax_min_value) {
            $min = $request->pax_min_value;
            $max = $request->pax_max_value ?? $min;
            $visits->whereBetween('ne.pax', [$min, $max]);
        }
        if ($request->dashboard_filters) {
            if ($request->dashboard_filters == 'recce_schedule_this_month') {
                $from = Carbon::today()->startOfMonth();
                $to = Carbon::today()->endOfMonth();
                $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == 'recce_schedule_today') {
                $from = Carbon::today()->startOfDay();
                $to = Carbon::today()->endOfDay();
                $visits->whereBetween('visits.visit_schedule_datetime', [$from, $to])->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == 'total_recce_overdue') {
                $visits->where('visits.visit_schedule_datetime', '<', Carbon::today())->whereNull('visits.done_datetime');
            } elseif ($request->dashboard_filters == 'recce_done_this_month') {
                $from = Carbon::today()->startOfMonth();
                $to = Carbon::today()->endOfMonth();
                $visits->whereBetween('visits.done_datetime', [$from, $to]);
            }
        }

        $visits = $visits->get();

        return datatables($visits)->toJson();
    }
}
