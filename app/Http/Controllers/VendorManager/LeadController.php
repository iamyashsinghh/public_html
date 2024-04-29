<?php

namespace App\Http\Controllers\VendorManager;

use App\Http\Controllers\Controller;
use App\Models\nvLead;
use App\Models\nvLeadForward;
use App\Models\nvLeadForwardInfo;
use App\Models\nvrmLeadForward;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller {
    private $current_timestamp;
    public function __construct() {
        $this->current_timestamp = date('Y-m-d H:i:s');
    }

    public function list(Request $request) {
        $auth_user = Auth::guard('vendormanager')->user();
        $v_members = Vendor::select('id', 'name', 'business_name')->where('parent_id', $auth_user->id)->get();

        $filter_params = "";
        if ($request->lead_status != null) {
            $filter_params =  ['lead_status' => $request->lead_status];
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

        $page_heading = $filter_params ? "Leads - Filtered" : "Leads";
        return view('vendormanager.vendorCrm.lead.list', compact('page_heading', 'filter_params', 'v_members'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('vendormanager')->user();

        $v_members = Vendor::select('id', 'name', 'business_name')->where('parent_id', $auth_user->id)->get();
        $vs_id = [];
        foreach ($v_members as $list) {
            array_push($vs_id, $list->id);
        }

        $leads = nvLeadForward::select(
            'nv_lead_forwards.lead_id',
            'nv_lead_forwards.lead_datetime as lead_date',
            'nv_lead_forwards.name',
            'nv_lead_forwards.mobile',
            'nv_lead_forwards.lead_status',
            'nv_lead_forwards.event_datetime as event_date',
            'nv_lead_forwards.read_status',
            'ne.pax as pax',
            DB::raw("(SELECT COUNT(*) FROM nv_lead_forwards AS sub WHERE sub.lead_id = nv_lead_forwards.lead_id) as forwarded_count")
        )
        ->leftJoin('nv_events as ne', 'ne.lead_id', '=', 'nv_lead_forwards.lead_id')
        ->whereIn('nv_lead_forwards.forward_to', $vs_id)
        ->groupBy('nv_lead_forwards.lead_id');

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
        $auth_user = Auth::guard('vendormanager')->user();
        $lead =  nvLead::find($lead_id);
        if (!$lead) {
            abort(404);
        }
        $v_members = Vendor::select('id', 'name', 'business_name')->where('parent_id', $auth_user->id)->get();
        $vs_id = [];
        foreach ($v_members as $list) {
            array_push($vs_id, $list->id);
        }
        $nv_forwarded_count = nvLeadForward::where('lead_id', $lead_id)->count();
        $forwarded_count = $nv_forwarded_count;
        return view('vendormanager.vendorCrm.lead.view', compact('lead', 'forwarded_count','v_members'));
    }

    public function lead_forward(Request $request)
    {
        $nv_lead_forword = nvLeadForward::where('lead_id', $request->forward_id)->first();
        if(!$nv_lead_forword){
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Lead not found.']);
        }
        $auth_user = Auth::guard('vendormanager')->user();
        foreach($request->forward_vendors_id as $fvi){

            $exist_lead_forward = nvLeadForward::where(['lead_id' => $nv_lead_forword->lead_id, 'forward_to' => $fvi])->first();
            if ($exist_lead_forward) {
                $nvLeadForward = $exist_lead_forward;
            } else {

            $nvLeadForward = new nvLeadForward();
            $nvLeadForward->lead_id = $nv_lead_forword->lead_id;
            $nvLeadForward->forward_to = $fvi;
            }

            $nvLeadForward->lead_datetime = $this->current_timestamp;
            $nvLeadForward->name = $nv_lead_forword->name;
            $nvLeadForward->email = $nv_lead_forword->email;
            $nvLeadForward->mobile = $nv_lead_forword->mobile;
            $nvLeadForward->alternate_mobile = $nv_lead_forword->alternate_mobile;
            $nvLeadForward->address = $nv_lead_forword->address;
            $nvLeadForward->lead_status = $nv_lead_forword->lead_status;
            $nvLeadForward->event_datetime = $nv_lead_forword->event_datetime;
            $nvLeadForward->read_status = false;
            $nvLeadForward->done_title = null;
            $nvLeadForward->done_message = null;
            $nvLeadForward->save();

            $lead_forward_info = nvLeadForwardInfo::where(['lead_id' => $nv_lead_forword->lead_id, 'forward_from' => $auth_user->id, 'forward_to' => $fvi])->first();
            if (!$lead_forward_info) {
                $lead_forward_info = new nvLeadForwardInfo();
                $lead_forward_info->lead_id = $nv_lead_forword->lead_id;
                $lead_forward_info->forward_from = $auth_user->id;
                $lead_forward_info->forward_to = $fvi;
            }
            $lead_forward_info->updated_at = $this->current_timestamp;
            $lead_forward_info->save();

        }
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead forwarded successfully.']);
        return redirect()->back();
    }

    public function get_forward_info($lead_id = 0) {
        try {
            // $nvrm_forwards = nvrmLeadForward::select(
            //     'tm.name',
            //     'r.name as role_name',
            //     'nvrm_lead_forwards.read_status'
            // )->leftJoin('team_members as tm', 'nvrm_lead_forwards.forward_to', '=', 'tm.id')->leftJoin('roles as r', 'tm.role_id', '=', 'r.id')
            //     ->where(['nvrm_lead_forwards.lead_id' => $lead_id])->groupBy('nvrm_lead_forwards.forward_to')->orderBy('nvrm_lead_forwards.lead_datetime', 'desc')->get()->toArray();

            $nv_forwards = nvLeadForward::select(
                'v.name',
                'v.business_name',
                'nv_lead_forwards.read_status'
            )->leftJoin('vendors as v', 'nv_lead_forwards.forward_to', '=', 'v.id')
                ->where(['nv_lead_forwards.lead_id' => $lead_id])->groupBy('nv_lead_forwards.forward_to')->orderBy('nv_lead_forwards.lead_datetime', 'desc')->get()->toArray();

            $lead_forwards = array_merge($nv_forwards);
            rsort($lead_forwards);

            $lead_forward_info = nvLeadForwardInfo::where(['lead_id' => $lead_id])->orderBy('updated_at', 'desc')->first();
            if ($lead_forward_info) {
                $last_forwarded_info = "Last forwarded by: " . $lead_forward_info->get_forward_from->name . " to " . $lead_forward_info->get_forward_to->name . " @ " . date('d-M-Y h:i a', strtotime($lead_forward_info->updated_at));
            } else {
                $last_forwarded_info = "Last forwarded by: N/A";
            }

            return response()->json(['success' => true, 'lead_forwards' => $lead_forwards, 'last_forwarded_info' => $last_forwarded_info]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'error' => $th->getMessage()]);
        }
    }
}
