<?php

namespace App\Http\Controllers\Bdm;

use App\Http\Controllers\Controller;
use App\Models\BdmLead;
use App\Models\BdmTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller {
    public function list(Request $request, $dashboard_filters = null) {
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

        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }

        return view('bdm.task.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request) {
        $auth_user = Auth::guard('bdm')->user();
        $tasks = BdmLead::select(
            'bdm_leads.lead_id',
            'bdm_leads.lead_datetime',
            'bdm_leads.name',
            'bdm_leads.mobile',
            'bdm_leads.lead_status',
            'bdm_leads.business_name',
            'vc.name as business_cat',
            'bdm_tasks.task_schedule_datetime',
            'bdm_tasks.created_at as task_created_datetime',
            'bdm_tasks.done_datetime as task_done_datetime',
        )->join('bdm_tasks', ['bdm_leads.lead_id' => 'bdm_tasks.lead_id'])
        ->leftJoin('vendor_categories as vc', 'vc.id', 'bdm_leads.business_cat')
        ->where(['bdm_tasks.created_by' => $auth_user->id, 'bdm_tasks.deleted_at' => null]);

        $current_date = date('Y-m-d');
        if ($request->task_status == "Upcoming") {
            $tasks->where('bdm_tasks.task_schedule_datetime', '>', Carbon::today()->endOfDay());
        } elseif ($request->task_status == "Today") {
            $tasks->where('bdm_tasks.task_schedule_datetime', 'like', "%$current_date%");
        } elseif ($request->task_status == "Overdue") {
            $tasks->where('bdm_tasks.task_schedule_datetime', '<', Carbon::today())->whereNull('bdm_tasks.done_datetime');
        } elseif ($request->task_status == "Done") {
            $tasks->whereNotNull('bdm_tasks.done_datetime');
        }
        if ($request->task_created_from_date) {
            $from = Carbon::make($request->task_created_from_date);
            if ($request->task_created_to_date != null) {
                $to = Carbon::make($request->task_created_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_created_from_date)->endOfDay();
            }
            $tasks->whereBetween('bdm_tasks.created_at', [$from, $to]);
        }

        if ($request->task_done_from_date) {
            $from = Carbon::make($request->task_done_from_date);
            if ($request->task_done_to_date != null) {
                $to = Carbon::make($request->task_done_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_done_from_date)->endOfDay();
            }
            $tasks->whereBetween('bdm_tasks.done_datetime', [$from, $to]);
        }
        if ($request->task_schedule_from_date) {
            $from = Carbon::make($request->task_schedule_from_date);
            if ($request->task_schedule_to_date != null) {
                $to = Carbon::make($request->task_schedule_to_date)->endOfDay();
            } else {
                $to = Carbon::make($request->task_schedule_from_date)->endOfDay();
            }
            $tasks->whereBetween('bdm_tasks.task_schedule_datetime', [$from, $to])->whereNull('bdm_tasks.done_datetime');
        }
        if ($request->dashboard_filters != null) {
            if ($request->dashboard_filters == "task_schedule_this_month") {
                $from =  Carbon::today()->startOfMonth();
                $to =  Carbon::today()->endOfMonth();
                $tasks->whereBetween('bdm_tasks.task_schedule_datetime', [$from, $to]);
            } elseif ($request->dashboard_filters == "task_schedule_today") {
                $from =  Carbon::today()->startOfDay();
                $to =  Carbon::today()->endOfDay();
                $tasks->whereBetween('bdm_tasks.task_schedule_datetime', [$from, $to])->whereNull('bdm_tasks.done_datetime');
            } elseif ($request->dashboard_filters == "total_task_overdue") {
                $tasks->where('bdm_tasks.task_schedule_datetime', '<', Carbon::today())->whereNull('bdm_tasks.done_datetime');
            }
        }


        $tasks = $tasks->get();
        return datatables($tasks)->toJson();
    }

    // public function manage_ajax($task_id) {
    //     $task = BdmTask::where(['id' => $task_id, 'created_by' => Auth::guard('bdm')->user()->id, 'done_datetime' => null])->first();
    //     if (!$task) {
    //         return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.'], 500);
    //     } else {
    //         return response()->json(['success' => true, 'task' => $task]);
    //     }
    // }

    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:bdm_leads,lead_id',
            'task_schedule_datetime' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('bdm')->user();


        $exist_task = BdmTask::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();

        if ($exist_task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active task, please complete it first.']);
            return redirect()->back();
        }
        $task = new BdmTask();
        $task->lead_id = $request->lead_id;
        $task->created_by = $auth_user->id;
        $task->task_schedule_datetime = date('Y-m-d H:i:s', strtotime($request->task_schedule_datetime));
        $task->follow_up = $request->task_follow_up;
        $task->message = $request->task_message;
        $task->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task created successfully.']);
        return redirect()->back();
    }

    public function status_update(Request $request, $task_id) {
        $validate = Validator::make($request->all(), [
            'task_done_with' => 'required|string',
            'task_done_message' => 'required|string'
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('bdm')->user();
        if ($auth_user->role_id == 4) {
            $task = BdmTask::find($task_id);
        } else {
            $task = BdmTask::where(['id' => $task_id, 'created_by' => $auth_user->id])->first();
        }

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
        $auth_user = Auth::guard('bdm')->user();
        $task = BdmTask::where(['id' => $task_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if (!$task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        $task->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task Deleted.']);
        return redirect()->back();
    }
}
