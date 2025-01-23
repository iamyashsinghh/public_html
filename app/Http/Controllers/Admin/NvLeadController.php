<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\nvEvent;
use App\Models\nvLead;
use App\Models\nvLeadForward;
use App\Models\nvLeadForwardInfo;
use App\Models\nvrmLeadForward;
use App\Models\TeamMember;
use App\Models\WhatsappCampain;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NvLeadController extends Controller
{
    private $current_timestamp;
    public function __construct()
    {
        $this->current_timestamp = date('Y-m-d H:i:s');
    }

    public function list(Request $request)
    {
        $filter_params = "";
        if ($request->lead_status != null) {
            $filter_params = ['lead_status' => $request->lead_status];
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

        $nvrm_members = TeamMember::select('id', 'name')->where('role_id', 3)->orderBy('name', 'asc')->get(); //role_id:3 = NVRM;
        $page_heading = $filter_params ? "NV Leads - Filtered" : "NV Leads";
        $getRm = TeamMember::select('id', 'name')->where('venue_name', 'RM >< Non Venue')->get();
        $whatsapp_campaigns = WhatsappCampain::where('status', 1)->get();
        return view('admin.nonVenueCrm.nvlead.list', compact('page_heading', 'filter_params', 'nvrm_members', 'whatsapp_campaigns', 'getRm'));
    }

    public function ajax_list(Request $request)
    {
        $leads = DB::table('nv_leads')->select(
            'nv_leads.id',
            'nv_leads.lead_datetime',
            'nv_leads.name',
            'nv_leads.mobile',
            'nv_leads.event_datetime',
            'nv_leads.is_whatsapp_msg',
            'nv_leads.whatsapp_msg_time',
            'tm.name as team_name',
            'roles.name as team_role',
            DB::raw("(select count(nvrm_fwd.id) from nvrm_lead_forwards as nvrm_fwd where nvrm_fwd.lead_id = nv_leads.id group by nvrm_fwd.lead_id) as nvrm_forwarded_count"),
            DB::raw("(select count(nv_fwd.id) from nv_lead_forwards as nv_fwd where nv_fwd.lead_id = nv_leads.id group by nv_fwd.lead_id) as nv_forwarded_count"),
            DB::raw("(select count(fwd_info.id) from nv_lead_forward_infos as fwd_info where fwd_info.lead_id = nv_leads.id) as forwarded_info_count"),
            DB::raw("(select tm_forward_to.name from team_members as tm_forward_to where tm_forward_to.id = nvrm_lf.forward_to) as forward_to"),
            'nvrm_lf.last_forwarded_by',
            'nvrm_lf.service_status',
            'nvrm_lf.lead_status',
            'ne.pax as pax',
        )->leftJoin('team_members as tm', 'nv_leads.created_by', 'tm.id')
            ->leftJoin('roles', 'tm.role_id', 'roles.id')
            ->leftJoin('nv_events as ne', 'ne.lead_id', '=', 'nv_leads.id')
            ->leftJoin('nvrm_lead_forwards as nvrm_lf', 'nv_leads.id', 'nvrm_lf.lead_id')
            ->groupBy('nv_leads.mobile');

        if ($request->event_from_date != null) {
            $from = Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $leads->whereBetween('nv_leads.event_datetime', [$from, $to])->orderBy('nv_leads.event_datetime', 'asc');
        }
        if ($request->lead_from_date != null) {
            $from = Carbon::make($request->lead_from_date);
            if ($request->lead_to_date != null) {
                $to = Carbon::make($request->lead_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->lead_from_date)->endOfDay();
            }
            $leads->whereBetween('nv_leads.lead_datetime', [$from, $to])->orderBy('nv_leads.lead_datetime', 'asc');
        }
        if ($request->has_rm_message != null) {
            if ($request->has_rm_message == "yes") {
                $leads->join('nvrm_messages as nvrm_msg', 'nv_leads.id', '=', 'nvrm_msg.lead_id');
            } else {
                $leads->leftJoin('nvrm_messages as nvrm_msg', 'nv_leads.id', '=', 'nvrm_msg.lead_id')->where('nvrm_msg.title', null);
            }
        }

        if ($request->has('lead_status') && $request->lead_status != '') {
            $leads->where('nvrm_lf.lead_status', $request->lead_status);
        }

        if ($request->lead_read_status != null) {
            $leads->where('nvrm_lf.read_status', $request->lead_read_status);
        }

        if ($request->pax_min_value != null) {
            $min =  $request->pax_min_value;
            if ($request->pax_max_value != null) {
                $max = $request->pax_max_value;
            } else {
                $max = $request->pax_min_value;
            }
            $leads->whereBetween('ne.pax', [$min, $max]);
        }

        if ($request->service_status != null) {
            if ($request->service_status == 0) {
                $leads->where(function ($query) use ($request) {
                    $query->where('nvrm_lf.service_status', $request->service_status)
                        ->orWhereNull('nvrm_lf.service_status');
                });
            } else {
                $leads->where('nvrm_lf.service_status', $request->service_status);
            }
        }

        if ($request->lead_done_from_date != null) {
            $from = Carbon::make($request->lead_done_from_date);
            if ($request->lead_done_to_date != null) {
                $to = Carbon::make($request->lead_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->lead_done_from_date)->endOfDay();
            }
            $leads->where('nvrm_lf.lead_status', 'Done')->whereBetween('nvrm_lf.updated_at', [$from, $to]);
        }

        if ($request->team_members != null) {
            $leads->whereIn('nvrm_lf.forward_to', $request->team_members);
        }

        // $leads->orderBy('nv_leads.lead_datetime', 'desc');
        $leads = $leads->whereNull('nv_leads.deleted_at')->get();
        $unresolvedNotesQuery = DB::table('nv_notes')
            ->select('lead_id', 'vendors.name as vendor_name', DB::raw('COUNT(nv_notes.id) as unresolved_count'))
            ->join('vendors', 'nv_notes.created_by', '=', 'vendors.id')
            ->where('nv_notes.is_solved', 0)
            ->groupBy('nv_notes.lead_id', 'vendors.name');
        $unresolvedNotes = $unresolvedNotesQuery->get()->groupBy('lead_id');
        foreach ($leads as $lead) {
            $notesForLead = collect($unresolvedNotes->get($lead->id, []));
            $formattedNotes = $notesForLead->map(function ($item) {
                return "{$item->vendor_name} -- {$item->unresolved_count}";
            })->implode(', ');
            $lead->unresolved_notes = $formattedNotes;
        }
        return datatables($leads)->toJson();
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
        $auth_user = Auth::guard('admin')->user();
        $exist_lead = nvLead::where('mobile', $request->mobile_number)->first();
        if ($exist_lead) {
            $lead_link = route('admin.nvlead.view', $exist_lead->id);
            session()->flash('status', ['success' => true, 'alert_type' => 'info', 'message' => "Lead is already exist with this mobile number. Click on the link below to view the lead. <a href='$lead_link'><b>$lead_link</b></a>"]);

            return redirect()->back();
        }
        $lead = new nvLead();
        $lead->created_by = $auth_user->id;
        $lead->lead_datetime = $this->current_timestamp;
        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->mobile = $request->mobile_number;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->address = $request->address;
        $lead->event_datetime = $request->event_date ? $request->event_date . " " . date('H:i:s') : '';
        if ($lead->save()) {
            $nvrmIds = $request->nvrm_id;
            $exist_lead_forward = nvrmLeadForward::where(['lead_id' => $lead->id])->first();
            if (!$exist_lead_forward) {
                $lead_forward = new nvrmLeadForward();
                $lead_forward->lead_id = $lead->id;
                $lead_forward->forward_to = $nvrmIds;
                $lead_forward->lead_datetime = $this->current_timestamp;
                $lead_forward->name = $lead->name;
                $lead_forward->email = $lead->email;
                $lead_forward->mobile = $lead->mobile;
                $lead_forward->alternate_mobile = $lead->alternate_mobile;
                $lead_forward->address = $lead->address;
                $lead_forward->lead_status = "Active";
                $lead_forward->read_status = false;
                $lead_forward->done_title = null;
                $lead_forward->done_message = null;
                $lead_forward->event_datetime = $lead->event_datetime;
                $lead_forward->save();
            }
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead added and forwarded successfully."]);
        } else {
        }

        if ($request->event_date != null) {
            $event = new nvEvent();
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

    public function edit_process(Request $request, $lead_id)
    {
        $lead = nvLead::find($lead_id);
        if (!$lead) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead updated successfully.']);
        return redirect()->back();
    }

    public function view($lead_id)
    {
        $nvrm_members = TeamMember::select('id', 'name')->where('role_id', 3)->orderBy('name', 'asc')->get(); //role_id:3=NVRM;
        $lead = nvLead::find($lead_id);
        $lead_extra_data = nvrmLeadForward::where('lead_id', $lead_id)->first();
        if (!$lead) {
            abort(404);
        }
        $nvrm_forwarded_count = nvrmLeadForward::where('lead_id', $lead_id)->count();
        $nv_forwarded_count = nvLeadForward::where('lead_id', $lead_id)->count();
        $forwarded_count = $nvrm_forwarded_count + $nv_forwarded_count;
        // return $lead->get_events;
        return view('admin.nonVenueCrm.nvlead.view', compact('lead', 'forwarded_count', 'nvrm_members', 'lead_extra_data'));
    }

    public function delete($lead_id)
    {
        $lead = nvLead::find($lead_id);
        if ($lead) {
            nvrmLeadForward::where('lead_id', $lead_id)->delete();
            nvLeadForward::where('lead_id', $lead_id)->delete();
            $lead->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead deleted successfully."]);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Lead not found."]);
        }
        return redirect()->back();
    }

    public function lead_forward(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'forward_leads_id' => 'required',
            'forward_rms_id' => 'required',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $leads_id = explode(',', $request->forward_leads_id);
        $leads = nvLead::whereIn('id', $leads_id)->get();
        foreach ($leads as $lead) {
            foreach ($request->forward_rms_id as $rm_id) {
                $exist_lead_forward = nvrmLeadForward::where('lead_id', $lead->id)->first();
                $lead_forward = $exist_lead_forward;
                $lead_forward->forward_to = $rm_id;
                $lead_forward->save();
            }
        }
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead's Forwarded successfully."]);
        return redirect()->back();
    }

    public function get_forward_info($lead_id = 0)
    {
        try {
            $nvrm_forwards = nvrmLeadForward::select(
                'tm.name',
                'r.name as role_name',
                'nvrm_lead_forwards.read_status',
            )->leftJoin('team_members as tm', 'nvrm_lead_forwards.forward_to', '=', 'tm.id')->leftJoin('roles as r', 'tm.role_id', '=', 'r.id')
                ->where(['nvrm_lead_forwards.lead_id' => $lead_id])->groupBy('nvrm_lead_forwards.forward_to')->orderBy('nvrm_lead_forwards.lead_datetime', 'desc')->get()->toArray();

            $nv_forwards =  nvLeadForwardInfo::select(
                'v.name as name',
                'v.business_name',
                'nvrm.name as from_name',
                'nv_lead_forwards.read_status',
                'nv_lead_forward_infos.updated_at'
            )->join('vendors as v', 'nv_lead_forward_infos.forward_to', '=', 'v.id')
                ->join('team_members as nvrm', 'nv_lead_forward_infos.forward_from', '=', 'nvrm.id')
                ->join('nv_lead_forwards', function ($join) use ($lead_id) {
                    $join->on('nv_lead_forwards.forward_to', '=', 'v.id')
                        ->where('nv_lead_forwards.lead_id', '=', $lead_id);
                })
                ->where('nv_lead_forward_infos.lead_id', $lead_id)
                ->orderBy('nv_lead_forwards.updated_at', 'desc')
                ->get()->toArray();


            $lead_forwards = array_merge($nvrm_forwards, $nv_forwards);
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
