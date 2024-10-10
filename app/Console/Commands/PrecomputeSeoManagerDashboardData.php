<?php

namespace App\Console\Commands;

use App\Models\DashboardStatistics;
use App\Models\Lead;
use Illuminate\Console\Command;

class PrecomputeSeoManagerDashboardData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seomanagerdashboard:precompute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Precompute dashboard data and store it to avoid recalculating on every load';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $yearly_calendar = [];
        for ($i = 12; $i >= 0; $i--) {
            $date = date('Y-M', strtotime("-$i month"));
            array_push($yearly_calendar, $date);
        }
        $yearly_calendar = implode(",", $yearly_calendar);

        $venue_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->whereIn('source', ['WB|Call', 'WB|Form', 'WB|WhatsApp'])->count();
            array_push($venue_leads_for_this_month, $count);
        }
        $venue_leads_for_this_month = implode(",", $venue_leads_for_this_month);

        $venue_form_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Form')->count();
            array_push($venue_form_leads_for_this_month, $count);
        }
        $venue_form_leads_for_this_month = implode(",", $venue_form_leads_for_this_month);

        $venue_whatsapp_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|WhatsApp')->count();
            array_push($venue_whatsapp_leads_for_this_month, $count);
        }
        $venue_whatsapp_leads_for_this_month = implode(",", $venue_whatsapp_leads_for_this_month);

        $venue_ads_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '1')->count();
            array_push($venue_ads_leads_for_this_month, $count);
        }
        $venue_ads_leads_for_this_month = implode(",", $venue_ads_leads_for_this_month);

        $venue_organic_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '0')->count();
            array_push($venue_organic_leads_for_this_month, $count);
        }
        $venue_organic_leads_for_this_month = implode(",", $venue_organic_leads_for_this_month);

        $venue_call_leads_for_this_month = [];
        for ($i = 1; $i <= date('d'); $i++) {
            $datetime = date("Y-m-d", strtotime(date('Y-m-') . $i));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Call')->count();
            array_push($venue_call_leads_for_this_month, $count);
        }
        $venue_call_leads_for_this_month = implode(",", $venue_call_leads_for_this_month);

        $venue_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->whereIn('source', ['WB|Call', 'WB|Form', 'WB|WhatsApp'])->count();
            array_push($venue_leads_for_this_year, $count);
        }
        $venue_leads_for_this_year = implode(",", $venue_leads_for_this_year);

        $venue_organic_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '0')->count();
            array_push($venue_organic_leads_for_this_year, $count);
        }
        $venue_organic_leads_for_this_year = implode(",", $venue_organic_leads_for_this_year);

        $venue_ads_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('is_ad', '1')->count();
            array_push($venue_ads_leads_for_this_year, $count);
        }
        $venue_ads_leads_for_this_year = implode(",", $venue_ads_leads_for_this_year);

        $venue_call_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Call')->count();
            array_push($venue_call_leads_for_this_year, $count);
        }
        $venue_call_leads_for_this_year = implode(",", $venue_call_leads_for_this_year);
        $venue_whatsapp_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|WhatsApp')->count();
            array_push($venue_whatsapp_leads_for_this_year, $count);
        }
        $venue_whatsapp_leads_for_this_year = implode(",", $venue_whatsapp_leads_for_this_year);

        $venue_form_leads_for_this_year = [];
        for ($i = 12; $i >= 0; $i--) {
            $datetime = date("Y-m", strtotime("-$i month"));
            $count = Lead::where('lead_datetime', 'like', "%$datetime%")->where('source', 'WB|Form')->count();
            array_push($venue_form_leads_for_this_year, $count);
        }
        $venue_form_leads_for_this_year = implode(",", $venue_form_leads_for_this_year);



        $data = [
            'venue_leads_for_this_month' => $venue_leads_for_this_month,
            'venue_form_leads_for_this_month' => $venue_form_leads_for_this_month,
            'venue_ads_leads_for_this_month' => $venue_ads_leads_for_this_month,
            'venue_organic_leads_for_this_month' => $venue_organic_leads_for_this_month,
            'venue_call_leads_for_this_month' => $venue_call_leads_for_this_month,
            'venue_whatsapp_leads_for_this_month' => $venue_whatsapp_leads_for_this_month,

            'venue_form_leads_for_this_year' => $venue_form_leads_for_this_year,
            'venue_whatsapp_leads_for_this_year' => $venue_whatsapp_leads_for_this_year,
            'venue_organic_leads_for_this_year' => $venue_organic_leads_for_this_year,
            'venue_call_leads_for_this_year' => $venue_call_leads_for_this_year,
            'venue_ads_leads_for_this_year' => $venue_ads_leads_for_this_year,
            'venue_leads_for_this_year' => $venue_leads_for_this_year,
            'yearly_calendar' => $yearly_calendar,
        ];

        DashboardStatistics::updateOrCreate(
            ['type' => 'seomanagerdashboard'],
            ['data' => json_encode($data)]
        );

        // Success message
        $this->info('Dashboard data has been precomputed and stored.');
    }
}
