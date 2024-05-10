<?php

namespace App\Http\Controllers\SeoManager;

use App\Http\Controllers\Controller;
use App\Models\Lead;

class DashboardController extends Controller
{
    public function index() {
        $yearly_calendar = [];
        for ($i = 12; $i >= 0; $i--) {
            $date = date('Y-M', strtotime("-$i month"));
            array_push($yearly_calendar, $date);
        }
        $yearly_calendar = implode(",", $yearly_calendar);

        $venue_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->count();
            array_push($venue_leads_for_this_month, $count);
        }
        $venue_leads_for_this_month = implode(",", $venue_leads_for_this_month);

        $venue_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->count();
            array_push($venue_leads_for_this_year, $count);
        }
        $venue_leads_for_this_year = implode(",", $venue_leads_for_this_year);
        return view('seomanager.dashboard', compact('venue_leads_for_this_month', 'venue_leads_for_this_year','yearly_calendar'));
    }

}
