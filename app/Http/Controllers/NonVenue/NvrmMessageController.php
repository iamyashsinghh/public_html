<?php

namespace App\Http\Controllers\NonVenue;

use App\Http\Controllers\Controller;
use App\Models\nvrmMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NvrmMessageController extends Controller {
    public function manage_process(Request $request) {
        $is_budget_valid = $request->budget !== null ? 'required|int' : '';
        $is_message_valid = $request->message !== null ? 'required|string' : '';
        $validate = Validator::make($request->all(), [
            'service_category' => 'required|int|exists:vendor_categories,id',
            'lead_id' => 'required|int|exists:nv_leads,id',
            'title' => "required|string|max:255",
            'budget' => $is_budget_valid,
            'message' => $is_message_valid,
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        try {
            $msg = new nvrmMessage();
            $msg->lead_id = $request->lead_id;
            $msg->vendor_category_id = $request->service_category;
            $msg->created_by = Auth::guard('nonvenue')->user()->id;
            $msg->title = $request->title;
            $msg->message = $request->message;
            $msg->budget = $request->budget;
            $msg->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'RM message added.']);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
        return redirect()->back();
    }

    public function edit(Request $request, $rm_msg_id)
    {
        $auth_user = Auth::guard('nonvenue')->user();
        $msg = nvrmMessage::where(['id' => $rm_msg_id, 'created_by' => $auth_user->id])->first();
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


    public function delete($rm_msg_id)
    {
        $auth_user = Auth::guard('nonvenue')->user();
        $msg = nvrmMessage::where(['id' => $rm_msg_id, 'created_by' => $auth_user->id])->first();
        if ($msg) {
            $msg->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'RM message Deleted.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
        return redirect()->back();
    }


}
