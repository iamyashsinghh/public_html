<?php

namespace App\Console\Commands;

use App\Models\DashboardStatistics;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SaveDashboardHistoricalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:save-historical-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
         for ($m = 1; $m < 13; $m++) {
            $monthName = Carbon::now()->subMonthsNoOverflow($m)->format('F Y');

            $venue_leads = [];
            $form_leads = [];
            $ads_leads = [];
            $organic_leads = [];
            $call_leads = [];
            $whatsapp_leads = [];
            $avarage_leads = '';

            // Get the number of days in the month
            $daysInMonth = Carbon::now()->subMonthsNoOverflow($m)->daysInMonth;

            // Loop through each day of the month
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $currentDate = Carbon::now()->subMonthsNoOverflow($m)->format('Y-m-') . sprintf("%02d", $i);

                // Fetch counts for each metric
                $venue_leads[] = Lead::where('lead_datetime', 'like', "%$currentDate%")
                    ->whereIn('source', ['WB|Call', 'WB|Form', 'WB|WhatsApp'])
                    ->count();
                $form_leads[] = Lead::where('lead_datetime', 'like', "%$currentDate%")
                    ->where('source', 'WB|Form')
                    ->count();
                $ads_leads[] = Lead::where('lead_datetime', 'like', "%$currentDate%")
                    ->where('is_ad', 1)
                    ->count();
                $organic_leads[] = Lead::where('lead_datetime', 'like', "%$currentDate%")
                    ->where('is_ad', 0)
                    ->count();
                $call_leads[] = Lead::where('lead_datetime', 'like', "%$currentDate%")
                    ->where('source', 'WB|Call')
                    ->count();
                $whatsapp_leads[] = Lead::where('lead_datetime', 'like', "%$currentDate%")
                    ->where('source', 'WB|WhatsApp')
                    ->count();
            }

            $venue_leads = implode(',', $venue_leads);

        $avarage_leads = array_sum(explode(",", $venue_leads)) / date('d');

            $metrics = [
                'avarage_leads' => $avarage_leads,
                'venue_leads' => $venue_leads,
                'form_leads' => implode(',', $form_leads),
                'ads_leads' => implode(',', $ads_leads),
                'organic_leads' => implode(',', $organic_leads),
                'call_leads' => implode(',', $call_leads),
                'whatsapp_leads' => implode(',', $whatsapp_leads),
            ];

            DashboardStatistics::updateOrCreate(
                [
                    'type' => "admin$monthName",
                    'time_period' => $monthName,
                ],
                [
                    'data' => json_encode($metrics),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            echo "Data for $monthName saved successfully.\n";
        }
    }
}
