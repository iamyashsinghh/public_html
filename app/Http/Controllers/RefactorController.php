<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\partyArea;
use App\Models\Task;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Http\Request;


class RefactorController extends Controller {
    // public function availablity() {

    //     $calendar = [];
    //     for ($i = 0; $i <= 12; $i++) {
    //         if ($i == 0) {
    //             array_push($calendar, Carbon::today()->addMonth(-1));
    //         } elseif ($i == 1) {
    //             array_push($calendar, Carbon::today()->addMonth(0));
    //         } else {
    //             array_push($calendar, Carbon::today()->addMonth($i - 1));
    //         }
    //     }

    //     return $calendar[0]->endOfMonth()->day;


    //     // $party_areas = ["Ground Floor", "First Floor", "Second Floor"];
    //     // $food_category = ["Lunch", "Dinner"];

    //     // $data = [];
    //     // foreach ($party_areas as $area) {
    //     //     foreach ($food_category as $cat) {
    //     //         $event = ['area_name' => $area . " " . $cat, "year" => date('Y'), 'event_details' => []];
    //     //         for ($i = 1; $i <= 12; $i++) {
    //     //             for ($j = 1; $j <= $carbon->month($i)->endOfMonth()->day; $j++) {
    //     //                 $event_details = [
    //     //                     date('d-m-Y', strtotime("2023-$i-$j")) => [
    //     //                         "selected" => false,
    //     //                         "pax" => null
    //     //                     ],
    //     //                 ];
    //     //                 array_push($event['event_details'], $event_details);
    //     //                 // $event['event_details'] = $event_details;
    //     //             }
    //     //         }
    //     //         array_push($data, $event);
    //     //     }
    //     // }
    //     // return $data;
    // }

    // public function bookings_refactor() {
    //     $bookings = Booking::all();
    //     foreach ($bookings as $booking) {
    //         $lead_forward = LeadForward::where(['lead_id' => $booking->lead_id, 'forward_to' => $booking->created_by])->first();
    //         $lead_forward->booking_id = $booking->id;
    //         $lead_forward->save();
    //     }
    //     return "Bookings refactored success";
    // }

    // public function availability_refactor_with_bookings() {
    //     $bookings = Booking::all();

    //     foreach ($bookings as $booking) {
    //         $party_area = partyArea::where(['member_id' => $booking->created_by, 'name' => $booking->party_area])->first();
    //         $event = $booking->get_event;

    //         $availability = Availability::where(['created_by' => $booking->created_by, 'party_area_id' => $party_area->id, 'food_type' => $event->event_slot, 'date' => date('Y-m-d', strtotime($event->event_datetime))])->first();
    //         if (!$availability) {
    //             $availability = new Availability();
    //             $availability->created_by = $booking->created_by;
    //             $availability->party_area_id = $party_area->id;
    //             $availability->food_type = $event->event_slot;
    //             $availability->date = date('Y-m-d', strtotime($event->event_datetime));
    //         }
    //         $availability->pax = $event->pax;
    //         $availability->save();
    //     }
    //     return "Availability refactored successfully.";
    // }
}
