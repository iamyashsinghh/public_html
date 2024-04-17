<?php

namespace App\Http\Controllers\Bdm;

use App\Http\Controllers\Controller;
use App\Models\BdmNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    public function manage_ajax($note_id) {
        $note = BdmNote::where(['id' => $note_id, 'created_by' => Auth::guard('bdm')->user()->id])->first();
        if (!$note) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.'], 500);
        } else {
            return response()->json(['success' => true, 'note' => $note]);
        }
    }

    public function manage_process(Request $request, $note_id = 0) {

        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:bdm_leads,lead_id',
            'note_message' => 'required|string',
        ]);
        
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $auth_user = Auth::guard('bdm')->user();
        if ($note_id > 0) {
            $msg = "Note updated successfully.";
            $note = BdmNote::where(['id' => $note_id, 'created_by' => $auth_user->id])->first();
            if (!$note) {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
                return redirect()->back();
            }
        } else {
            $msg = "Note added successfully.";
            $note = new BdmNote();
            $note->lead_id = $request->lead_id;
            $note->created_by = $auth_user->id;
        }

        $note->message = $request->note_message;
        $note->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }

    public function delete($note_id) {
        $auth_user = Auth::guard('bdm')->user();
        $note = BdmNote::where(['id' => $note_id, 'created_by' => $auth_user->id])->first();
        if (!$note) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }

        $note->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Note Deleted.']);
        return redirect()->back();
    }
}
