<?php

namespace App\Http\Controllers\Bdm;

use App\Http\Controllers\Controller;
use App\Models\BdmLead;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\TeamMember;
use App\Models\WhatsappCampain;
use App\Models\VendorCategory;
use Illuminate\Http\Request;
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

    public function list(Request $request, $dashboard_filters = null)
    {
        $getBdm = TeamMember::select('id', 'name')->where('role_id', '6')->get();
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
        $page_heading = $filter_params ? "Leads - Filtered" : "Leads";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }
        $auth_user = Auth::guard('bdm')->user();
        $vendor_categories = VendorCategory::select('id', 'name')->get();
        $whatsapp_campaigns = WhatsappCampain::select('id', 'name')->where('status', 1)->where('assign_to', $auth_user->id)->get();

        return view('bdm.lead.list', compact('page_heading', 'filter_params', 'getBdm', 'whatsapp_campaigns', 'vendor_categories'));
    }

    public function ajax_list(Request $request)
    {
        $auth_user = Auth::guard('bdm')->user();
            $leads = DB::table('bdm_leads')->select(
                'bdm_leads.lead_id as lead_id',
                'bdm_leads.lead_datetime',
                'bdm_leads.name',
                'bdm_leads.mobile',
                'bdm_leads.lead_status',
                'bdm_leads.service_status',
                'bdm_leads.read_status',
                'bdm_leads.assign_to',
                'bdm_leads.assign_id',
                'bdm_leads.source',
                'bdm_leads.business_name',
                'bdm_leads.city',
                'vc.name as business_cat',
                'tm.name as created_by',
                'bdm_leads.whatsapp_msg_time',
                'bdm_leads.enquiry_count',
                'bdm_leads.is_whatsapp_msg',
            )->leftJoin('team_members as tm', 'tm.id', 'bdm_leads.created_by')
            ->leftJoin('vendor_categories as vc', 'vc.id', 'bdm_leads.business_cat')->where('bdm_leads.assign_id', $auth_user->id);
            $leads->where('bdm_leads.deleted_at', null)->groupBy('bdm_leads.mobile');

            if ($request->has('lead_status') && $request->lead_status != '') {
                $leads->where('bdm_leads.lead_status', $request->lead_status);
            }
            if ($request->has('business_cat') && $request->business_cat != '') {
                $leads->where('bdm_leads.business_cat', $request->business_cat);
            }
            if ($request->has('lead_source') && $request->lead_source != '') {
                $leads->where('bdm_leads.source', $request->lead_source);
            }

            if ($request->has('lead_from_date') && $request->lead_from_date != '') {
                $from = Carbon::make($request->lead_from_date);
                $to = $request->has('lead_to_date') && $request->lead_to_date != '' ? Carbon::make($request->lead_to_date)->endOfDay() : $from->copy()->endOfDay();
                $leads->whereBetween('bdm_leads.lead_datetime', [$from, $to]);
            }

            if ($request->lead_read_status != null) {
                $leads->where('read_status', $request->lead_read_status);
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
                $leads->where('bdm_leads.lead_status', 'Done')->whereBetween('bdm_leads.updated_at', [$from, $to]);
            }

            if ($request->team_members != null) {
                $leads->where('bdm_leads.assign_id', $request->team_members);
            }

            if ($request->dashboard_filters != null) {
                $current_month = date('Y-m');
                $current_date = date('Y-m-d');
                $currentDateTime = Carbon::today();
                if ($request->dashboard_filters == "total_leads_received_this_month") {
                    $leads->where('bdm_leads.lead_datetime', 'like', "%$current_month%")->whereNull('bdm_leads.deleted_at')->where('bdm_leads.assign_id', $auth_user->id);
                } elseif ($request->dashboard_filters == "total_leads_received_today") {
                    $leads->where('bdm_leads.lead_datetime', 'like', "%$current_date%")->whereNull('bdm_leads.deleted_at')->where('bdm_leads.assign_id', $auth_user->id);
                } elseif ($request->dashboard_filters == "bdm_unfollowed_leads") {
                    $currentDateTime = Carbon::now();
                    $leads = DB::table('bdm_leads')->select(
                        'bdm_leads.lead_id as lead_id',
                        'bdm_leads.lead_datetime',
                        'bdm_leads.name',
                        'bdm_leads.mobile',
                        'bdm_leads.lead_status',
                        'bdm_leads.service_status',
                        'bdm_leads.read_status',
                        'bdm_leads.assign_to',
                        'bdm_leads.assign_id',
                        'bdm_leads.source',
                        'bdm_leads.city',
                        'bdm_leads.business_name',
                        'vc.name as business_cat',
                        'tm.name as created_by',
                        'bdm_leads.whatsapp_msg_time',
                        'bdm_leads.enquiry_count',
                        'bdm_leads.is_whatsapp_msg',
                    )->leftJoin('team_members as tm', 'tm.id', 'bdm_leads.created_by')
                    ->leftJoin('vendor_categories as vc', 'vc.id', 'bdm_leads.business_cat')
                    ->leftJoin(DB::raw("
                        (SELECT bdm_tasks.lead_id
                        FROM bdm_tasks
                        WHERE bdm_tasks.deleted_at IS NULL
                        AND bdm_tasks.created_by = $auth_user->id
                        GROUP BY bdm_tasks.lead_id
                        HAVING COUNT(CASE WHEN bdm_tasks.done_datetime IS NULL THEN 1 END) = 0) as completed_tasks
                    "), 'completed_tasks.lead_id', '=', 'bdm_leads.lead_id')
                    ->whereNotNull('completed_tasks.lead_id')
                    ->where('bdm_leads.lead_status', '!=', 'Done');
                }elseif($request->dashboard_filters == "unread_leads_this_month"){
                    $leads->where('bdm_leads.lead_datetime', 'like', "%$current_month%")->whereNull('bdm_leads.deleted_at')->where(['bdm_leads.read_status' => false])->where('bdm_leads.assign_id', $auth_user->id);
                }elseif($request->dashboard_filters == "unread_leads_today"){
                    $leads->where('bdm_leads.lead_datetime', 'like', "%$current_date%")->where(['bdm_leads.read_status' => false])->whereNull('bdm_leads.deleted_at')->where('bdm_leads.assign_id', $auth_user->id);
                }elseif($request->dashboard_filters == "total_unread_leads_overdue"){
                    $leads->where('bdm_leads.read_status', false)
                    ->where('bdm_leads.assign_id', $auth_user->id);
                }
            }
        return datatables($leads)->toJson();
    }


    public function edit_process(Request $request, $lead_id)
    {
        $lead = BdmLead::find($lead_id);
        if (!$lead) {
            abort(404);
        }
        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->alternate_mobile = $request->alternate_mobile_number;
        $lead->lead_status = $request->lead_status;
        $lead->city = $request->city;
        $lead->business_name = $request->business_name;
        $lead->business_cat = $request->business_cat;
        $lead->save();
        Log::info($request);
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Lead updated successfully.']);
        return redirect()->back();
    }

    public function add_process(Request $request)
    {
        $is_name_valid = $request->name !== null ? "required|string|max:255" : "";
        $is_email_valid = $request->email !== null ? "required|email" : "";
        $is_alt_mobile_valid = $request->alternate_mobile !== null ? "required|digits:10" : "";
        $validate = Validator::make($request->all(), [
            'name' => $is_name_valid,
            'email' => $is_email_valid,
            'alternate_mobile' => $is_alt_mobile_valid,
            'mobile' => "required|digits:10",
            'lead_status' => 'required',
            'business_cat' => 'required',
            'business_name' => 'required',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('bdm')->user();
        $exist_lead = BdmLead::where('mobile', $request->mobile_number)->first();
        if ($exist_lead) {
            session()->flash('status', ['success' => true, 'alert_type' => 'warning', 'message' => "Lead is already exist with this mobile number, Please contact to the management."]);
            return redirect()->back();
        }

        $source = "WB|Team";
        $lead = new BdmLead();
        $lead->created_by = $auth_user->id;
        $lead->lead_datetime = $this->current_timestamp;
        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->mobile = $request->mobile;
        $lead->alternate_mobile = $request->alternate_mobile;
        $lead->source = $source;
        $lead->lead_status = $request->lead_status;
        $lead->business_cat = $request->business_cat;
        $lead->business_name = $request->business_name;
        $lead->city = $request->city;
        $lead->assign_to = $auth_user->name;
        $lead->assign_id = $auth_user->id;
        $lead->lead_color = "#0066ff33";
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead created successfully."]);
        return redirect()->back();
    }

    public function view($lead_id)
    {
        $lead = BdmLead::where('lead_id',$lead_id)->first();
        if (!$lead) {
            abort(404);
        }
        $vendor_categories = VendorCategory::get();
        return view('bdm.lead.view', compact('lead', 'vendor_categories'));
    }

    public function service_status_update($lead_id, $status)
    {
        $lead = BdmLead::where('lead_id', $lead_id)->first();
        if (!$lead) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
            return redirect()->back();
        }
        $lead->service_status = $status;
        $lead->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Service status updated."]);
        return redirect()->back();
    }

    public function status_update(Request $request, $lead_id, $status = "Done")
    {
        $auth_user = Auth::guard('bdm')->user();
            $lead = BdmLead::find($lead_id);

        if (!$lead) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
            return redirect()->back();
        }

        if ($status == "Active") {
            // $lead->lead_datetime = $this->current_timestamp;
            $lead->lead_status = "Active";
            $lead->read_status = false;
            $lead->service_status = false;
            $lead->done_title = null;
            $lead->done_message = null;
            $lead->save();
        } else {
            $validate = Validator::make($request->all(), [
                'done_title' => 'required|string',
            ]);
            session(['next_modal_to_open' => null]);

            if ($validate->fails()) {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
                return redirect()->back();
            }
            $lead->lead_status = "Done";
            $lead->read_status = true;
            $lead->done_title = $request->done_title;
            $lead->done_message = $request->done_message;
            $lead->created_by = $auth_user->id;
            $lead->lead_color = "#ff000066";
            $lead->save();
        }

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Lead status updated."]);
        return redirect()->back();
    }
}
