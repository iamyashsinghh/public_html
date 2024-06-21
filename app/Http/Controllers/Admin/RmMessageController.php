<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\nvrmMessage;
use App\Models\RmMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RmMessageController extends Controller
{
    public function edit_nvrm(Request $request, $rm_msg_id)
    {
        $msg = nvrmMessage::where(['id' => $rm_msg_id])->first();
        if ($msg) {
            $msg->title = $request->title;
            $msg->message = $request->message;
            $msg->budget = $request->budget;
            $msg->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'RM message Updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
        return redirect()->back();
    }


    public function delete_nvrm($rm_msg_id)
    {
        $msg = nvrmMessage::where(['id' => $rm_msg_id])->first();
        if ($msg) {
            $msg->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'RM message Deleted.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
        return redirect()->back();
    }

    public function edit_rm(Request $request, $rm_msg_id)
    {
        $msg = RmMessage::where(['id' => $rm_msg_id])->first();
        if ($msg) {
            $msg->title = $request->title;
            $msg->message = $request->message;
            $msg->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'RM message Updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
        return redirect()->back();
    }


    public function delete_rm($rm_msg_id)
    {
        $msg = RmMessage::where(['id' => $rm_msg_id])->first();
        if ($msg) {
            $msg->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'RM message Deleted.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
        return redirect()->back();
    }
}
