<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\VmProductivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VmProductivityController extends Controller {
    public function manage_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'vm_id' => 'required|exists:team_members,id',
            'wb_recce_target' => 'required|int'
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
        }
        $current_mohth = date('Y-m');
        $vm_productivity = VmProductivity::where('team_id', $request->vm_id)->where('date', 'like', "%$current_mohth%")->first();
        if (!$vm_productivity) {
            $vm_productivity = new VmProductivity();
            $vm_productivity->team_id = $request->vm_id;
        }
        $vm_productivity->wb_recce_target = $request->wb_recce_target;
        $vm_productivity->date = date('Y-m-d');;
        $vm_productivity->save();

        return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Recce target updated!']);
    }
}
