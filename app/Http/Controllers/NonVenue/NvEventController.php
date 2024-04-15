<?php

namespace App\Http\Controllers\NonVenue;

use App\Http\Controllers\Controller;
use App\Models\nvEvent;
use App\Models\nvLead;
use App\Models\nvLeadForward;
use App\Models\nvrmLeadForward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NvEventController extends Controller {
    public function manage_ajax($event_id) {
        try {
            $event = nvEvent::find($event_id);
            $event->event_date = date('Y-m-d', strtotime($event->event_datetime));
            return response()->json(['success' => true, 'event' => $event]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.'], 500);
        }
    }

    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'forward_id' => 'required|exists:nvrm_lead_forwards,id',
            'event_name' => 'required|string',
            'event_date' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $event_datetime = $request->event_date . " " . date('H:i:s');
        $auth_user = Auth::guard('nonvenue')->user();

        //update forward lead event_datetime.
        $forward = nvrmLeadForward::find($request->forward_id);
        $forward->event_datetime = $event_datetime;
        $forward->save();

        $event = new nvEvent();
        $event->lead_id = $forward->lead_id;
        $event->created_by = $auth_user->id;
        $event->event_name = $request->event_name;
        $event->event_datetime = $event_datetime;
        $event->event_slot = $request->event_slot;
        $event->pax = $request->number_of_guest;
        $event->save();

        $lead = nvLead::find($forward->lead_id);
        $lead->event_datetime = $event_datetime;
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Event added successfully."]);
        return redirect()->back();
    }

    public function edit_process(Request $request, $event_id) {
        $validate = Validator::make($request->all(), [
            'forward_id' => 'required|exists:nvrm_lead_forwards,id',
            'event_name' => 'required|string',
            'event_date' => 'required|date',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $event = nvEvent::find($event_id);
        if ($event) {
            $event_datetime = $request->event_date . " " . date('H:i:s');

            $event->event_name = $request->event_name;
            $event->event_datetime = $event_datetime;
            $event->pax = $request->number_of_guest;
            $event->event_slot = $request->event_slot;
            $event->venue_name = $request->venue_name;
            $event->pax = $request->number_of_guest;
            $event->save();

            //update forward lead event_datetime.
            $forward = nvrmLeadForward::find($request->forward_id);
            $forward->event_datetime = $event_datetime;
            $forward->save();

            $lead = nvLead::find($forward->lead_id);
            $lead->event_datetime = $event_datetime;
            $lead->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Event updated successfully."]);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Someting went wrong. Please try again later."]);
        }
        return redirect()->back();
    }
}
