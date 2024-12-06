<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\foodPreference;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\partyArea;
use App\Models\TeamMember;
use App\Models\Venue;
use App\Models\VmEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller {

    public function list(Request $request, $dashboard_filters = null) {
        $filter_params = [];
        if ($request->booking_source != null) {
            $filter_params['booking_source'] = $request->booking_source;
        }
        if ($request->venues_source != null) {
            $filter_params['venues_source'] = $request->venues_source;
        }
        if ($request->vm_source != null) {
            $filter_params['vm_source'] = $request->vm_source;
        }
        if ($request->quarter_advance_collected != null) {
            $filter_params['quarter_advance_collected'] = $request->quarter_advance_collected;
        }
        if ($request->event_from_date != null) {
            $filter_params['event_from_date'] = $request->event_from_date;
            $filter_params['event_to_date'] = $request->event_to_date;
        }
        if ($request->pax_min_value != null) {
            $filter_params['pax_min_value'] = $request->pax_min_value;
            $filter_params['pax_max_value'] = $request->pax_max_value;
        }
        if ($request->booking_from_date != null) {
            $filter_params['booking_from_date'] = $request->booking_from_date;
            $filter_params['booking_to_date'] = $request->booking_to_date;
        }
        $page_heading = !empty($filter_params) ? "Bookings - Filtered" : "Bookings";
        if ($dashboard_filters !== null) {
            $filter_params['dashboard_filters'] = $dashboard_filters;
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }
        $vm_id_name = TeamMember::select('id', 'name')->where('role_id', 5)->get();
        $all_venues = Venue::select('id', 'name')->get();

        return view('admin.venueCrm.bookings.list', compact('page_heading', 'filter_params', 'vm_id_name', 'all_venues'));
    }

    public function ajax_list(Request $request) {
        $bookings = LeadForward::select(
            'lead_forwards.lead_id',
            'bookings.created_at',
            'bookings.booking_source',
            'team_members.venue_name',
            'team_members.name as vm_name',
            'lead_forwards.name',
            'lead_forwards.mobile',
            'events.event_name',
            'events.event_datetime',
            'events.pax',
            'bookings.total_gmv',
            'bookings.advance_amount',
            'bookings.quarter_advance_collected',
        )->join('bookings', 'bookings.id', 'lead_forwards.booking_id')
            ->join('vm_events as events', 'events.id', 'bookings.event_id')
            ->join('team_members', 'team_members.id', 'lead_forwards.forward_to')
            ->where(['team_members.role_id' => 5, 'bookings.deleted_at' => null]);

        if ($request->booking_source != null) {
            $bookings->whereIn('bookings.booking_source', $request->booking_source);
        }
        if ($request->vm_source) {
            $bookings->whereIn('team_members.name', $request->vm_source);
        }
        if ($request->venues_source) {
            $bookings->whereIn('team_members.venue_name', $request->venues_source);
        }
        if ($request->quarter_advance_collected != null) {
            $bookings->where('bookings.quarter_advance_collected', $request->quarter_advance_collected);
        }
        if ($request->booking_from_date != null) {
            $from = Carbon::make($request->booking_from_date);
            if ($request->booking_to_date != null) {
                $to = Carbon::make($request->booking_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->booking_from_date)->endOfDay();
            }
            $bookings->whereBetween('bookings.created_at', [$from, $to]);
        }
        if ($request->event_from_date != null) {
            $from = Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $bookings->whereBetween('events.event_datetime', [$from, $to]);
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
        if ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "bookings_this_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $bookings->whereBetween('bookings.created_at', [$from, $to]);
            }
        }
        $bookings = $bookings->get();
        return datatables($bookings)->make(false);
    }

    public function fetch_booking($booking_id) {
        try {
            $booking = Booking::select(
                'bookings.id',
                'bookings.lead_id',
                'bookings.created_by',
                'bookings.event_id',
                'bookings.party_area',
                'bookings.menu_selected',
                'vm_events.event_name',
                'vm_events.event_datetime as event_date',
                'vm_events.event_slot',
                'vm_events.food_preference',
                'vm_events.pax',
                'bookings.price_per_plate',
                'bookings.total_gmv',
                'bookings.advance_amount',
            )->join('vm_events', 'vm_events.id', 'bookings.event_id')
                ->where('bookings.id', $booking_id)->first();
            $booking->event_date = date('Y-m-d', strtotime($booking->event_date));

            $predefined_events = VmEvent::select('id', 'event_name')->where(['lead_id' => $booking->lead_id, 'created_by' => $booking->created_by])->get();
            $food_preferences = foodPreference::select('name')->where('member_id', $booking->created_by)->get();
            $party_areas = partyArea::select('name')->where('member_id', $booking->created_by)->get();

            return response()->json(['success' => true, 'alert_type' => 'error', 'booking' => $booking, 'predefined_events' => $predefined_events, 'food_preferences' => $food_preferences, 'party_areas' => $party_areas]);
        } catch (\Throwable $th) {
            return response()->json(['success' => true, 'alert_type' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function manage_process(Request $request, $booking_id) {
        $validate = Validator::make($request->all(), [
            'predefined_event' => 'required|int|exists:vm_events,id',
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

        $booking = Booking::find($booking_id);

        $event_datetime = $request->event_date . " " . date('H:i:s');

        $event = VmEvent::find($request->predefined_event);
        $event->event_name = $request->event_name;
        $event->event_datetime = $event_datetime;
        $event->pax = $request->number_of_guest;
        $event->budget = $request->total_gmv;
        $event->food_preference = $request->food_Preference;
        $event->event_slot = $request->event_slot;
        $event->save();

        $lead_forward = LeadForward::where(['lead_id' => $booking->lead_id, 'forward_to' => $booking->created_by])->first();
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
        $booking->event_id = $request->predefined_event;
        $booking->party_area = $request->party_area;
        $booking->menu_selected = $request->menu_selected;
        $booking->price_per_plate = $request->price_per_plate;
        $booking->total_gmv = $request->total_gmv;
        $booking->advance_amount = $total_advance_amount;
        $booking->quarter_advance_collected = $quarter_advance <= $total_advance_amount;
        $booking->save();

        $lead_forward->event_datetime = $event_datetime;
        $lead_forward->booking_id = $booking->id;
        $lead_forward->save();

        $lead = Lead::find($booking->lead_id);
        $lead->pax = $request->number_of_guest; // this update is used for manager crm, when he/she will find filter data. check database table column and his/her crm panel.
        $lead->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Booking updated successfully."]);
        return redirect()->back();
    }

    function delete($booking_id) {
        try {
            Booking::find($booking_id)->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Booking deleted successfully."]);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
        }
        return redirect()->back();
    }

    function fetch_vm_event($event_id) {
        try {
            $event = VmEvent::find($event_id);
            return response()->json(['success' => true, 'alert_type' => 'success', 'event' => $event]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
    function fetch_team_event($event_id) {
        try {
            $event = Event::find($event_id);
            return response()->json(['success' => true, 'alert_type' => 'success', 'event' => $event]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
