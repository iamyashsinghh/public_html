<?php

namespace App\Http\Controllers\NonVenue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\nvNote;

class NvNotesController extends Controller
{
    public function list(Request $request, $dashboard_filters = null)
    {
        $filter_params = "";
        $page_heading = $filter_params ? "Vendors Help - Filtered" : "Vendors Help";
        if ($dashboard_filters !== null) {
            $filter_params = ['dashboard_filters' => $dashboard_filters];
            $page_heading = ucwords(str_replace("_", " ", $dashboard_filters));
        }

        return view('nonvenue.rmhelpsupport.list', compact('page_heading', 'filter_params'));
    }

    public function ajax_list(Request $request){
        $auth_user = Auth::guard('nonvenue')->user();
        $vendor_help = nvNote::leftJoin('nvrm_lead_forwards', 'nvrm_lead_forwards.lead_id', '=', 'nv_notes.lead_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'nv_notes.created_by')
            ->leftJoin('vendor_categories', 'vendor_categories.id', '=', 'vendors.category_id')
            ->leftJoin('team_members', 'team_members.id', '=', 'nv_notes.done_by')
            ->select(
                'nv_notes.*',
                'nvrm_lead_forwards.lead_id',
                'nvrm_lead_forwards.lead_status',
                'vendors.name as created_by_name',
                'vendor_categories.name as category_name',
                'team_members.name as done_by_name'
            )->where('nv_notes.id', '>', 592);
        if (!empty($request->dashboard_filters)) {
            if ($request->dashboard_filters == "vendor_non_responsed_help") {
                $vendor_help->whereNull('nv_notes.done_by');
            } else if ($request->dashboard_filters == "vendor_total_helped") {
                $vendor_help->where('nv_notes.done_by',$auth_user->id);
            }
        }
        return datatables($vendor_help->get())->toJson();
    }


    public function vendor_update_help(Request $request, $vendor_help_id)
    {
        $validate = Validator::make($request->all(), [
            'vendor_help_reponse_message' => 'required|string',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $auth_user = Auth::guard('nonvenue')->user();
        $vendor_help = nvNote::where(['id' => $vendor_help_id])->first();
        if (!$vendor_help) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong, please try again later.']);
            return redirect()->back();
        }
        $vendor_help->update([
            'done_by' => $auth_user->id,
            'nvrm_msg' => $request->vendor_help_reponse_message,
            'status' => '1', // '0' => 'Pending', '1' => 'Done'
            'is_solved' => null, // null => 'solved' /, '0' => 'unsolved'
            'done_datetime' => date('Y-m-d H:i:s'),
        ]);

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Sucessfully Responsed.']);
        return redirect()->back();
    }
}
