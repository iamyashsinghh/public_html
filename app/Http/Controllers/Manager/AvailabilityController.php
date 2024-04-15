<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller {
    public function list() {
        $auth_user = Auth::guard('manager')->user();
        $vm_members = TeamMember::select('id', 'name', 'venue_name')->where(['parent_id' => $auth_user->id, 'role_id' => 5])->orderBy('venue_name', 'asc')->get();

        $calendar = $this->getCalender();

        $grand_total_event = 0;
        $grand_total_pax = 0;
        foreach ($vm_members as $vm) {
            $arr = [];
            foreach ($calendar as $cal_date) {
                $model = Availability::where('created_by', $vm->id)->where('date', 'like', "%" . date('Y-m', strtotime($cal_date)) . "%");
                $arr[date('M-Y', strtotime($cal_date))]['events'] = $model->count();
                $arr[date('M-Y', strtotime($cal_date))]['pax'] = $model->sum('pax');
                $vm['total_event'] += $model->count();
                $vm['total_pax'] += $model->sum('pax');
            }
            $vm['availability'] = $arr;
            $grand_total_event += $vm['total_event'];
            $grand_total_pax += $vm['total_pax'];
        }
        return view('manager.venueCrm.availability.list', compact('vm_members', 'grand_total_event', 'grand_total_pax'));
    }
}
