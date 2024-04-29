<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PVendorMeeting;
use Illuminate\Http\Request;
use App\Models\PVendorLead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PMeetingController extends Controller
{
    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:p_vendor_leads,id',
            'meeting_event_name' => 'required|string',
            'meeting_schedule_datetime' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $exist_meeting = PVendorMeeting::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if ($exist_meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active meeting, please complete it first.']);
            return redirect()->back();
        }

        $meeting = new PVendorMeeting();
        $meeting->lead_id = $request->lead_id;
        $meeting->created_by = $auth_user->id;

        $meeting->event_name = $request->meeting_event_name;
        $meeting->meeting_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->meeting_schedule_datetime));
        $meeting->message = $request->meeting_message;
        $meeting->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meeting created successfully.']);
        return redirect()->back();
    }

    public function delete($meeting_id) {
        $auth_user = Auth::guard('vendor')->user();
        $meeting = PVendorMeeting::where(['id' => $meeting_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();

        if (!$meeting) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        
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
        $meeting = PVendorMeeting::where(['id' => $meeting_id, 'created_by' => $auth_user->id])->first();
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
