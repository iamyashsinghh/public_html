<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PVendorEvent;
use App\Models\PVendorLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PEventController extends Controller
{
    public function manage_ajax($event_id) {
        try {
            $event = PVendorEvent::find($event_id);
            $event->event_date = date('Y-m-d', strtotime($event->event_datetime));
            return response()->json(['success' => true, 'event' => $event]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.'], 500);
        }
    }
    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:p_vendor_leads,id',
            'event_name' => 'required|string',
            'event_date' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $event_datetime = $request->event_date . " " . date('H:i:s');
        $auth_user = Auth::guard('vendor')->user();

        $forward = PVendorLead::find($request->lead_id);
        $forward->event_datetime = $event_datetime;
        $forward->save();

        $event = new PVendorEvent();
        $event->lead_id = $forward->id;
        $event->created_by = $auth_user->id;
        $event->event_name = $request->event_name;
        $event->event_datetime = $event_datetime;
        $event->event_slot = $request->event_slot;
        $event->venue_name = $request->venue_name;
        $event->pax = $request->number_of_guest;
        $event->save();
        
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Event added successfully."]);
        return redirect()->back();
    }

    public function edit_process(Request $request, $event_id) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:p_vendor_leads,id',
            'event_name' => 'required|string',
            'event_date' => 'required|date',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $event = PVendorEvent::find($event_id);
        if ($event) {
            $event_datetime = $request->event_date . " " . date('H:i:s');

            $event->event_name = $request->event_name;
            $event->event_datetime = $event_datetime;
            $event->pax = $request->number_of_guest;
            $event->event_slot = $request->event_slot;
            $event->venue_name = $request->venue_name;
            $event->pax = $request->number_of_guest;
            $event->save();

            $forward = PVendorEvent::find($request->lead_id);
            $forward->event_datetime = $event_datetime;
            $forward->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Event updated successfully."]);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Someting went wrong. Please try again later."]);
        }
        return redirect()->back();
    }
}
