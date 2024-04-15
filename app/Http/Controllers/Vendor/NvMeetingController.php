<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\nvLeadForward;
use App\Models\nvMeeting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class NvMeetingController extends Controller {
    public function list(Request $request) {
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
        return view('vendor.meeting.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('vendor')->user();
        $meetings = nvLeadForward::select(
            'nv_lead_forwards.lead_id',
            'nv_lead_forwards.lead_datetime',
            'nv_lead_forwards.name',
            'nv_lead_forwards.mobile',
            'nv_lead_forwards.lead_status',
            'nv_lead_forwards.read_status',
            'meetings.meeting_schedule_datetime',
            'nv_lead_forwards.event_datetime',
            'meetings.created_at as meeting_created_datetime',
            'meetings.done_datetime as meeting_done_datetime',
        )->join('nv_meetings as meetings', ['nv_lead_forwards.meeting_id' => 'meetings.id'])->where(['nv_lead_forwards.forward_to' => $auth_user->id, 'meetings.deleted_at' => null])->whereNotNull('nv_lead_forwards.meeting_id');

        $current_date = date('Y-m-d');
        if ($request->meeting_status == "Upcoming") {
            $meetings->where('meetings.meeting_schedule_datetime', '>', Carbon::today())->whereNull('meetings.done_datetime');
        } elseif ($request->meeting_status == "Today") {
            $meetings->where('meetings.meeting_schedule_datetime', 'like', "%$current_date%")->whereNull('meetings.done_datetime');
        } elseif ($request->meeting_status == "Overdue") {
            $meetings->where('meetings.meeting_schedule_datetime', '<', Carbon::today())->whereNull('meetings.done_datetime');
        } elseif ($request->meeting_status == "Done") {
            $meetings->whereNotNull('meetings.done_datetime');
        } elseif ($request->meeting_created_from_date) {
            $from = Carbon::make($request->meeting_created_from_date);
            if ($request->meeting_created_to_date != null) {
                $to = Carbon::make($request->meeting_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->meeting_created_from_date)->endOfDay();
            }
            $meetings->whereBetween('meetings.created_at', [$from, $to]);
        } elseif ($request->meeting_done_from_date) {
            $from = Carbon::make($request->meeting_done_from_date);
            if ($request->meeting_done_to_date != null) {
                $to = Carbon::make($request->meeting_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->meeting_done_from_date)->endOfDay();
            }
            $meetings->whereBetween('meetings.done_datetime', [$from, $to]);
        } elseif ($request->meeting_schedule_from_date) {
            $from = Carbon::make($request->meeting_schedule_from_date);
            if ($request->meeting_schedule_to_date != null) {
                $to = Carbon::make($request->meeting_schedule_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->meeting_schedule_from_date)->endOfDay();
            }
            $meetings->whereBetween('meetings.meeting_schedule_datetime', [$from, $to]);
        }

        // return $meetings->toSql();
        $meetings = $meetings->get();
        return datatables($meetings)->toJson();
    }

    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:nv_leads,id',
            'meeting_event_name' => 'required|string',
            'meeting_schedule_datetime' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $exist_meeting = nvMeeting::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if ($exist_meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active meeting, please complete it first.']);
            return redirect()->back();
        }

        $meeting = new nvMeeting();
        $meeting->lead_id = $request->lead_id;
        $meeting->created_by = $auth_user->id;

        $meeting->event_name = $request->meeting_event_name;
        $meeting->meeting_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->meeting_schedule_datetime));
        $meeting->message = $request->meeting_message;
        $meeting->save();

        $lead_forwards = nvLeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $auth_user->id])->first();
        $lead_forwards->meeting_id = $meeting->id;
        $lead_forwards->read_status = true;
        $lead_forwards->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meeting created successfully.']);
        return redirect()->back();
    }

    public function delete($meeting_id) {
        $auth_user = Auth::guard('vendor')->user();
        $meeting = nvMeeting::where(['id' => $meeting_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();

        if (!$meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }

        $forward = nvLeadForward::where(['lead_id' => $meeting->lead_id, 'forward_to' => $auth_user->id])->first();
        $forward->meeting_id = null;
        $forward->save();

        $meeting->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meeting Deleted.']);
        return redirect()->back();
    }

    public function status_update(Request $request, $meeting_id) {
        $validate = Validator::make($request->all(), [
            'price_quoted' => 'required|int',
            'event_date' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $meeting = nvMeeting::where(['id' => $meeting_id, 'created_by' => $auth_user->id])->first();
        if (!$meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }
        $meeting->event_datetime = date('Y-m-d H:i:s', strtotime($request->event_date));
        $meeting->price_quoted = $request->price_quoted;
        $meeting->done_message = $request->done_message;
        $meeting->done_datetime = date('Y-m-d H:i:s');
        $meeting->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'meeting status updated.']);
        return redirect()->back();
    }
}
