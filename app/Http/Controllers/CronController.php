<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{
    public function vm_recce_today()
    {
        $startDate = Carbon::createFromDate(2024, 8, 1);
        $current_date = Carbon::now()->toDateString();
        $vm_recce_today = Lead::select('leads.lead_id', 'leads.lead_status')
            ->join('visits', 'visits.lead_id', '=', 'leads.lead_id')
            ->leftJoin('rm_messages', 'rm_messages.lead_id', '=', 'leads.lead_id')
            ->leftJoin('tasks', function ($join) {
                $join->on('tasks.lead_id', '=', 'leads.lead_id')
                    ->where('tasks.is_vm_recce_task', 1);
            })
            ->whereDate('visits.done_datetime', '>=', $startDate)
            ->whereNotNull('visits.done_datetime')
            ->whereDate(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '>', DB::raw('tasks.created_at'))
            ->whereDate(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '=', $current_date)
            ->orderBy('rm_messages.updated_at', 'desc')
            ->groupBy('leads.lead_id')
            ->get();

        foreach ($vm_recce_today as $lead) {
            if ($lead->lead_status == 'Done') {
                $lead->lead_status = 'Active';
                $lead->done_title = null;
                $lead->done_message = null;
                $lead->lead_color = #ff00001f;
                    $lead->save();
            }
        }
        return $vm_recce_today;
    }
    public function vm_recce_overdue()
    {
        $startDate = Carbon::createFromDate(2024, 8, 1);
        $current_date = Carbon::now()->toDateString();
        $vm_recce_overdue = Lead::select('leads.lead_id', 'leads.lead_status')
            ->join('visits', 'visits.lead_id', '=', 'leads.lead_id')
            ->leftJoin('rm_messages', 'rm_messages.lead_id', '=', 'leads.lead_id')
            ->leftJoin('tasks', function ($join) {
                $join->on('tasks.lead_id', '=', 'leads.lead_id')
                    ->where('tasks.is_vm_recce_task', 1);
            })
            ->whereNotNull('visits.done_message')
            ->whereDate('visits.done_datetime', '>=', $startDate)
            ->whereDate(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '<', $current_date)
            ->where(function ($query) {
                $query->whereNull('tasks.created_at')
                    ->orWhere(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '>', DB::raw('tasks.created_at'));
            })
            ->orderBy('rm_messages.updated_at', 'desc')
            ->orderBy('visits.done_datetime', 'desc')
            ->groupBy('leads.lead_id')
            ->get();

        foreach ($vm_recce_overdue as $lead) {
            if ($lead->lead_status == 'Done') {
                $lead->lead_status = 'Active';
                $lead->done_title = null;
                $lead->done_message = null;
                $lead->lead_color = #ff00001f;
                    $lead->save();
            }
        }
        return $vm_recce_overdue;
    }
}
