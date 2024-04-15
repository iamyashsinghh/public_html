<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorLocalities;
use Illuminate\Support\Facades\Validator;

class VendorLocalityController extends Controller
{
    public function list() {
        $page_heading = "Vendor Localities";
        $localities = VendorLocalities::all();
        return view('admin.nonVenueCrm.vendor.locality_list', compact('localities', 'page_heading'));
        }

    public function manage_process(Request $request, $locality_id = 0) {
        $validate = Validator::make($request->all(), [
            'locality_name' => 'required|string|min:3,max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($locality_id > 0) {
            $msg = "Category updated.";
            $category = VendorLocalities::find($locality_id);
        } else {
            $msg = "Category added.";
            $category = new VendorLocalities();
        }
        $category->name = $request->locality_name;
        $category->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }
}
