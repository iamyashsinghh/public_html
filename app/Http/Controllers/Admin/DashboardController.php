<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardStatistics;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = DashboardStatistics::where(['type' => 'dashboard'])->first();

        $decodedData = json_decode($data['data'], false);

        return view('admin.dashboard', (array) $decodedData);
    }

    public function getVenueChartData(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $monthName = "$month $year";

        $statistics = DashboardStatistics::where('type', 'LIKE', 'admin%')
            ->where('time_period', $monthName)
            ->first();

        if (!$statistics) {
            return response()->json([
                'error' => 'No data available for the selected month.',
            ], 404);
        }

        $data = json_decode($statistics->data, true);

        return response()->json([
            'venue_leads' => $data['venue_leads'] ?? [],
            'form_leads' => $data['form_leads'] ?? [],
            'ads_leads' => $data['ads_leads'] ?? [],
            'organic_leads' => $data['organic_leads'] ?? [],
            'call_leads' => $data['call_leads'] ?? [],
            'whatsapp_leads' => $data['whatsapp_leads'] ?? [],
        ]);
    }
}
