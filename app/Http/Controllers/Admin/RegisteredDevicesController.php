<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\TeamMember;
use App\Models\Vendor;

class RegisteredDevicesController extends Controller
{
    public function permit_or_not_more_device_for_login_for_an_account($venue_or_vendor, $member_id, $value)
    {
        try{
            if($value == 1){
                $msg = 'Permission given for add 1 more device';
            }else{
                $msg = 'Permission removed for adding device';
            }

            if ($venue_or_vendor === 'team') {
                $member = TeamMember::where(['id' => $member_id])->first();
            } else {
                $member = Vendor::where(['id' => $member_id])->first();
            }

            $member->can_add_device = $value;
            $member->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        }catch(\Throwable $th){
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong. Internal server error."]);
        }
        return redirect()->back();
    }

    public function delete_device($device_id)
    {
        try {
            $device = Device::find($device_id);
            $device->delete();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Device deleted."]);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong. Internal server error."]);
        }
        return redirect()->back();
    }
}
