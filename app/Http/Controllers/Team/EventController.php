<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\VmEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller {
    public function manage_ajax($event_id) {
        try {
            $auth_user = Auth::guard('team')->user();
            if ($auth_user->role_id == 4) {
                $event = Event::find($event_id);
            } else {
                $event = VmEvent::find($event_id);
            }
            $event->event_date = date('Y-m-d', strtotime($event->event_datetime));
            return response()->json(['success' => true, 'event' => $event]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.'], 500);
        }
    }

    public function add_process(Request $request) {
        $is_budget_validate = $request->budget !== null ? "required|numeric" : "";
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,lead_id',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_slot' => 'required|string|max:255',
            'food_Preference' => 'required|string|max:255',
            'number_of_guest' => 'required|int|max_digits:6',
            'budget' => $is_budget_validate
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $event_datetime = $request->event_date . " " . date('H:i:s');
        $auth_user = Auth::guard('team')->user();

        $lead = Lead::find($request->lead_id);
        if ($auth_user->role_id == 4) {
            $lead->event_datetime = $event_datetime;

            $event = new Event();
        } else {
            $forward = LeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $auth_user->id])->first();
            $forward->event_datetime = $event_datetime;
            $forward->save();
            $event = new VmEvent();
        }

        $event->lead_id = $request->lead_id;
        $event->created_by = $auth_user->id;
        $event->event_name = $request->event_name;
        $event->event_datetime = $event_datetime;
        $event->pax = $request->number_of_guest;
        $event->budget = $request->budget;
        $event->food_preference = $request->food_Preference;
        $event->event_slot = $request->event_slot;
        $event->save();

        $lead->pax = $request->number_of_guest;
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Event added successfully."]);
        return redirect()->back();
    }

    public function edit_process(Request $request, $event_id) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,lead_id',
            'event_name' => 'required|string',
            'event_date' => 'required|date',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('team')->user();
        $event_datetime = $request->event_date . " " . date('H:i:s');

        $lead = Lead::find($request->lead_id);
        if ($auth_user->role_id == 4) {
            $lead->event_datetime = $event_datetime;

            $event = Event::find($event_id);
        } else {
            $forward = LeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $auth_user->id])->first();
            $forward->event_datetime = $event_datetime;

            $event = VmEvent::find($event_id);
        }

        if (!$event) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Someting went wrong. Please try again later."]);
            return redirect()->back();
        }

        if ($auth_user->role_id == 5) {
            $forward->save();
        }

        $event->event_name = $request->event_name;
        $event->event_datetime = $event_datetime;
        $event->pax = $request->number_of_guest;
        $event->budget = $request->budget;
        $event->food_preference = $request->food_Preference;
        $event->event_slot = $request->event_slot;
        $event->save();

        $lead->pax = $request->number_of_guest;
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Event updated successfully."]);
        return redirect()->back();
    }
}
