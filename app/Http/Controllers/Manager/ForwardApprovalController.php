<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeadForwardApproval;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ForwardApprovalController extends Controller
{
    public function list(){
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $page_heading = "Lead Forward Approval";
        $auth_user = Auth::guard('manager')->user();
        $approvalsThisMonth = LeadForwardApproval::where('forward_by', $auth_user->id)
                                                 ->whereMonth('created_at', $currentMonth)
                                                 ->whereYear('created_at', $currentYear)
                                                 ->get();

        return view('manager.venueCrm.forwardApproval.list', compact('approvalsThisMonth', 'page_heading'));
    }
}
