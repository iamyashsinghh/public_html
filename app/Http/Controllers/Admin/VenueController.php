<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VenueController extends Controller
{
    public function list() {
        $page_heading = "Venues";
        $venues = Venue::all();
        return view('admin.venueCrm.venue.list', compact('venues', 'page_heading'));
    }

    public function manage_process(Request $request, $locality_id = 0) {
        $validate = Validator::make($request->all(), [
            'venue_name' => 'required|string|min:3,max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($locality_id > 0) {
            $msg = "Venue updated.";
            $venue = Venue::find($locality_id);
        } else {
            $msg = "Venue added.";
            $venue = new Venue();
        }
        $venue->name = $request->venue_name;
        $venue->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }
}
