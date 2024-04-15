<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorCategoryController extends Controller {
    public function list() {
        $page_heading = "Vendor Categories";
        $categories = VendorCategory::all();
        return view('admin.nonVenueCrm.vendor.category_list', compact('categories', 'page_heading'));
    }

    public function manage_process($category_id = 0, Request $request) {
        $validate = Validator::make($request->all(), [
            'category_name' => 'required|string|min:3,max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($category_id > 0) {
            $msg = "Category updated.";
            $category = VendorCategory::find($category_id);
        } else {
            $msg = "Category added.";
            $category = new VendorCategory();
        }
        $category->name = $request->category_name;
        $category->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }
}
