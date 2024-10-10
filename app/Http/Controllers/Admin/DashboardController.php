<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardStatistics;

class DashboardController extends Controller
{
    public function index()
    {
        $data = DashboardStatistics::where(['type' => 'dashboard'])->first();

        // Decode JSON as an object (which allows access using '->')
        $decodedData = json_decode($data['data'], false);

        // Pass the decoded data as it is (object form) to the view
        return view('admin.dashboard', (array) $decodedData);
    

        // return view('admin.dashboard', [
        //     'total_vendors' => $decodedData['total_vendors'],
        //     'total_team' => $decodedData['total_team'],
        //     'total_venue_leads' => $decodedData['total_venue_leads'],
        //     'total_nv_leads' => $decodedData['total_nv_leads'],
        //     'venue_leads_for_this_month' => $decodedData['venue_leads_for_this_month'],
        //     'venue_form_leads_for_this_month' => $decodedData['venue_form_leads_for_this_month'],
        //     'venue_ads_leads_for_this_month' => $decodedData['venue_ads_leads_for_this_month'],
        //     'venue_organic_leads_for_this_month' => $decodedData['venue_organic_leads_for_this_month'],
        //     'venue_ads_leads_for_this_year' => $decodedData['venue_ads_leads_for_this_year'],
        //     'venue_organic_leads_for_this_year' => $decodedData['venue_organic_leads_for_this_year'],
        //     'venue_call_leads_for_this_month' => $decodedData['venue_call_leads_for_this_month'],
        //     'venue_whatsapp_leads_for_this_month' => $decodedData['venue_whatsapp_leads_for_this_month'],
        //     'venue_leads_for_this_year' => $decodedData['venue_leads_for_this_year'],
        //     'nv_leads_for_this_month' => $decodedData['nv_leads_for_this_month'],
        //     'nv_leads_for_this_year' => $decodedData['nv_leads_for_this_year'],
        //     'vm_members' => $decodedData['vm_members'],
        //     'rm_members' => $decodedData['rm_members'],
        //     'yearly_calendar' => $decodedData['yearly_calendar'],
        //     'v_members' => $decodedData['v_members'],
        //     'nv_members' => $decodedData['nv_members'],
        //     'categories' => $decodedData['categories'],
        // ]);
        }
}
