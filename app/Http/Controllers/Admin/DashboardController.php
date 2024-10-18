<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardStatistics;

class DashboardController extends Controller
{
    public function index()
    {
        $data = DashboardStatistics::where(['type' => 'dashboard'])->first();

        $decodedData = json_decode($data['data'], false);

        return view('admin.dashboard', (array) $decodedData);

        }
}
