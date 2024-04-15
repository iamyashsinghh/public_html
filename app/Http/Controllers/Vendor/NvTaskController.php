<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\nvLeadForward;
use App\Models\nvTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NvTaskController extends Controller {
    public function list(Request $request) {
        $filter_params = "";
        if ($request->task_status != null) {
            $filter_params =  ['task_status' => $request->task_status];
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
        return view('vendor.task.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('vendor')->user();
        $tasks = nvLeadForward::select(
            'nv_lead_forwards.lead_id',
            'nv_lead_forwards.lead_datetime',
            'nv_lead_forwards.name',
            'nv_lead_forwards.mobile',
            'nv_lead_forwards.lead_status',
            'nv_lead_forwards.read_status',
            'tasks.task_schedule_datetime',
            'nv_lead_forwards.event_datetime',
            'tasks.created_at as task_created_datetime',
            'tasks.done_datetime as task_done_datetime',
        )->join('nv_tasks as tasks', ['nv_lead_forwards.task_id' => 'tasks.id'])->where(['nv_lead_forwards.forward_to' => $auth_user->id, 'tasks.deleted_at' => null])->whereNotNull('nv_lead_forwards.task_id');

        $current_date = date('Y-m-d');
        if ($request->task_status == "Upcoming") {
            $tasks->where('tasks.task_schedule_datetime', '>', Carbon::today())->whereNull('tasks.done_datetime');
        } elseif ($request->task_status == "Today") {
            $tasks->where('tasks.task_schedule_datetime', 'like', "%$current_date%")->whereNull('tasks.done_datetime');
        } elseif ($request->task_status == "Overdue") {
            $tasks->where('tasks.task_schedule_datetime', '<', Carbon::today())->whereNull('tasks.done_datetime');
        } elseif ($request->task_status == "Done") {
            $tasks->whereNotNull('tasks.done_datetime');
        } elseif ($request->task_created_from_date) {
            $from = Carbon::make($request->task_created_from_date);
            if ($request->task_created_to_date != null) {
                $to = Carbon::make($request->task_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_created_from_date)->endOfDay();
            }
            $tasks->whereBetween('tasks.created_at', [$from, $to]);
        } elseif ($request->task_done_from_date) {
            $from = Carbon::make($request->task_done_from_date);
            if ($request->task_done_to_date != null) {
                $to = Carbon::make($request->task_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_done_from_date)->endOfDay();
            }
            $tasks->whereBetween('tasks.done_datetime', [$from, $to]);
        } elseif ($request->task_schedule_from_date) {
            $from = Carbon::make($request->task_schedule_from_date);
            if ($request->task_schedule_to_date != null) {
                $to = Carbon::make($request->task_schedule_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_schedule_from_date)->endOfDay();
            }
            $tasks->whereBetween('tasks.task_schedule_datetime', [$from, $to]);
        }

        $tasks = $tasks->get();
        return datatables($tasks)->toJson();
    }

    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:nv_leads,id',
            'task_schedule_datetime' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $exist_task = nvTask::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if ($exist_task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active task, please complete it first.']);
            return redirect()->back();
        }

        $task = new nvTask();
        $task->lead_id = $request->lead_id;
        $task->created_by = $auth_user->id;
        $task->task_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->task_schedule_datetime));
        $task->follow_up = $request->task_follow_up;
        $task->message = $request->task_message;
        $task->save();

        $forward = nvLeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $auth_user->id])->first();
        $forward->task_id = $task->id;
        $forward->read_status = true;
        $forward->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task created successfully.']);
        return redirect()->back();
    }

    public function status_update(Request $request, $task_id) {
        $validate = Validator::make($request->all(), [
            'task_done_with' => 'required|string',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $task = nvTask::where(['id' => $task_id, 'created_by' => $auth_user->id])->first();

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


    public function delete($task_id) {
        $auth_user = Auth::guard('vendor')->user();
        $task = nvTask::where(['id' => $task_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if (!$task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        $task->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task Deleted.']);
        return redirect()->back();
    }
}
