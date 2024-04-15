<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;

use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller {
    public function manage() {
        $calendar  =  $this->getCalender();
        return view('team.venueCrm.availability.manage', compact('calendar'));
    }

    public function manage_process(Request $request) {
        $is_pax_validate = $request->checked == true ? "required|int|min:1|max_digits:6" : "";
        $validate = Validator::make($request->all(), [
            'date' => 'required|date',
            'party_area_id' => 'required|exists:party_areas,id',
            'food_type' => 'required|string|max:6',
            'pax' => $is_pax_validate,
            'checked' => 'required|boolean'
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
        }

        try {
            $auth_user = Auth::guard('team')->user();
            if ($request->checked == true) {
                $model = new Availability();
                $model->created_by = $auth_user->id;
                $model->party_area_id = $request->party_area_id;
                $model->food_type = $request->food_type;
                $model->pax = $request->pax;
                $model->date = $request->date;
                $model->save();
            } else {
                Availability::where(['created_by' => $auth_user->id, 'party_area_id' => $request->party_area_id, 'food_type' => $request->food_type, 'date' => $request->date])->delete();
            }
            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Changes updated successfully.', 'checked' => $request->checked]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong.']);
        }
    }

    public function reset_calendar($datetime) {
        $auth_user  = Auth::guard('team')->user();
        try {
            Availability::where('created_by', $auth_user->id)->where('date', 'like', "%" . date('Y-m', strtotime($datetime)) . "%")->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Calendar reset successfully."]);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong.']);
        }
        return redirect()->back();
    }
}
