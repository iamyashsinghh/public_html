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
use Illuminate\Support\Facades\DB;

class RefactorController extends Controller
{

    // public function yello_lead()
    // {
    //     $leads = DB::table('leads')
    //         ->select(
    //             'leads.lead_id as lead_id',
    //             'leads.lead_datetime',
    //             'leads.name',
    //             'leads.mobile',
    //             'leads.event_datetime as event_date',
    //             'leads.lead_status',
    //             'leads.service_status',
    //             'leads.read_status',
    //             'leads.lead_color',
    //             'leads.assign_to',
    //             'leads.source',
    //             'leads.preference',
    //             'leads.locality',
    //             'tm.name as created_by',
    //             'leads.whatsapp_msg_time',
    //             'leads.last_forwarded_by',
    //             'leads.enquiry_count',
    //             'leads.is_whatsapp_msg',
    //             DB::raw("(select count(fwd.id) from lead_forward_infos as fwd where fwd.lead_id = leads.lead_id group by fwd.lead_id) as forwarded_count")
    //         )
    //         ->join('tasks', 'tasks.lead_id', '=', 'leads.lead_id')
    //         ->leftJoin('team_members as tm', 'tm.id', '=', 'leads.created_by')
    //         ->where(function ($query) {
    //             $query->whereNull('leads.done_message')
    //                 ->orWhere('leads.done_message', '=', '');
    //         })
    //         ->where(function ($query) {
    //             $query->whereNull('leads.last_forwarded_by')
    //                 ->orWhere('leads.last_forwarded_by', '=', '');
    //         })
    //         ->whereNull('leads.deleted_at')
    //         ->where('leads.lead_status', '!=', 'Done')->whereDate('leads.lead_datetime', '>=', '2024-10-01')
    //         ->groupBy('leads.lead_id')->pluck('leads.lead_id');

    //     DB::table('leads')
    //         ->whereIn('lead_id', $leads)
    //         ->update(['lead_color' => '#ffff0050']);

    //     return $leads = DB::table('leads')
    //         ->join('tasks', 'tasks.lead_id', '=', 'leads.lead_id')
    //         ->whereNull('leads.done_message')
    //         ->whereNull('leads.last_forwarded_by')
    //         ->whereNull('leads.deleted_at')
    //         ->where('leads.lead_status', '!=', 'Done')
    //         ->whereDate('leads.lead_datetime', '>=', '2024-10-01')
    //         ->groupBy('leads.lead_id')
    //         ->pluck('leads.lead_id');
    // }

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

    public function deelte_old_availabilities()
    {
        $availabilities = Availability::get();

        $groupedAvailabilities = $availabilities->groupBy(function ($item) {
            return $item->created_by . '-' . $item->party_area_id . '-' . $item->food_type . '-' . $item->date;
        });

        $deletedRecords = [];
        $keptRecords = [];
        $idsToDelete = [];
        foreach ($groupedAvailabilities as $group) {
            $sortedGroup = $group->sortByDesc('id');

            $latestRecord = $sortedGroup->first();
            $keptRecords[] = $latestRecord;

            $toDelete = $sortedGroup->slice(1)->pluck('id')->toArray();
            $idsToDelete = array_merge($idsToDelete, $toDelete);

            $deletedRecords = array_merge($deletedRecords, $sortedGroup->slice(1)->toArray());
        }

        Availability::whereIn('id', $idsToDelete)->delete();

        $response = [
            'status' => 'success',
            'message' => 'Older records deleted successfully, latest records kept.',
            'deleted_records' => $deletedRecords,
            'kept_records' => $keptRecords,
        ];

        return response()->json($response);
    }
}
