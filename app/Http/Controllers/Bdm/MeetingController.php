<?php

namespace App\Http\Controllers\Bdm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BdmLead;
use App\Models\BdmMeeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    public function list(Request $request, $dashboard_filters = null) {
        $filter_params = "";
        if ($request->meeting_status != null) {
            $filter_params =  ['meeting_status' => $request->meeting_status];
        }
        if ($request->meeting_created_from_date != null) {
            $filter_params = ['meeting_created_from_date' => $request->meeting_created_from_date, 'meeting_created_to_date' => $request->meeting_created_to_date];
        }
        if ($request->meeting_done_from_date != null) {
            $filter_params = ['meeting_done_from_date' => $request->meeting_done_from_date, 'meeting_done_to_date' => $request->meeting_done_to_date];
        }
        if ($request->meeting_schedule_from_date != null) {
            $filter_params = ['meeting_schedule_from_date' => $request->meeting_schedule_from_date, 'meeting_schedule_to_date' => $request->meeting_schedule_to_date];
        }

        $page_heading = $filter_params ? "Meetings - Filtered" : "Meetings";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }

        return view('bdm.meeting.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('bdm')->user();
        $meetings = BdmLead::select(
            'bdm_leads.lead_id',
            'bdm_leads.lead_datetime',
            'bdm_leads.name',
            'bdm_leads.mobile',
            'bdm_leads.lead_status',
            'bdm_leads.business_name',
            'vc.name as business_cat',
            'bdm_meetings.meeting_schedule_datetime',
            'bdm_meetings.created_at as meeting_created_datetime',
            'bdm_meetings.done_datetime as meeting_done_datetime',
        )->join('bdm_meetings', ['bdm_leads.lead_id' => 'bdm_meetings.lead_id'])
        ->leftJoin('vendor_categories as vc', 'vc.id', 'bdm_leads.business_cat')
        ->where(['bdm_meetings.created_by' => $auth_user->id, 'bdm_meetings.deleted_at' => null]);


        if ($request->has('meeting_status')) {
            $meetings->where(function ($query) use ($request) {
                foreach ($request->meeting_status as $status) {
                    switch ($status) {
                        case 'Upcoming':
                            $query->orWhere(function ($q) {
                                $q->where('bdm_meetings.meeting_schedule_datetime', '>', Carbon::today()->endOfDay());
                            });
                            break;
                        case 'Today':
                            $current_date = date('Y-m-d');
                            $query->orWhere(function ($q) use ($current_date) {
                                $q->where('bdm_meetings.meeting_schedule_datetime', 'like', "%$current_date%");
                            });
                            break;
                        case 'Overdue':
                            $query->orWhere(function ($q) {
                                $q->where('bdm_meetings.meeting_schedule_datetime', '<', Carbon::today())->whereNull('bdm_meetings.done_datetime');
                            });
                            break;
                        case 'Done':
                            $query->orWhereNotNull('bdm_meetings.done_datetime');
                            break;
                    }
                }
            });
        }
        if ($request->meeting_created_from_date) {
            $from = Carbon::make($request->meeting_created_from_date);
            if ($request->meeting_created_to_date != null) {
                $to = Carbon::make($request->meeting_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->meeting_created_from_date)->endOfDay();
            }
            $meetings->whereBetween('bdm_meetings.created_at', [$from, $to]);
        }
        if ($request->meeting_done_from_date) {
            $from = Carbon::make($request->meeting_done_from_date);
            if ($request->meeting_done_to_date != null) {
                $to = Carbon::make($request->meeting_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->meeting_done_from_date)->endOfDay();
            }
            $meetings->whereBetween('bdm_meetings.done_datetime', [$from, $to]);
        }
        if ($request->meeting_schedule_from_date) {
            $from = Carbon::make($request->meeting_schedule_from_date);
            if ($request->meeting_schedule_to_date != null) {
                $to = Carbon::make($request->meeting_schedule_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->meeting_schedule_from_date)->endOfDay();
            }
            $meetings->whereBetween('bdm_meetings.meeting_schedule_datetime', [$from, $to])->whereNull('bdm_meetings.done_datetime');
        }
        if ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "meeting_schedule_this_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $meetings->whereBetween('bdm_meetings.meeting_schedule_datetime', [$from, $to]);
            } elseif ($request->dashboard_filters == "meeting_schedule_today") {
                $from =  Carbon::today()->startOfDay();
                $to =  Carbon::today()->endOfDay();
                $meetings->whereBetween('bdm_meetings.meeting_schedule_datetime', [$from, $to])->whereNull('bdm_meetings.done_datetime');
            } elseif ($request->dashboard_filters == "total_meeting_overdue") {
                $meetings->where('bdm_meetings.meeting_schedule_datetime', '<', Carbon::today())->whereNull('bdm_meetings.done_datetime');
            }
        }

        $meetings = $meetings->get();
        return datatables($meetings)->toJson();
    }

    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:bdm_leads,lead_id',
            'meeting_schedule_datetime' => 'required|date',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $auth_user = Auth::guard('bdm')->user();
        $exist_meeting = BdmMeeting::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();

        session(['next_modal_to_open' => null]);

        if ($exist_meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active Meeting, please complete it first.']);
            return redirect()->back();
        }
        $getBdmLead = BdmLead::select('lead_id', 'read_status')->where('lead_id', $request->lead_id)->first();
        $getBdmLead->read_status = true;
        $getBdmLead->save();

        $meeting = new BdmMeeting();
        $meeting->lead_id = $request->lead_id;
        $meeting->created_by = $auth_user->id;
        $meeting->follow_up = $request->meeting_follow_up;
        $meeting->meeting_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->meeting_schedule_datetime));
        $meeting->message = $request->meeting_message;
        $meeting->save();

        session()->flash('status', ['sueccess' => true, 'alert_type' => 'success', 'message' => 'Meeting created successfully.']);
        return redirect()->back();
    }

    public function status_update(Request $request, $meeting_id) {
        $validate = Validator::make($request->all(), [
            'meeting_done_with' => 'required|string',
            'meeting_done_message' => 'required|string'
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('bdm')->user();

        $meeting = BdmMeeting::where(['id' => $meeting_id, 'created_by' => $auth_user->id])->first();

        if (!$meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        session(['next_modal_to_open' => $request->meeting_done_status]);

        $meeting->done_with = $request->meeting_done_with;
        $meeting->done_message = $request->meeting_done_message;
        $meeting->meeting_done_status = $request->meeting_done_status;
        $meeting->done_datetime = date('Y-m-d H:i:s');
        $meeting->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meeting status updated.']);
        return redirect()->back();
    }


    public function delete($meeting_id) {
        $auth_user = Auth::guard('bdm')->user();
        $meeting = BdmMeeting::where(['id' => $meeting_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if (!$meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        $meeting->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meeting Deleted.']);
        return redirect()->back();
    }
}
