<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\nvrmLeadForward;
use App\Models\nvrmMessage;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{
    public function vm_recce_today()
    {
        $startDate = Carbon::createFromDate(2024, 8, 1);
        $current_date = Carbon::now()->toDateString();
        $users = TeamMember::where('role_id', 4);

        foreach ($users as $user) {
            $vm_recce_today = Lead::select('leads.lead_id', 'leads.lead_status')
                ->join('visits', 'visits.lead_id', '=', 'leads.lead_id')
                ->leftJoin('rm_messages', 'rm_messages.lead_id', '=', 'leads.lead_id')
                ->leftJoin(DB::raw('(SELECT lead_id, MAX(created_at) as latest_task_created_at FROM tasks WHERE deleted_at IS NULL AND created_by = ' . $user->id . ' GROUP BY lead_id) as latest_tasks'), function ($join) {
                    $join->on('latest_tasks.lead_id', '=', 'leads.lead_id');
                })
                ->whereDate('visits.done_datetime', '>=', $startDate)
                ->whereNotNull('visits.done_datetime')
                ->where(function ($query) {
                    $query->whereNull('latest_tasks.latest_task_created_at')
                        ->orWhereDate('latest_tasks.latest_task_created_at', '<', DB::raw('DATE_ADD(visits.done_datetime, INTERVAL 2.4 DAY)'));
                })
                ->whereDate(DB::raw('DATE(DATE_ADD(visits.done_datetime, INTERVAL 3 DAY))'), '=', $current_date)
                ->where('rm_messages.created_by', '=', $user->id)
                ->where('leads.lead_status', '!=', 'Done')
                ->orderBy('rm_messages.updated_at', 'desc')
                ->groupBy('leads.lead_id')
                ->get();

            foreach ($vm_recce_today as $lead) {
                if ($lead->lead_status == 'Done') {
                    $lead->lead_status = 'Active';
                    // $lead->done_title = null;
                    // $lead->done_message = null;
                    $lead->lead_color = '#ff00001f'; // Corrected the color format to be a valid string
                    $lead->save();
                }
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
                $join->on('tasks.lead_id', '=', 'leads.lead_id');
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

    public function delete_leads_no_use()
{
    $leadIds = nvrmLeadForward::distinct()->pluck('lead_id');
    $totalDeletedCount = 0;  // Track total deleted leads count
    $totalUpdatedCount = 0;  // Track total updated leads count

    foreach ($leadIds as $lead_id) {
        $lead = nvrmLeadForward::where('lead_id', $lead_id)->orderBy('id')->first();

        if ($lead) {
            $deletedCount = nvrmLeadForward::where('lead_id', $lead_id)
                ->where('id', '!=', $lead->id)
                ->delete();
            $totalDeletedCount += $deletedCount;

            $latestMessage = nvrmMessage::where('lead_id', $lead_id)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->first();

                if ($latestMessage) {
                $isValidTeamMember = TeamMember::where('id', $latestMessage->created_by)->exists();
                if ($isValidTeamMember) {
                    $lead->forward_to = $latestMessage->created_by;
                    $lead->save();
                    $totalUpdatedCount++;
                }
            }
        }
    }

    echo "Total leads deleted: " . $totalDeletedCount . "\n";
    echo "Total leads updated with the latest message: " . $totalUpdatedCount . "\n";
}


}
