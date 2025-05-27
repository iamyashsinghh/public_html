<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\partyArea;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartyAreaController extends Controller {
    public function manage_process(Request $request, $area_id = 0) {
        $is_area_name_validate = $area_id > 0 ? "string" : 'array';
        $validate = Validator::make($request->all(), [
            'member_id' => 'required|int|exists:team_members,id',
            'party_area_name' => "required|$is_area_name_validate",
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($area_id > 0) {
            $party_area = partyArea::where(['id' => $area_id, 'member_id' => $request->member_id])->first();
            if (!$party_area) {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            } else {
                $party_area->name = $request->party_area_name;
                $party_area->save();
                session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Party area updated."]);
            }
        } else {
            if (sizeof($request->party_area_name) > 0) {
                foreach ($request->party_area_name as $name) {
                    $party_area = new partyArea();
                    $party_area->member_id = $request->member_id;
                    $party_area->name = $name;
                    $party_area->save();
                }
                session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Party area added.']);
            } else {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            }
        }
        return redirect()->back();
    }

    public function delete($area_id) {
        $party_area = partyArea::find($area_id);

        if (!$party_area) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Party area not found.']);
            return redirect()->back();
        }

        // Delete all related availabilities first
        Availability::where('party_area_id', $area_id)->delete();

        // Now delete the party area
        $party_area->delete();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Party area deleted.']);
        return redirect()->back();
    }
}
