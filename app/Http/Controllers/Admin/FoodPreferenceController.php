<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\foodPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoodPreferenceController extends Controller {
    public function manage_process(Request $request, $id = 0) {
        $food_preference_name = $id > 0 ? "string" : 'array';
        $validate = Validator::make($request->all(), [
            'member_id' => 'required|int|exists:team_members,id',
            'food_preference_name' => "required|$food_preference_name",
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($id > 0) {
            $food_preference = foodPreference::where(['id' => $id, 'member_id' => $request->member_id])->first();
            if (!$food_preference) {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            } else {
                $food_preference->name = $request->food_preference_name;
                $food_preference->save();
                session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Food preference updated."]);
            }
        } else {
            if (sizeof($request->food_preference_name) > 0) {
                foreach ($request->food_preference_name as $name) {
                    $food_preference = new foodPreference();
                    $food_preference->member_id = $request->member_id;
                    $food_preference->name = $name;
                    $food_preference->save();
                }
                session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Food preference added.']);
            } else {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            }
        }
        return redirect()->back();
    }

    public function delete($id) {
        $party_area = foodPreference::find($id);
        $party_area->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Food preference deleted.']);
        return redirect()->back();
    }
}
