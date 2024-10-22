<?php

namespace App\Http\Controllers\NonVenue;

use App\Http\Controllers\Controller;
use App\Models\nvrmLeadForward;
use App\Models\nvrmTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function list(Request $request, $dashboard_filters = null)
    {
        $filter_params = "";
        if ($request->task_status != null) {
            $filter_params = ['task_status' => $request->task_status];
        }
        if ($request->task_created_from_date != null) {
            $filter_params = ['task_created_from_date' => $request->task_created_from_date, 'task_created_to_date' => $request->task_created_to_date];
        }
        if ($request->task_done_from_date != null) {
            $filter_params = ['task_done_from_date' => $request->task_done_from_date, 'task_done_to_date' => $request->task_done_to_date];
        }
        if ($request->task_schedule_from_date != null) {
            $filter_params = ['task_schedule_from_date' => $request->task_schedule_from_date, 'task_schedule_to_date' => $request->task_schedule_to_date];
        }

        $page_heading = $filter_params ? "Tasks - Filtered" : "Tasks";

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }

        return view('nonvenue.task.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request)
    {
        $auth_user = Auth::guard('nonvenue')->user();
        $latestTask = nvrmTask::select('lead_id', DB::raw('MAX(created_at) as latest_created_at'))
            ->whereNull('done_datetime')
            ->whereNull('deleted_at')
            ->groupBy('lead_id');

        $tasks = nvrmLeadForward::select(
            'nvrm_lead_forwards.lead_id',
            'nvrm_lead_forwards.lead_datetime',
            'nvrm_lead_forwards.name',
            'nvrm_lead_forwards.mobile',
            'nvrm_lead_forwards.lead_status',
            'nvrm_tasks.task_schedule_datetime',
            'nvrm_lead_forwards.event_datetime',
            'nvrm_tasks.created_at as task_created_datetime',
            'nvrm_tasks.done_datetime as task_done_datetime'
        )
            ->joinSub($latestTask, 'latest_task', function ($join) {
                $join->on('nvrm_lead_forwards.lead_id', '=', 'latest_task.lead_id');
            })
            ->join('nvrm_tasks', function ($join) {
                $join->on('nvrm_lead_forwards.lead_id', '=', 'nvrm_tasks.lead_id')
                    ->on('nvrm_tasks.created_at', '=', 'latest_task.latest_created_at');
            })
            ->where(['nvrm_tasks.created_by' => $auth_user->id])
            ->whereNull('nvrm_lead_forwards.deleted_at')
            ->where('nvrm_lead_forwards.lead_status', '!=', 'done');



        $current_date = date('Y-m-d');
        if ($request->task_status == "Upcoming") {
            $tasks->where('nvrm_tasks.task_schedule_datetime', '>', Carbon::today()->endOfDay());
        } elseif ($request->task_status == "Today") {
            $tasks->where('nvrm_tasks.task_schedule_datetime', 'like', "%$current_date%");
        } elseif ($request->task_status == "Overdue") {
            $tasks->where('nvrm_tasks.task_schedule_datetime', '<', Carbon::today())->whereNull('nvrm_tasks.done_datetime');
        } elseif ($request->task_status == "Done") {
            $tasks->whereNotNull('nvrm_tasks.done_datetime');
        } elseif ($request->task_created_from_date) {
            $from = Carbon::make($request->task_created_from_date);
            if ($request->task_created_to_date != null) {
                $to = Carbon::make($request->task_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_created_from_date)->endOfDay();
            }
            $tasks->whereBetween('nvrm_tasks.created_at', [$from, $to]);
        } elseif ($request->task_done_from_date) {
            $from = Carbon::make($request->task_done_from_date);
            if ($request->task_done_to_date != null) {
                $to = Carbon::make($request->task_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_done_from_date)->endOfDay();
            }
            $tasks->whereBetween('nvrm_tasks.done_datetime', [$from, $to]);
        } elseif ($request->task_schedule_from_date) {
            $from = Carbon::make($request->task_schedule_from_date);
            if ($request->task_schedule_to_date != null) {
                $to = Carbon::make($request->task_schedule_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_schedule_from_date)->endOfDay();
            }
            $tasks->whereBetween('nvrm_tasks.task_schedule_datetime', [$from, $to])->whereNull('nvrm_tasks.done_datetime');
        } elseif ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "task_schedule_this_month") {
                $from = Carbon::today()->startOfMonth();
                $to = Carbon::today()->endOfMonth();
                $tasks->whereBetween('nvrm_tasks.task_schedule_datetime', [$from, $to]);
            } elseif ($request->dashboard_filters == "task_schedule_today") {
                $from = Carbon::today()->startOfDay();
                $to = Carbon::today()->endOfDay();
                $tasks->whereBetween('nvrm_tasks.task_schedule_datetime', [$from, $to])->whereNull('nvrm_tasks.done_datetime');
            } elseif ($request->dashboard_filters == "total_task_overdue") {
                $tasks->where('nvrm_tasks.task_schedule_datetime', '<', Carbon::today())->whereNull('nvrm_tasks.done_datetime');
            }
        }

        $tasks = $tasks->get();
        return datatables($tasks)->toJson();
    }

    public function add_process(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:nvrm_lead_forwards,lead_id',
            'task_schedule_datetime' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('nonvenue')->user();

        $exist_task = nvrmTask::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();

        if ($exist_task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active task, please complete it first.']);
            return redirect()->back();
        }

        $task = new nvrmTask();
        $task->lead_id = $request->lead_id;
        $task->created_by = $auth_user->id;
        $task->task_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->task_schedule_datetime));
        $task->follow_up = $request->task_follow_up;
        $task->message = $request->task_message;
        $task->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task created successfully.']);
        return redirect()->back();
    }

    public function status_update(Request $request, $task_id)
    {
        $validate = Validator::make($request->all(), [
            'task_done_with' => 'required|string',
            'task_done_message' => 'required|string',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('nonvenue')->user();

        $task = nvrmTask::where(['id' => $task_id, 'created_by' => $auth_user->id])->first();

        if (!$task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }

        $task->done_with = $request->task_done_with;
        $task->done_message = $request->task_done_message;
        $task->done_datetime = date('Y-m-d H:i:s');
        $task->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task status updated.']);
        return redirect()->back();
    }

    public function delete($task_id)
    {
        $auth_user = Auth::guard('nonvenue')->user();
        $task = nvrmTask::where(['id' => $task_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if (!$task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        $task->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task Deleted.']);
        return redirect()->back();
    }
}
