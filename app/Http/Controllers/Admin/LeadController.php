<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\LeadForwardInfo;
use App\Models\Note;
use App\Models\nvEvent;
use App\Models\nvLead;
use App\Models\nvrmLeadForward;
use App\Models\RmMessage;
use App\Models\Task;
use App\Models\WhatsappCampain;
use App\Models\TeamMember;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    private $current_timestamp;
    public function __construct()
    {
        $this->current_timestamp = date('Y-m-d H:i:s');
    }

    public function list(Request $request)
    {
        $filter_params = "";
        if ($request->lead_read_status != null) {
            $filter_params = ['lead_read_status' => $request->lead_read_status];
        }
        if ($request->service_status != null) {
            $filter_params = ['service_status' => $request->service_status];
        }
        if ($request->team_members != null) {
            $filter_params = ['team_members' => $request->team_members];
        }
        if ($request->lead_from_date != null) {
            $filter_params = ['lead_from_date' => $request->lead_from_date, 'lead_to_date' => $request->lead_to_date];
        }
        if ($request->lead_done_from_date != null) {
            $filter_params = ['lead_done_from_date' => $request->lead_done_from_date, 'lead_done_to_date' => $request->lead_done_to_date];
        }
        if ($request->lead_status != null) {
            $filter_params = ['lead_status' => $request->lead_status];
        }
        if ($request->has_rm_message != null) {
            $filter_params = ['has_rm_message' => $request->has_rm_message];
        }
        if ($request->event_from_date != null) {
            $filter_params = ['event_from_date' => $request->event_from_date, 'event_to_date' => $request->event_to_date];
        }

        $rm_members = TeamMember::select('id', 'name')->where('role_id', 4)->orderBy('name', 'asc')->get();
        $getRm = TeamMember::select('id', 'name')->where('venue_name', 'RM >< Venue')->get();
        $page_heading = $filter_params ? "Leads - Filtered" : "Leads";
        $whatsapp_campaigns = WhatsappCampain::where('status', 1)->get();
        return view('admin.venueCrm.lead.list', compact('page_heading', 'filter_params', 'rm_members', 'getRm', 'whatsapp_campaigns'));
    }

    public function ajax_list(Request $request)
    {
        $leads = DB::table('leads')->select(
            'leads.lead_id',
            'leads.assign_to',
            'leads.service_status',
            'leads.lead_datetime',
            'leads.name',
            'leads.mobile',
            'leads.lead_status',
            'leads.source',
            'leads.lead_color',
            'leads.enquiry_count',
            'leads.event_datetime',
            'leads.preference',
            'leads.locality',
            'tm.name as created_by',
            'leads.last_forwarded_by',
            'leads.is_whatsapp_msg',
            'roles.name as created_by_role',
            'leads.whatsapp_msg_time',
            'leads.lead_catagory',
            'ne.pax as pax',
            DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count"),
        )->leftJoin('team_members as tm', 'leads.created_by', 'tm.id')
        ->leftJoin('events as ne', 'ne.lead_id', '=', 'leads.lead_id')
            ->leftJoin('roles', 'tm.role_id', 'roles.id')->groupBy('leads.mobile');

        if ($request->has('lead_status') && $request->lead_status != '') {
            $leads->whereIn('leads.lead_status', $request->lead_status);
        }

        if ($request->has('event_from_date') && $request->event_from_date != '') {
            $from = Carbon::make($request->event_from_date);
            $to = $request->has('event_to_date') && $request->event_to_date != '' ? Carbon::make($request->event_to_date)->endOfDay() : $from->copy()->endOfDay();
            $leads->whereBetween('leads.event_datetime', [$from, $to]);
        }

        if ($request->has('lead_from_date') && $request->lead_from_date != '') {
            $from = Carbon::make($request->lead_from_date);
            $to = $request->has('lead_to_date') && $request->lead_to_date != '' ? Carbon::make($request->lead_to_date)->endOfDay() : $from->copy()->endOfDay();
            $leads->whereBetween('leads.lead_datetime', [$from, $to]);
        }

        if ($request->has('has_rm_message') && $request->has_rm_message != '') {
            if ($request->has_rm_message == "yes") {
                $leads->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('rm_messages')
                        ->whereRaw('leads.lead_id = rm_messages.lead_id');
                });
            } else {
                $leads->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('rm_messages')
                        ->whereRaw('leads.lead_id = rm_messages.lead_id');
                });
            }
        }

        if ($request->lead_read_status != null) {
            $leads->where('read_status', $request->lead_read_status);
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
            $leads->where('service_status', $request->service_status);
        }

        if ($request->lead_done_from_date != null) {
            $from = Carbon::make($request->lead_done_from_date);
            if ($request->lead_done_to_date != null) {
                $to = Carbon::make($request->lead_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->lead_done_from_date)->endOfDay();
            }
            $leads->where('leads.lead_status', 'Done')->whereBetween('leads.updated_at', [$from, $to]);
        }

        if ($request->team_members != null) {
            $leads->whereIn('leads.assign_id', $request->team_members);
        }

        $leads->orderBy('leads.whatsapp_msg_time', 'desc');

        $leads->whereNull('leads.deleted_at');

        $leads = $leads->get();
        return datatables($leads)->escapeColumns([])->toJson();
    }

    public function add_process(Request $request)
    {
        $is_name_valid = $request->name !== null ? "required|string|max:255" : "";
        $is_email_valid = $request->email !== null ? "required|email" : "";
        $is_alt_mobile_valid = $request->alternate_mobile_number !== null ? "required|digits:10" : "";
        $is_budget_validate = $request->budget !== null ? "required|numeric" : "";
        $validate = Validator::make($request->all(), [
            'name' => $is_name_valid,
            'email' => $is_email_valid,
            'alternate_mobile_number' => $is_alt_mobile_valid,
            'mobile_number' => "required|digits:10",
            'lead_source' => 'required',
            'lead_status' => 'required',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_slot' => 'required|string|max:255',
            'food_Preference' => 'required|string|max:255',
            'number_of_guest' => 'required|int|max_digits:6',
            'budget' => $is_budget_validate,
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('admin')->user();
        $exist_lead = Lead::where('mobile', $request->mobile_number)->first();
        if ($exist_lead) { // Here we are finding the lead with same mobile number and just only adding event information.
            $lead_link = route('admin.lead.view', $exist_lead->lead_id);
            session()->flash('status', ['success' => true, 'alert_type' => 'info', 'message' => "Lead is already exist with this mobile number. Click on the link below to view the lead. <a href='$lead_link'><b>$lead_link</b></a>"]);
            return redirect()->back();
        }

        $lead = new Lead();
        $lead->created_by = $auth_user->id;
        $lead->lead_datetime = $this->current_timestamp;
        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->mobile = $request->mobile_number;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->source = $request->lead_source;
        $lead->locality = $request->locality;
        $lead->lead_status = $request->lead_status;
        $lead->event_datetime = $request->event_date ? $request->event_date . " " . date('H:i:s') : '';
        $lead->pax = $request->number_of_guest;
        $lead->lead_color = "#0066ff33";
        $lead->save();

        $event = new Event();
        $event->created_by = $auth_user->id;
        $event->lead_id = $lead->lead_id;
        $event->event_name = $request->event_name;
        $event->event_datetime = $request->event_date . " " . date('H:i:s');
        $event->pax = $request->number_of_guest;
        $event->budget = $request->budget;
        $event->food_Preference = $request->food_Preference;
        $event->event_slot = $request->event_slot;
        $event->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead added successfully."]);
        return redirect()->back();
    }

    public function edit_process(Request $request, $lead_id)
    {
        $lead = Lead::find($lead_id);
        if (!$lead) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->source = $request->lead_source;
        $lead->locality = $request->locality;
        $lead->lead_status = $request->lead_status;
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead updated successfully.']);
        return redirect()->back();
    }

    public function view($lead_id)
    {
        $rm_members = TeamMember::select('id', 'name')->where(['role_id' => 4, 'status' => 1])->orderBy('name', 'asc')->get();
        $lead = Lead::find($lead_id);
        $done_leads = LeadForward::where(['lead_id' => $lead_id, 'lead_status' => 'Done'])->get();
        $nvrm_members = TeamMember::select('id', 'name')->where('venue_name', 'RM >< Non Venue')->get();
        return view('admin.venueCrm.lead.view', compact('lead', 'rm_members', 'done_leads', 'nvrm_members'));
    }

    public function delete($lead_id)
    {
        $lead = Lead::find($lead_id);
        $lead_forwards = LeadForward::where('lead_id', $lead_id)->get();
        $lead_forward_infos = LeadForwardInfo::where('lead_id', $lead_id)->get();
        $rm_messages = RmMessage::where('lead_id', $lead_id)->get();
        $events = Event::where('lead_id', $lead_id)->get();
        $visits = Visit::where('lead_id', $lead_id)->get();
        $tasks = Task::where('lead_id', $lead_id)->get();
        $notes = Note::where('lead_id', $lead_id)->get();

        $lead_forwards->each->delete();
        $lead_forward_infos->each->delete();
        $rm_messages->each->delete();
        $events->each->delete();
        $visits->each->delete();
        $tasks->each->delete();
        $notes->each->delete();
        $lead->delete();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead deleted successfully."]);
        return redirect()->route('admin.lead.list');
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
        $leads = Lead::whereIn('lead_id', $leads_id)->get();
        $auth_user = Auth::guard('admin')->user();
        $last_forwarded_info = $auth_user->name . " <br> " . date('d-M-Y h:i a');
        $TeamMember = TeamMember::where('id', $request->forward_rms_id)->first();
        foreach ($leads as $lead) {
            $lead->assign_to = $TeamMember->name;
            $lead->save();
        }
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead's Forwarded successfully."]);
        return redirect()->back();
    }

    public function lead_forward_nvrm(Request $request)
    {
        $rm_lead_id = $request->lead_id;
        $forward_rms_id = $request->forward_rms_id;
        $auth_user = Auth::guard('admin')->user();
        $leadData = Lead::where('lead_id', $rm_lead_id)->first();
        if (!$leadData) {
            return response()->json(['success' => false, 'message' => 'Lead data not found.'], 404);
        }
        $exist_lead = nvLead::where('mobile', $leadData->mobile)->first();
        if ($exist_lead) {
            $lead_link = route('admin.nvlead.view', $exist_lead->id);
            return response()->json(['success' => true, 'alert_type' => 'info', 'message' => "Lead is already exist with this mobile number. Click on the link below to view the lead.", 'link' => $lead_link], 200);
        } else {
            $lead = new nvLead();
            $lead->created_by = $auth_user->id;
            $lead->lead_datetime = now();
            $lead->name = $leadData->name;
            $lead->email = $leadData->email;
            $lead->mobile = $leadData->mobile;
            $lead->alternate_mobile = $leadData->alternate_mobile;
            $lead->event_datetime = $leadData->event_datetime;
            if ($lead->save()) {
                    $exist_lead_forward = nvrmLeadForward::where(['lead_id' => $lead->id, 'forward_to' => $forward_rms_id])->first();
                    if (!$exist_lead_forward) {
                        $lead_forward = new nvrmLeadForward();
                        $lead_forward->lead_id = $lead->id;
                        $lead_forward->forward_to = $forward_rms_id;
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
                $leadEventsData = Event::where('lead_id', $rm_lead_id)->get();
                foreach ($leadEventsData as $leadEventData) {
                    $event = new NvEvent();
                    $event->created_by = $auth_user->id;
                    $event->lead_id = $lead->id;
                    $event->event_name = $leadEventData->event_name;
                    $event->event_datetime = $leadEventData->event_datetime . " " . date('H:i:s');
                    $event->pax = $leadEventData->pax;
                    $event->event_slot = $leadEventData->event_slot;
                    $event->save();
                }
                return response()->json(['success' => true, 'message' => 'Lead added and forwarded successfully.'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to save the lead.'], 500);
            }
        }
    }


    public function get_forward_info($lead_id = 0)
    {
        try {
            $lead_forwards = LeadForwardInfo::select(
                'tm.name as to_name',
                'tm.venue_name',
                'rm.name as from_name',
                'lead_forwards.read_status',
                'lead_forward_infos.updated_at',
            )->join('team_members as tm', 'lead_forward_infos.forward_to', '=', 'tm.id')
            ->join('team_members as rm', 'lead_forward_infos.forward_from', '=', 'rm.id')
            ->join('lead_forwards', function($join) use ($lead_id) {
                $join->on('lead_forwards.forward_to', '=', 'tm.id')
                     ->where('lead_forwards.lead_id', '=', $lead_id);
            })
            ->where('lead_forward_infos.lead_id', $lead_id)
            ->orderBy('lead_forwards.updated_at', 'desc')
            ->get();

            $lead_forward_info = LeadForwardInfo::where(['lead_id' => $lead_id])->orderBy('updated_at', 'desc')->first();
            if ($lead_forward_info) {
                $last_forwarded_info = "Last forwarded by: " . $lead_forward_info->get_forward_from->name . " (" . $lead_forward_info->get_forward_from->get_role->name . ") " . " @ " . date('d-M-Y h:i a', strtotime($lead_forward_info->updated_at));
            } else {
                $last_forwarded_info = "Last forwarded by: N/A";
            }

            return response()->json(['success' => true, 'lead_forwards' => $lead_forwards, 'last_forwarded_info' => $last_forwarded_info]);
        } catch (\Throwable $th) {
            return response()->json(['success' => true, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'error' => $th]);
        }
    }
}
