<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\nvLeadForward;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NvLeadController extends Controller {
    private $current_timestamp;
    public function __construct() {
        $this->current_timestamp = date('Y-m-d H:i:s');
    }
    public function list(Request $request, $dashboard_filters = null) {
        $filter_params = "";
        if ($request->lead_status != null) {
            $filter_params =  ['lead_status' => $request->lead_status];
        }
        if ($request->lead_read_status != null) {
            $filter_params = ['lead_read_status' => $request->lead_read_status];
        }
        if ($request->service_status != null) {
            $filter_params = ['service_status' => $request->service_status];
        }
        if ($request->has_rm_message != null) {
            $filter_params = ['has_rm_message' => $request->has_rm_message];
        }
        if ($request->event_from_date != null) {
            $filter_params = ['event_from_date' => $request->event_from_date, 'event_to_date' => $request->event_to_date];
        }
        if ($request->lead_from_date != null) {
            $filter_params = ['lead_from_date' => $request->lead_from_date, 'lead_to_date' => $request->lead_to_date];
        }
        if ($request->lead_done_from_date != null) {
            $filter_params = ['lead_done_from_date' => $request->lead_done_from_date, 'lead_done_to_date' => $request->lead_done_to_date];
        }

        $page_heading = $filter_params ? "Leads - Filtered" : "Leads";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }
        return view('vendor.lead.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('vendor')->user();
        $leads = nvLeadForward::select(
            'nv_lead_forwards.lead_id',
            'nv_lead_forwards.lead_datetime as lead_date',
            'nv_lead_forwards.name',
            'nv_lead_forwards.mobile',
            'nv_lead_forwards.lead_status',
            'nv_lead_forwards.event_datetime as event_date',
            'nv_lead_forwards.read_status',
            'ne.pax as pax',
        )->leftJoin('nv_events as ne', 'ne.lead_id', 'nv_lead_forwards.lead_id')
        ->where(['forward_to' => $auth_user->id]);

        if ($request->lead_status != null) {
            $leads->where('lead_status', $request->lead_status);
        } elseif ($request->lead_read_status != null) {
            $leads->where('read_status', '=', $request->lead_read_status);
        } elseif ($request->event_from_date != null) {
            $from =  Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $leads->whereBetween('event_datetime', [$from, $to]);
        } elseif ($request->lead_from_date != null) {
            $from =  Carbon::make($request->lead_from_date);
            if ($request->lead_to_date != null) {
                $to = Carbon::make($request->lead_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->lead_from_date)->endOfDay();
            }
            $leads->whereBetween('lead_datetime', [$from, $to]);
        } elseif ($request->lead_done_from_date != null) {
            $from =  Carbon::make($request->lead_done_from_date);
            if ($request->lead_done_to_date != null) {
                $to = Carbon::make($request->lead_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->lead_done_from_date)->endOfDay();
            }
            $leads->where('lead_status', 'Done')->whereBetween('nv_lead_forwards.updated_at', [$from, $to]);
        } elseif ($request->has_rm_message != null) {
            if ($request->has_rm_message == "yes") {
                $leads->join('nvrm_messages as rm_msg', 'nv_lead_forwards.lead_id', '=', 'rm_msg.lead_id');
            } else {
                $leads->leftJoin('nvrm_messages as rm_msg', 'nv_lead_forwards.lead_id', '=', 'rm_msg.lead_id')->where('rm_msg.title', null);
            }
        } elseif ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "leads_of_the_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $leads->whereBetween('nv_lead_forwards.lead_datetime', [$from, $to]);
            } elseif ($request->dashboard_filters == "leads_of_the_day") {
                $from =  Carbon::today()->startOfDay();
                $to =  Carbon::today()->endOfDay();
                $leads->whereBetween('nv_lead_forwards.lead_datetime', [$from, $to]);
            } elseif ($request->dashboard_filters == "unreaded_leads") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $leads->where('nv_lead_forwards.read_status', false);
            }
        }

        $leads = $leads->get();
        return datatables($leads)->toJson();
    }

    public function view($lead_id) {
        $auth_user = Auth::guard('vendor')->user();
        $lead_forward = nvLeadForward::where(['forward_to' => $auth_user->id, 'lead_id' => $lead_id])->first();;
        if (!$lead_forward) {
            abort(404);
        }
        return view('vendor.lead.view', compact('lead_forward'));
    }


    public function status_update(Request $request, $forward_id, $status = "Done") {
        $auth_user = Auth::guard('vendor')->user();
        $lead_forward = nvLeadForward::where(['id' => $forward_id, 'forward_to' => $auth_user->id])->first();

        if (!$lead_forward) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
            return redirect()->back();
        }

        if ($status == "Active") {
            $lead_forward->lead_status = "Active";
            $lead_forward->read_status = false;
            $lead_forward->done_title = null;
            $lead_forward->done_message = null;
        }else if ($status == "Booked"){
            $lead_forward->lead_status = "Booked";
            $lead_forward->read_status = true;
            $lead_forward->done_title = null;
            $lead_forward->done_message = null;
            session(['show_congratulations' => true]);
        } else {
            $validate = Validator::make($request->all(), [
                'forward_id' => 'required|exists:nv_lead_forwards,id',
                'done_title' => 'required|string',
            ]);

            if ($validate->fails()) {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
                return redirect()->back();
            }

            $lead_forward->lead_status = "Done";
            $lead_forward->read_status = true;
            $lead_forward->done_title = $request->done_title;
            $lead_forward->done_message = $request->done_message;
        }

        $lead_forward->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead status updated."]);
        return redirect()->back();
    }
}
