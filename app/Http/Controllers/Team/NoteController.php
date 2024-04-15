<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\LeadForward;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller {
    public function manage_ajax($note_id) {
        $note = Note::where(['id' => $note_id, 'created_by' => Auth::guard('team')->user()->id])->first();
        if (!$note) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.'], 500);
        } else {
            return response()->json(['success' => true, 'note' => $note]);
        }
    }

    public function manage_process(Request $request, $note_id = 0) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,lead_id',
            'note_message' => 'required|string',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('team')->user();
        if ($note_id > 0) {
            $msg = "Note updated successfully.";
            $note = Note::where(['id' => $note_id, 'created_by' => $auth_user->id])->first();
            if (!$note) {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
                return redirect()->back();
            }
        } else {
            $msg = "Note added successfully.";
            $note = new Note();
            $note->lead_id = $request->lead_id;
            $note->created_by = $auth_user->id;
        }

        $note->message = $request->note_message;
        $note->save();

        //updating: lead_forwards;
        if ($auth_user->role_id == 5) {
            $lead_forwards = LeadForward::where(['lead_id' => $request->lead_id, 'forward_to' => $auth_user->id])->first();
            $lead_forwards->read_status = true;
            $lead_forwards->save();
        }
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }

    public function delete($note_id) {
        $auth_user = Auth::guard('team')->user();
        $note = Note::where(['id' => $note_id, 'created_by' => $auth_user->id])->first();
        if (!$note) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }

        $note->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Note Deleted.']);
        return redirect()->back();
    }
}
