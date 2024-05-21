<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VisitController extends Controller {
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
        if ($request->pax_min_value != null) {
            $filter_params = ['pax_min_value' => $request->pax_min_value, 'pax_max_value' => $request->pax_max_value];
        }

        $page_heading = $filter_params ? "Visits - Filtered" : "Visits";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }

        return view('team.venueCrm.visit.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('team')->user();
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
            'ne.pax as pax',
        )->join('visits', ['lead_forwards.visit_id' => 'visits.id'])
        ->leftJoin('vm_events as ne', 'ne.lead_id', '=', 'lead_forwards.lead_id')
        ->where(['lead_forwards.forward_to' => $auth_user->id, 'visits.deleted_at' => null])->groupBy('lead_forwards.lead_id');

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
        }elseif ($request->pax_min_value != null) {
            $min = $request->pax_min_value;
            if ($request->pax_max_value != null) {
                $max = $request->pax_max_value;
            } else {
                $max = $request->pax_min_value;
            }
            $visits->whereBetween('ne.pax', [$min, $max]);
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

        // return $visits->toSql();
        $visits = $visits->get();
        return datatables($visits)->toJson();
    }

    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,lead_id',
            'visit_event_name' => 'required|string',
            'visit_schedule_datetime' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('team')->user();
        if ($auth_user->role_id == 4) {
            $exist_visit = Visit::join('team_members as tm', ['tm.id' => 'visits.created_by'])->where(['visits.lead_id' => $request->lead_id, 'visits.done_datetime' => null, 'tm.role_id' => 4])->first();
        } else {
            $exist_visit = Visit::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        }

        if ($exist_visit) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active visit, please complete or delete it first.']);
            return redirect()->back();
        }

        $vm_visits_id_arr = [];
        if ($request->visit_venue !== null && sizeof($request->visit_venue) > 0) {
            foreach ($request->visit_venue as $member_id) {
                $refer_visit = new Visit();
                $refer_visit->lead_id = $request->lead_id;
                $refer_visit->created_by = $member_id;
                $refer_visit->event_name = $request->visit_event_name;
                $refer_visit->visit_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->visit_schedule_datetime));
                $refer_visit->message = $request->visit_message;
                $refer_visit->referred_by = $auth_user->id;
                $refer_visit->save();
                array_push($vm_visits_id_arr, $refer_visit->id);

                $lead_forwards = LeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $member_id])->first();
                $lead_forwards->visit_id = $refer_visit->id;
                $lead_forwards->save();
            }
        }

        $visit = new Visit();
        $visit->lead_id = $request->lead_id;
        $visit->created_by = $auth_user->id;
        if (sizeof($vm_visits_id_arr) > 0) {
            $visit->vm_visits_id = implode(",", $vm_visits_id_arr);
        }

        $visit->event_name = $request->visit_event_name;
        $visit->visit_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->visit_schedule_datetime));
        $visit->message = $request->visit_message;
        $visit->save();

        if ($auth_user->role_id == 4) {
            $lead = Lead::find($request->lead_id);
        } else {
            $lead = LeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $auth_user->id])->first();
            $lead->read_status = true;
        }
        $lead->visit_id = $visit->id;
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Visit created successfully.']);
        return redirect()->back();
    }

    public function get_forward_info($visit_id) {
        $auth_user = Auth::guard('team')->user();
        try {
            $visit = Visit::where(['id' => $visit_id, 'created_by' => $auth_user->id])->first();
            $shared_visit_ids = explode(",", $visit->vm_visits_id);
            $visit_forwards = Visit::select(
                'tm.name',
                'tm.venue_name',
            )->join('team_members as tm', 'visits.created_by', '=', 'tm.id')->whereIn('visits.id', $shared_visit_ids)->withTrashed()->get();
            return response()->json(['success' => true, 'visit_forwards' => $visit_forwards]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function delete($visit_id) {
        $auth_user = Auth::guard('team')->user();
        $visit = Visit::where(['id' => $visit_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();

        if (!$visit) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }

        if ($auth_user->role_id == 4) {
            $share_visit_ids = explode(",", $visit->vm_visits_id);
            $rm_visits = Visit::whereIn('id', $share_visit_ids);
            $rm_visits->delete();
        }

        $visit->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Visit Deleted.']);
        return redirect()->back();
    }

    public function status_update(Request $request, $visit_id) {
        $validate = Validator::make($request->all(), [
            'event_date' => 'required|date',
            'party_area' => 'required|string',
            'menu_selected' => 'required|string',
            'price_quoted' => 'required|int',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
        }

        $auth_user = Auth::guard('team')->user();
        $visit = Visit::where(['id' => $visit_id, 'created_by' => $auth_user->id])->first();
        if (!$visit) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
        $visit->event_datetime = $request->event_date . " " . date('H:i:s');
        $visit->menu_selected = $request->menu_selected;
        $visit->party_area = $request->party_area;
        $visit->price_quoted = $request->price_quoted;
        $visit->done_message = $request->done_message;
        $visit->done_datetime = date('Y-m-d H:i:s');
        $visit->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Visit status updated.']);
        return redirect()->back();
    }

    public function rm_visit_status_update($visit_id) {
        $auth_user = Auth::guard('team')->user();
        $visit = Visit::where(['id' => $visit_id, 'created_by' => $auth_user->id])->first();
        if ($visit == null || $auth_user->role_id !== 4) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
        $visit->done_datetime = date('Y-m-d H:i:s');
        $visit->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Visit status updated.']);
        return redirect()->back();
    }
}
