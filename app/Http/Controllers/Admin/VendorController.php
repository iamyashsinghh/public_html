<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\Vendor;
use App\Models\VendorCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\VendorLocalities;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function list()
    {
        $page_heading = "Vendors";
        $vendor_categories = VendorCategory::select('id', 'name')->get();
        $localities = VendorLocalities::all();
        $team_members = TeamMember::where('role_id', '7')->get();
        return view('admin.nonVenueCrm.vendor.list', compact('page_heading', 'vendor_categories', 'localities', 'team_members'));
    }

    public function ajax_list($vendor_cat_id)
    {
        if($vendor_cat_id == 0){
        $vendors = Vendor::select(
            'vendors.id',
            'vendors.profile_image',
            'vendors.name',
            'vendors.mobile',
            'vendors.email',
            'vendors.business_name',
            'vc.name as category_name',
            'vendors.status',
            'vendors.is_whatsapp_msg',
            'vendors.created_at',
            'vendors.group_name',
            DB::raw('(SELECT COUNT(*) FROM nv_lead_forwards WHERE nv_lead_forwards.forward_to = vendors.id AND (nv_lead_forwards.lead_datetime BETWEEN vendors.start_date AND COALESCE(vendors.end_date, NOW()) OR (vendors.start_date IS NULL AND vendors.end_date IS NULL))) as total_leads')
            )->leftJoin("vendor_categories as vc", 'vendors.category_id', '=', 'vc.id')
            ->orderBy('group_name', 'asc')
            ->get();
        }else{
            $vendors = Vendor::select(
                'vendors.id',
                'vendors.profile_image',
                'vendors.name',
                'vendors.mobile',
                'vendors.email',
                'vendors.business_name',
                'vc.name as category_name',
                'vendors.status',
                'vendors.is_whatsapp_msg',
                'vendors.created_at',
                'vendors.group_name',
                DB::raw('(SELECT COUNT(*) FROM nv_lead_forwards WHERE nv_lead_forwards.forward_to = vendors.id AND (nv_lead_forwards.lead_datetime BETWEEN vendors.start_date AND COALESCE(vendors.end_date, NOW()) OR (vendors.start_date IS NULL AND vendors.end_date IS NULL))) as total_leads')
                )->leftJoin("vendor_categories as vc", 'vendors.category_id', '=', 'vc.id')
                ->orderBy('group_name', 'asc')
                ->where('vendors.category_id', $vendor_cat_id)
                ->get();
        }
        return datatables($vendors)->editColumn('total_leads', function ($vendor) {
            return $vendor->total_leads ?: 'No leads found';
        })->toJson();
    }
    public function manage_ajax($vendor_id)
    {
        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
        return response()->json(['success' => true, 'alert_type' => 'success', 'vendor' => $vendor]);
    }

    public function manage_process($vendor_id = 0, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'vendor_name' => 'required|string|min:3|max:255',
            'mobile_number' => "required|digits:10",
            'email' => "required|email",
            'profile_image' => 'mimes:jpg,jpeg,png,webp|max:1024',
            'category' => 'required|int|exists:vendor_categories,id',
            'start_date' => 'required|date',
            'group_name' => 'nullable|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($vendor_id > 0) {
            $msg = "Vendor updated successfully.";
            $vendor = Vendor::find($vendor_id);
        }
        if ($vendor_id == 0) {
            $vendor = Vendor::where('mobile', $request->mobile_number)->withTrashed()->first();
            if ($vendor) {
                if ($vendor->deleted_at != null) {
                    $vendor->deleted_at = null;
                    $msg = "This user is already exist in our records, We just have updated it's profile info.";
                } else {
                    session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Mobile number is already exist.']);
                    return redirect()->back();
                }
            } else {
                $vendor = new Vendor();
                $msg = "Vendor added successfully.";
            }
        }

        if (is_file($request->profile_image)) {
            $file = $request->file('profile_image');
            $ext = $file->getClientOriginalExtension();

            $sub_str = substr($request->vendor_name, 0, 5);
            $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_profile" . date('dmyHis') . "." . $ext;
            $path = "vendorProfileImages/$file_name";
            Storage::put("public/" . $path, file_get_contents($file));
            $profile_image = asset("storage/" . $path);

            $vendor->profile_image = $profile_image;
        }

        $vendor->category_id = $request->category;
        $vendor->name = $request->vendor_name;
        $vendor->business_name = $request->business_name;
        $vendor->mobile = $request->mobile_number;
        $vendor->email = $request->email;
        $vendor->start_date = $request->start_date;
        $vendor->group_name = $request->group_name;
        $vendor->alt_mobile_number = $request->alt_mobile_number;
        $vendor->parent_id = $request->parent_id;
        $vendor->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }

    public function update_status($vendor_id, $status)
    {
        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return abort(404);
        }

        if($status == 1){
            $vendor->end_date = null;
        }else{
            $vendor->end_date = Carbon::now()->toDateString();
        }
        $vendor->status = $status;
        $vendor->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Status updated."]);
        return redirect()->back();
    }

    public function delete($vendor_id)
    {
        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return abort(404);
        }

        $vendor->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Vendor deleted."]);
        return redirect()->back();
    }

    public function view($vendor_id) {
        $vendor = Vendor::find($vendor_id);
        return view('admin.nonVenueCrm.vendor.view', compact('vendor'));
    }

    public function update_profile_image($vendor_id, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'profile_image' => 'mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            abort(404);
        }

        if (is_file($request->profile_image)) {
            $file = $request->file('profile_image');
            $ext = $file->getClientOriginalExtension();

            $sub_str = substr($vendor->name, 0, 5);
            $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_profile" . date('dmyHis') . "." . $ext;
            $path = "vendorProfileImages/$file_name";
            Storage::put("public/" . $path, file_get_contents($file));
            $profile_image = asset("storage/" . $path);
            $vendor->profile_image = $profile_image;
            $vendor->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Image updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong, please contact to administrator.']);
        }

        return redirect()->back();
    }
}
