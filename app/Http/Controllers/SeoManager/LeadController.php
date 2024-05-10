<?php

namespace App\Http\Controllers\SeoManager;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
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

        $page_heading = $filter_params ? "Leads - Filtered" : "Leads";
        return view('seomanager.lead.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request)
    {
        $leads = DB::table('leads')->select(
            'leads.lead_id',
            'leads.lead_datetime',
            'leads.source',
            'leads.preference',
            'leads.locality',
            'leads.lead_catagory'
        );
        if ($request->has('lead_from_date') && $request->lead_from_date != '') {
            $from = Carbon::make($request->lead_from_date);
            $to = $request->has('lead_to_date') && $request->lead_to_date != '' ? Carbon::make($request->lead_to_date)->endOfDay() : $from->copy()->endOfDay();
            $leads->whereBetween('leads.lead_datetime', [$from, $to]);
        }
        $leads->orderBy('leads.whatsapp_msg_time', 'desc');
        $leads->whereNull('leads.deleted_at');

        $leads = $leads->get();
        return datatables($leads)->escapeColumns([])->toJson();
    }
}
