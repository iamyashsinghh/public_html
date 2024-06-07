<?php

namespace App\Http\Controllers\Team;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\partyArea;
use App\Models\VmEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller {

    public function list(Request $request, $dashboard_filters = null) {
        $filter_params = "";
        if ($request->booking_source != null) {
            $filter_params = ['booking_source' => $request->booking_source];
        }
        if ($request->quarter_advance_collected != null) {
            $filter_params = ['quarter_advance_collected' => $request->quarter_advance_collected];
        }
        if ($request->event_from_date != null) {
            $filter_params = ['event_from_date' => $request->event_from_date, 'event_to_date' => $request->event_to_date];
        }
        if ($request->booking_from_date != null) {
            $filter_params = ['booking_from_date' => $request->booking_from_date, 'booking_to_date' => $request->booking_to_date];
        }
        if ($request->pax_min_value != null) {
            $filter_params = ['pax_min_value' => $request->pax_min_value, 'pax_max_value' => $request->pax_max_value];
        }
        $page_heading = $filter_params ? "Bookings - Filtered" : "Bookings";
        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }
        return view('team.venueCrm.bookings.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('team')->user();

        $bookings = LeadForward::select(
            'lead_forwards.lead_id',
            'bookings.created_at',
            'bookings.booking_source',
            'lead_forwards.name',
            'lead_forwards.mobile',
            'events.event_datetime',
            'events.pax',
            'bookings.total_gmv',
            'bookings.advance_amount',
            'bookings.quarter_advance_collected',
        )->join('bookings', 'bookings.id', 'lead_forwards.booking_id')
            ->join('vm_events as events', 'events.id', 'bookings.event_id')
            ->where(['lead_forwards.forward_to' => $auth_user->id, 'bookings.deleted_at' => null]);

        if ($request->booking_source != null) {
            $bookings->whereIn('bookings.booking_source', $request->booking_source);
        }
         if ($request->quarter_advance_collected != null) {
            $bookings->where('bookings.quarter_advance_collected', $request->quarter_advance_collected);
        }
        if ($request->booking_from_date != null) {
            $from =  Carbon::make($request->booking_from_date);
            if ($request->booking_to_date != null) {
                $to = Carbon::make($request->booking_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->booking_from_date)->endOfDay();
            }
            $bookings->whereBetween('bookings.created_at', [$from, $to]);
        }
        if ($request->pax_min_value != null) {
            $min = $request->pax_min_value;
            if ($request->pax_max_value != null) {
                $max = $request->pax_max_value;
            } else {
                $max = $request->pax_min_value;
            }
            $bookings->whereBetween('events.pax', [$min, $max]);
        }
        if ($request->event_from_date != null) {
            $from =  Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $bookings->whereBetween('events.event_datetime', [$from, $to]);
        }
        if ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "bookings_this_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $bookings->whereBetween('bookings.created_at', [$from, $to]);
            }
        }
        return datatables($bookings)->make(false);
    }

    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'predefined_event' => 'required|int|exists:vm_events,id',
            'lead_id' => 'required|int|exists:leads,lead_id',
            'party_area' => 'required|string|max:255',
            'menu_selected' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_slot' => 'required|string|max:255',
            'food_Preference' => 'required|string|max:255',
            'number_of_guest' => 'required|int|max_digits:6',
            'price_per_plate' => 'required|numeric',
            'total_gmv' => 'required|numeric',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('team')->user();
        $event_datetime = $request->event_date . " " . date('H:i:s');
        $lead = Lead::find($request->lead_id);

        $event = VmEvent::find($request->predefined_event);
        $event->event_name = $request->event_name;
        $event->event_datetime = $event_datetime;
        $event->pax = $request->number_of_guest;
        $event->budget = $request->total_gmv;
        $event->food_preference = $request->food_Preference;
        $event->event_slot = $request->event_slot;
        $event->save();

        $lead_forward = LeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $auth_user->id])->first();
        if (!$lead_forward) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $total_advance_amount = 0;
        if ($request->advance_amount != null) {
            foreach (array_filter($request->advance_amount) as $value) {
                $total_advance_amount += (float) $value;
            }
        }

        $quarter_advance = ((float) $request->total_gmv * 25) / 100;
        $booking = new Booking();
        $booking->lead_id = $request->lead_id;
        $booking->created_by = $auth_user->id;
        $booking->event_id = $request->predefined_event;
        $booking->party_area = $request->party_area;
        $booking->menu_selected = $request->menu_selected;
        $booking->booking_source = $lead_forward->source;
        $booking->price_per_plate = $request->price_per_plate;
        $booking->total_gmv = $request->total_gmv;
        $booking->advance_amount = $total_advance_amount;
        $booking->quarter_advance_collected = $quarter_advance <= $total_advance_amount;
        $booking->save();

        $lead_forward->event_datetime = $event_datetime;
        $lead_forward->booking_id = $booking->id;
        $lead_forward->lead_status = "Booked";
        $lead_forward->read_status = true;
        $lead_forward->save();

        $lead->pax = $request->number_of_guest;
        $lead->save();

        $party_area = partyArea::where(['member_id' => $auth_user->id, 'name' => $request->party_area])->first();
        if (!$party_area) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong. availability was not marked!"]);
            return redirect()->back();
        }

        $availability = new Availability();
        $availability->created_by = $auth_user->id;
        $availability->party_area_id = $party_area->id;
        $availability->food_type = $request->event_slot;
        $availability->pax = $request->number_of_guest;
        $availability->date = $request->event_date;
        $availability->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Booking added successfully."]);
        return redirect()->back();
    }

    public function add_more_advance_amount(Request $request, $booking_id) {
        $validate = Validator::make($request->all(), [
            'advance_amount' => 'required',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $total_advance_amount = 0;
        if ($request->advance_amount != null) {
            foreach (array_filter($request->advance_amount) as $value) {
                $total_advance_amount += (float) $value;
            }
        }

        $booking = Booking::find($booking_id);
        $total_advance_amount = (float) $booking->advance_amount + (float) $total_advance_amount;
        $quarter_advance = ((float) $booking->total_gmv * 25) / 100;

        $booking->advance_amount = $total_advance_amount;
        $booking->quarter_advance_collected = $quarter_advance <= $total_advance_amount;
        $booking->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Booking updated successfully."]);
        return redirect()->back();
    }
}
