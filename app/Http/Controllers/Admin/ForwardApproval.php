<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadForward;
use App\Models\LeadForwardApproval;
use App\Models\LeadForwardInfo;
use App\Models\VmEvent;
use Illuminate\Http\Request;

class ForwardApproval extends Controller
{
    private $current_timestamp;
    public function __construct()
    {
        $this->current_timestamp = date('Y-m-d H:i:s');
    }
    public function list(){
        $page_heading = "Lead Forward Approval";
        $LeadForwardApproval = LeadForwardApproval::where('is_approved', '2')->get();
        return view('admin.venueCrm.forwardApproval.list', compact('LeadForwardApproval', 'page_heading'));
    }

    public function manage_process($id, $status) {
        $values = LeadForwardApproval::where('id', $id)->first();
        if (!$values) {
            return redirect()->back()->with('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Lead Forward Approval not found.']);
        }

        if ($status == 0) {
            $values->is_approved = $status;
            $values->save();
            $msg = 'Request Rejected';
        } else {
            $msg = 'Request Approved';
            $lead = Lead::where('lead_id', $values->lead_id)->first();
            if (!$lead) {
                return redirect()->back()->with('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Lead not found.']);
            }

            $auth_user = $values->forward_by;
            $vm_id = $values->forward_to;
            $old_vm = $values->forward_from;

            return $vm_id;

                $exist_lead_forward = LeadForward::where(['lead_id' => $lead->lead_id, 'forward_to' => $old_vm])->first();
                $exist_lead_forward->forward_to = $vm_id;
                $exist_lead_forward->is_manager_forwarded = '1';
                $exist_lead_forward->manager_forwarded_by = $auth_user;
                $exist_lead_forward->save();

            $values->is_approved = $status;
            $values->save();

            $lead_forward_info = LeadForwardInfo::where(['lead_id' => $lead->lead_id, 'forward_from' => $auth_user, 'forward_to' => $vm_id])->first();
                    if (!$lead_forward_info) {
                        $lead_forward_info = new LeadForwardInfo();
                        $lead_forward_info->lead_id = $lead->lead_id;
                        $lead_forward_info->forward_from = $auth_user;
                        $lead_forward_info->forward_to = $vm_id;
                    }
                    $lead_forward_info->updated_at = $this->current_timestamp;
                    $lead_forward_info->save();
        }
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "$msg."]);
        return redirect()->back();
    }

}
