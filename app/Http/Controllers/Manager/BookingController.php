<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LeadForward;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        return view('manager.venueCrm.bookings.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('manager')->user();

        $vm_members = TeamMember::select('id', 'name', 'venue_name')->where('parent_id', $auth_user->id)->get();
        $vms_id = [];
        foreach ($vm_members as $list) {
            array_push($vms_id, $list->id);
        }

        $bookings = LeadForward::select(
            'lead_forwards.lead_id',
            'bookings.created_at',
            'bookings.booking_source',
            'team_members.venue_name',
            'team_members.name as vm_name',
            'lead_forwards.name',
            'lead_forwards.mobile',
            'events.event_datetime',
            'events.pax',
            'bookings.total_gmv',
            'bookings.advance_amount',
            'bookings.quarter_advance_collected',
            'events.event_name',
        )->join('bookings', 'bookings.id', 'lead_forwards.booking_id')
            ->join('vm_events as events', 'events.id', 'bookings.event_id')
            ->join('team_members', 'team_members.id', 'lead_forwards.forward_to')
            ->whereNull('bookings.deleted_at')->whereIn('lead_forwards.forward_to', $vms_id);

        if ($request->booking_source != null) {
            $bookings->where('bookings.booking_source', $request->booking_source);
        } else if ($request->quarter_advance_collected != null) {
            $bookings->where('bookings.quarter_advance_collected', $request->quarter_advance_collected);
        } elseif ($request->booking_from_date != null) {
            $from = Carbon::make($request->booking_from_date);
            if ($request->booking_to_date != null) {
                $to = Carbon::make($request->booking_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->booking_from_date)->endOfDay();
            }
            $bookings->whereBetween('bookings.created_at', [$from, $to]);
        } elseif ($request->event_from_date != null) {
            $from = Carbon::make($request->event_from_date);
            if ($request->event_to_date != null) {
                $to = Carbon::make($request->event_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->event_from_date)->endOfDay();
            }
            $bookings->whereBetween('events.event_datetime', [$from, $to]);
        }elseif ($request->pax_min_value != null) {
            $min = $request->pax_min_value;
            if ($request->pax_max_value != null) {
                $max = $request->pax_max_value;
            } else {
                $max = $request->pax_min_value;
            }
            $bookings->whereBetween('events.pax', [$min, $max]);
        } elseif ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "bookings_this_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $bookings->whereBetween('bookings.created_at', [$from, $to]);
            }
        }
        return datatables($bookings)->make(false);
    }
}
