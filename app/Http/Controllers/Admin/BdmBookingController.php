<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BdmBooking;
use App\Models\BdmLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BdmBookingController extends Controller
{
    public function add_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'lead_id' => 'required|exists:bdm_leads,lead_id',
            'booking_date' => 'required|date',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $auth_user = Auth::guard('admin')->user();

        $bookingData = new BdmBooking();
        $bookingData->lead_id = $request->lead_id;
        $bookingData->created_by = $auth_user->id;
        $bookingData->booking_date = date('Y-m-d H:i:s', strtotime($request->booking_date));
        $bookingData->package_name = $request->package_name;
        $bookingData->price = $request->price;
        $bookingData->payment_method = $request->payment_method;
        $bookingData->save();

        session()->flash('status', ['sueccess' => true, 'alert_type' => 'success', 'message' => 'Booking Done Successfully.']);
        return redirect()->back();
    }

    public function edit_process(Request $request , $booking_id) {
        $auth_user = Auth::guard('admin')->user();
        $bookingData = BdmBooking::where(['id'=> $booking_id])->first();
        if(!$bookingData){
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Booking does not exist.']);
        }
        $bookingData->booking_date = date('Y-m-d H:i:s', strtotime($request->booking_date));
        $bookingData->package_name = $request->package_name;
        $bookingData->price = $request->price;
        $bookingData->payment_method = $request->payment_method;
        $bookingData->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Booking Updated.']);
        return redirect()->back();
    }

    public function delete($booking_id){
        $bookingData = BdmBooking::where(['id'=> $booking_id])->first();
        $bookingData->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Booking Deleted.']);
        return redirect()->back();
    }

    public function get_booking($booking_id){
        $booking = BdmBooking::where('id', $booking_id)->first();
        return $booking;
    }

    public function update_payment_image(Request $request) {
        $validate = Validator::make($request->all(), [
            'image' => 'mimes:jpg,jpeg,png,webp|max:1024',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $booking = BdmBooking::where('id', $request->id)->first();
        if (!$booking) {
            abort(404);
        }
        if (is_file($request->image)) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $sub_str =  substr($booking->booking_date, 0, 5);
            $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_payment_img" . date('dmyHis') . "." . $ext;
            $path = "bdmPaymentProofImg/$file_name";
            Storage::put("public/" . $path, file_get_contents($file));
            $image_url = asset("storage/" . $path);
            $booking->payment_proof = $image_url;
            $booking->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Image updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong, please contact to administrator.']);
        }
        return redirect()->back();
    }

    public function manage_agreement_image(int $bdm_booking_id)
    {
        $data = BdmBooking::select('id', 'lead_id', 'order_agreement_farm_image')->where('id', $bdm_booking_id)->first();
        $bdm_lead = BdmLead::where('lead_id', $data->lead_id)->first();
        $view_used_for = "bdm";
        $page_heading = "Order & Agreement Farm Image";
        return view('admin.bdmCrm.common.manage_images', compact('data', 'view_used_for', 'page_heading', 'bdm_lead'));
    }

    public function manage_agreement_image_process(Request $request, int $bdm_booking_id)
    {
        try {
            $bdmBooking = BdmBooking::find($bdm_booking_id);
            $bdmBookingImagesArray = $bdmBooking->order_agreement_farm_image ? explode(",", $bdmBooking->order_agreement_farm_image) : [];
            if (is_array($request->gallery_images)) {
                foreach ($request->gallery_images as $key => $image) {
                    if (is_file($image)) {
                        $ext = $image->getClientOriginalExtension();
                        $sub_str = substr($bdmBooking->name, 0, 5);
                        $file_name = "agrement_" . strtolower(str_replace(' ', '_', $sub_str)) . "_" . time() + $key . "." . $ext;
                        $path = "uploads/bdmAgreementImg/$file_name";
                        Storage::put("public/" . $path, file_get_contents($image));
                        array_push($bdmBookingImagesArray, $file_name);
                    }
                }
            }
            $bdmBooking->order_agreement_farm_image = implode(",", $bdmBookingImagesArray);
            $bdmBooking->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Images uploaded successfully.']);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return $th->getMessage();
        }
        return redirect()->back();
    }
    public function agreement_image_delete(Request $request, $booking_id)
{
    try {
        $booking = BdmBooking::where('id' , $booking_id)->first();
        if (!$booking) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Booking not found.']);
        }
        $images_arr = explode(",", $booking->order_agreement_farm_image);
        $request_image_index = array_search($request->image_name, $images_arr);
        if ($request_image_index !== false) {
            unset($images_arr[$request_image_index]);
            $images_arr = array_values($images_arr);
        }
        $booking->order_agreement_farm_image = implode(",", $images_arr);
        $booking->save();

        if (Storage::exists("public/uploads/{$request->image_name}")) {
            Storage::delete("public/uploads/{$request->image_name}");
        }

        return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Image removed successfully.']);
    } catch (\Throwable $th) {
        return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'errors' => $th->getMessage()]);
    }
}
}
