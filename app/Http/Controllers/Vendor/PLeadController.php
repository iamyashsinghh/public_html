<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PVendorEvent;
use App\Models\PVendorLead;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class PLeadController extends Controller
{
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
        return view('vendor.plead.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('vendor')->user();
        $leads = PVendorLead::select(
            'p_vendor_leads.id',
            'p_vendor_leads.lead_datetime as lead_date',
            'p_vendor_leads.name',
            'p_vendor_leads.mobile',
            'p_vendor_leads.lead_status',
            'p_vendor_leads.event_datetime as event_date',
            'p_vendor_leads.read_status',
        )->where(['forward_to' => $auth_user->id]);

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
            $leads->where('lead_status', 'Done')->whereBetween('p_vendor_leads.updated_at', [$from, $to]);
        } elseif ($request->has_rm_message != null) {
            if ($request->has_rm_message == "yes") {
                $leads->join('nvrm_messages as rm_msg', 'p_vendor_leads.lead_id', '=', 'rm_msg.lead_id');
            } else {
                $leads->leftJoin('nvrm_messages as rm_msg', 'p_vendor_leads.lead_id', '=', 'rm_msg.lead_id')->where('rm_msg.title', null);
            }
        } elseif ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "leads_of_the_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $leads->whereBetween('p_vendor_leads.lead_datetime', [$from, $to]);
            } elseif ($request->dashboard_filters == "leads_of_the_day") {
                $from =  Carbon::today()->startOfDay();
                $to =  Carbon::today()->endOfDay();
                $leads->whereBetween('p_vendor_leads.lead_datetime', [$from, $to]);
            } elseif ($request->dashboard_filters == "unreaded_leads") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $leads->where('p_vendor_leads.read_status', false);
            }
        }

        $leads = $leads->get();
        return datatables($leads)->toJson();
    }

    public function edit_process(Request $request, $forward_id)
    {
        $forward = PVendorLead::find($forward_id);
        $forward->name = $request->name;
        $forward->email = $request->email;
        $forward->alternate_mobile = $request->alternate_mobile_number;
        $forward->address = $request->address;
        $forward->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead updated successfully.']);
        return redirect()->back();
    }

    public function add_process(Request $request)
    {
        $is_name_valid = $request->name !== null ? "required|string|max:255" : "";
        $is_email_valid = $request->email !== null ? "required|email" : "";
        $is_alt_mobile_valid = $request->alternate_mobile_number !== null ? "required|digits:10|" : "";
        $is_pax_valid = $request->number_of_guest !== null ? "required|int" : "";
        $validate = Validator::make($request->all(), [
            'name' => $is_name_valid,
            'email' => $is_email_valid,
            'alternate_mobile_number' => $is_alt_mobile_valid,
            'mobile_number' => "required|digits:10",
            'number_of_guest' => $is_pax_valid,
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $exist_lead = PVendorLead::where('mobile', $request->mobile_number)->where('forward_to', $auth_user->id)->first();
        if ($exist_lead) {
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead added successfully."]);
            return redirect()->back();
        }

        $lead = new PVendorLead();
        $lead->created_by = $auth_user->id;
        $lead->forward_to = $auth_user->id;
        $lead->lead_datetime = $this->current_timestamp;
        $lead->name = $request->name;
        $lead->lead_status = 'Active';
        $lead->email = $request->email;
        $lead->mobile = $request->mobile_number;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->address = $request->address;
        $lead->event_datetime = $request->event_date ? $request->event_date . " " . date('H:i:s') : '';
        $lead->save();

        if ($request->event_date != null) {
            $event = new PVendorEvent();
            $event->created_by = $auth_user->id;
            $event->lead_id = $lead->id;
            $event->event_name = $request->event_name;
            $event->event_datetime = $request->event_date . " " . date('H:i:s');
            $event->pax = $request->number_of_guest;
            $event->event_slot = $request->event_slot;
            $event->venue_name = $request->venue_name;
            $event->save();
        }

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead added successfully."]);
        return redirect()->back();
    }

    public function view($lead_id) {
        $auth_user = Auth::guard('vendor')->user();
        $lead_forward = PVendorLead::where(['forward_to' => $auth_user->id, 'id' => $lead_id])->first();;
        if (!$lead_forward) {
            abort(404);
        }
        return view('vendor.plead.view', compact('lead_forward'));
    }


    public function status_update(Request $request, $forward_id, $status = "Done") {
        $auth_user = Auth::guard('vendor')->user();
        $lead_forward = PVendorLead::where(['id' => $forward_id, 'forward_to' => $auth_user->id])->first();

        if (!$lead_forward) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
            return redirect()->back();
        }

        if ($status == "Active") {
            $lead_forward->lead_status = "Active";
            $lead_forward->read_status = false;
            $lead_forward->done_title = null;
            $lead_forward->done_message = null;
        } else if ($status == "Booked"){
            $lead_forward->lead_status = "Booked";
            $lead_forward->read_status = true;
            $lead_forward->done_title = null;
            $lead_forward->done_message = null;
            session(['show_congratulations' => true]);
        } else {
            $validate = Validator::make($request->all(), [
                'forward_id' => 'required|exists:p_vendor_leads,id',
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
