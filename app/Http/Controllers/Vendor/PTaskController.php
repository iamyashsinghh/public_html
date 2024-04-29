<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PVendorLead;
use App\Models\PVendorTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PTaskController extends Controller
{
    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:p_vendor_leads,id',
            'task_schedule_datetime' => 'required|date',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $exist_task = PVendorTask::where(['lead_id' => $request->lead_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if ($exist_task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'warning', 'message' => 'This lead has an active task, please complete it first.']);
            return redirect()->back();
        }

        $task = new PVendorTask();
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
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('vendor')->user();
        $task = PVendorTask::where(['id' => $task_id, 'created_by' => $auth_user->id])->first();

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
        $task = PVendorTask::where(['id' => $task_id, 'created_by' => $auth_user->id, 'done_datetime' => null])->first();
        if (!$task) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        $task->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Task Deleted.']);
        return redirect()->back();
    }
}
