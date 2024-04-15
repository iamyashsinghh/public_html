<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\LeadForwardInfo;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller {
    private $current_timestamp;
    public function __construct() {
        $this->current_timestamp = date('Y-m-d H:i:s');
    }

    public function list(Request $request) { //done
        $auth_user = Auth::guard('manager')->user();
        $vm_members = TeamMember::select('id', 'name', 'venue_name')->where('parent_id', $auth_user->id)->orderBy('name', 'asc')->get();

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
        if ($request->pax_from != null) {
            $filter_params = ['pax_from' => $request->pax_from, 'pax_to' => $request->pax_to];
        }

        $page_heading = $filter_params ? "Leads - Filtered" : "Leads";
        return view('manager.venueCrm.lead.list', compact('page_heading', 'filter_params', 'vm_members'));
    }

    public function ajax_list(Request $request) { //done
        $auth_user = Auth::guard('manager')->user();

        $vm_members = TeamMember::select('id', 'name', 'venue_name')->where('parent_id', $auth_user->id)->get();
        $vms_id = [];
        foreach ($vm_members as $list) {
            array_push($vms_id, $list->id);
        }

        $leads = LeadForward::select(
            'leads.lead_id',
            'leads.lead_datetime',
            'leads.name',
            'leads.mobile',
            'leads.lead_status',
            'leads.event_datetime',
            'leads.pax',
            DB::raw("count(lead_forwards.id) as forwarded_count"),
        )->join('leads', 'lead_forwards.lead_id', 'leads.lead_id')
            ->whereIn('forward_to', $vms_id);

        if ($request->lead_status != null) {
            $leads->where('leads.lead_status', $request->lead_status)->where('leads.lead_id', '>', 0);
        } elseif ($request->event_from_date != null) {
            $from =  Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $leads->whereBetween('leads.event_datetime', [$from, $to])->orderBy('leads.event_datetime', 'asc');
        } elseif ($request->lead_from_date != null) {
            $from =  Carbon::make($request->lead_from_date);
            if ($request->lead_to_date != null) {
                $to = Carbon::make($request->lead_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->lead_from_date)->endOfDay();
            }
            $leads->whereBetween('leads.lead_datetime', [$from, $to])->orderBy('leads.lead_datetime', 'asc');
        } elseif ($request->has_rm_message != null) {
            if ($request->has_rm_message == "yes") {
                $leads->join('rm_messages as rm_msg', 'leads.lead_id', '=', 'rm_msg.lead_id');
            } else {
                $leads->leftJoin('rm_messages as rm_msg', 'leads.lead_id', '=', 'rm_msg.lead_id')->where('rm_msg.title', null);
            }
            $leads->orderBy('lead_datetime', 'desc');
        } elseif ($request->pax_from != null) {
            if ($request->pax_to != null) {
                $pax_range = [$request->pax_from, $request->pax_to];
                $leads->whereBetween('leads.pax', $pax_range);
            } else {
                $leads->where('leads.pax', '>', $request->pax_from - 1);
            }
        } else {
            $leads->orderBy('lead_datetime', 'desc');
        }

        $leads = $leads->groupBy('leads.lead_id')->get();
        return datatables($leads)->toJson();
    }

    // public function add_process(Request $request) {
    //     $is_name_valid = $request->name !== null ? "required|string|max:255" : "";
    //     $is_email_valid = $request->email !== null ? "required|email" : "";
    //     $is_alt_mobile_valid = $request->alternate_mobile_number !== null ? "required|digits:10|" : "";
    //     $validate = Validator::make($request->all(), [
    //         'name' => $is_name_valid,
    //         'email' => $is_email_valid,
    //         'alternate_mobile_number' => $is_alt_mobile_valid,
    //         'mobile_number' => "required|digits:10",
    //         'lead_source' => 'required',
    //         'lead_status' => 'required',
    //         'event_date' => 'required|date'
    //     ]);

    //     if ($validate->fails()) {
    //         session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
    //         return redirect()->back();
    //     }

    //     $auth_user = Auth::guard('manager')->user();
    //     $exist_lead = Lead::where('mobile', $request->mobile_number)->first();
    //     if ($exist_lead) { // Here we are finding the lead with same mobile number and just only adding event information.
    //         $lead_link = route('manager.lead.view', $exist_lead->lead_id);
    //         session()->flash('status', ['success' => true, 'alert_type' => 'info', 'message' => "Lead is already exist with this mobile number. Click on the link below to view the lead. <a href='$lead_link'><b>$lead_link</b></a>"]);

    //         return redirect()->back();
    //     }

    //     $lead = new Lead();
    //     $lead->created_by = $auth_user->id;
    //     $lead->lead_datetime = $this->current_timestamp;
    //     $lead->name = $request->name;
    //     $lead->email = $request->email;
    //     $lead->mobile = $request->mobile_number;
    //     $lead->alternate_mobile = $request->alternate_mobile_number;
    //     $lead->source = $request->lead_source;
    //     $lead->locality = $request->locality;
    //     $lead->lead_status = $request->lead_status;
    //     $lead->event_datetime = $request->event_date . " " . date('H:i:s');
    //     $lead->pax = $request->number_of_guest;
    //     $lead->save();

    //     $event = new Event();
    //     $event->created_by = $auth_user->id;
    //     $event->lead_id = $lead->lead_id;
    //     $event->event_name = $request->event_name;
    //     $event->event_datetime = $request->event_date . " " . date('H:i:s');
    //     $event->pax = $request->number_of_guest;
    //     $event->budget = $request->budget;
    //     $event->food_Preference = $request->food_Preference;
    //     $event->event_slot = $request->event_slot;
    //     $event->save();

    //     session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead added successfully."]);
    //     return redirect()->back();
    // }

    // public function edit_process(Request $request, $lead_id) {
    //     $lead = Lead::find($lead_id);
    //     if (!$lead) {
    //         session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
    //         return redirect()->back();
    //     }

    //     $lead->name = $request->name;
    //     $lead->email = $request->email;
    //     $lead->alternate_mobile = $request->alternate_mobile_number;
    //     $lead->source = $request->lead_source;
    //     $lead->locality = $request->locality;
    //     $lead->lead_status = $request->lead_status;
    //     $lead->save();

    //     session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead updated successfully.']);
    //     return redirect()->back();
    // }

    public function view($lead_id) { //done
        $auth_user = Auth::guard('manager')->user();
        $lead = Lead::find($lead_id);
        $vm_members = TeamMember::select('id', 'name', 'venue_name')->where('parent_id', $auth_user->id)->orderBy('name', 'asc')->get();
        $vms_id = [];
        foreach ($vm_members as $list) {
            array_push($vms_id, $list->id);
        }
        $leads_forward = LeadForward::where('lead_id', $lead->lead_id)->whereIn('forward_to', $vms_id);
        $visits = $lead->get_visits->whereIn('created_by', $vms_id);
        $tasks = $lead->get_tasks->whereIn('created_by', $vms_id);
        $notes = $lead->get_notes->whereIn('created_by', $vms_id);
        $bookings = $lead->get_bookings->whereIn('created_by', $vms_id);

        return view('manager.venueCrm.lead.view', compact('lead', 'leads_forward', 'visits', 'tasks', 'bookings', 'notes', 'vm_members'));
    }

    public function lead_forward(Request $request) {
        $validate = Validator::make($request->all(), [
            'forward_leads_id' => 'required',
            'forward_vms_id' => 'required',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $leads_id = explode(',', $request->forward_leads_id);
        $leads = Lead::whereIn('lead_id', $leads_id)->get();
        $auth_user = Auth::guard('manager')->user();

        foreach ($leads as $lead) {
            foreach ($request->forward_vms_id as $vm_id) {
                $exist_lead_forward = LeadForward::where(['lead_id' => $lead->lead_id, 'forward_to' => $vm_id])->first();
                if ($exist_lead_forward) {
                    $lead_forward = $exist_lead_forward;
                } else {
                    $lead_forward = new LeadForward();
                    $lead_forward->lead_id = $lead->lead_id;
                    $lead_forward->forward_to = $vm_id;
                }
                $lead_forward->lead_datetime = $this->current_timestamp;
                $lead_forward->name = $lead->name;
                $lead_forward->email = $lead->email;
                $lead_forward->mobile = $lead->mobile;
                $lead_forward->alternate_mobile = $lead->alternate_mobile;
                $lead_forward->source = $lead->source;
                $lead_forward->locality = $lead->locality;
                $lead_forward->lead_status = $lead->lead_status;
                $lead_forward->read_status = false;
                $lead_forward->service_status = false;
                $lead_forward->done_title = null;
                $lead_forward->done_message = null;
                $lead_forward->event_datetime = $lead->event_datetime;
                $lead_forward->save();

                //update or insert lead_forward_info table
                $lead_forward_info = LeadForwardInfo::where(['lead_id' => $lead->lead_id, 'forward_from' => $auth_user->id, 'forward_to' => $vm_id])->first();
                if (!$lead_forward_info) {
                    $lead_forward_info = new LeadForwardInfo();
                    $lead_forward_info->lead_id = $lead->lead_id;
                    $lead_forward_info->forward_from = $auth_user->id;
                    $lead_forward_info->forward_to = $vm_id;
                }
                $lead_forward_info->updated_at = $this->current_timestamp;
                $lead_forward_info->save();
            }
        }

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead's Forwarded successfully."]);
        return redirect()->back();
    }

    public function get_forward_info($lead_id = 0) { //done
        try {
            $auth_user = Auth::guard('manager')->user();

            $lead_forwards = LeadForward::select(
                'tm.name',
                'tm.venue_name',
                'lead_forwards.read_status',
                'lead_forwards.lead_datetime',
            )->join('team_members as tm', 'lead_forwards.forward_to', 'tm.id')->where(['lead_forwards.lead_id' => $lead_id, 'tm.parent_id' => $auth_user->id])->orderBy('lead_forwards.updated_at', 'desc')->get();

            $lead_forward_info = LeadForwardInfo::where(['lead_id' => $lead_id])->orderBy('updated_at', 'desc')->first();
            if ($lead_forward_info) {
                $last_forwarded_info = "Last forwarded by: " . $lead_forward_info->get_forward_from->name . " (" . $lead_forward_info->get_forward_from->get_role->name . ")" . " @ " . date('d-M-Y h:i a', strtotime($lead_forward_info->updated_at));
            } else {
                $last_forwarded_info = "Last forwarded by: N/A";
            }

            return response()->json(['success' => true, 'lead_forwards' => $lead_forwards, 'last_forwarded_info' => $last_forwarded_info]);
        } catch (\Throwable $th) {
            return response()->json(['success' => true, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'error' => $th]);
        }
    }
}
