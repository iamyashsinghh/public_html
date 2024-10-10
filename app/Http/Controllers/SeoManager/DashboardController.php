<?php

namespace App\Http\Controllers\SeoManager;

use App\Http\Controllers\Controller;
use App\Models\DashboardStatistics;
use App\Models\Lead;

class DashboardController extends Controller
{
    public function index()
    {
        $data = DashboardStatistics::where(['type' => 'seomanagerdashboard'])->first();

        $decodedData = json_decode($data['data'], false);

        return view('seomanager.dashboard', (array) $decodedData);
    }
}
