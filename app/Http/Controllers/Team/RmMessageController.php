<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\RmMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RmMessageController extends Controller {
    public function manage_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|int|exists:leads,lead_id',
            'title' => "required|string|max:255",
            'message' => "required|string",
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        try {
            $msg = new RmMessage();
            $msg->lead_id = $request->lead_id;
            $msg->created_by = Auth::guard('team')->user()->id;
            $msg->title = $request->title;
            $msg->message = $request->message;
            $msg->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'RM message added.']);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
        return redirect()->back();
    }
}
