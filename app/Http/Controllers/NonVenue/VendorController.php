<?php

namespace App\Http\Controllers\NonVenue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorCategory;
use Illuminate\Support\Facades\DB;


class VendorController extends Controller
{
    public function list($vendor_id)
    {
        $vendor_category_id = $vendor_id;
        $page_heading = VendorCategory::select('name')->where('id', $vendor_id)->first()->name;
        return view('nonvenue.vendors.list', compact('page_heading', 'vendor_category_id'));
    }
    public function ajax_list($vendor_id)
    {
        $vendors = Vendor::select(
            'vendors.id',
            'vendors.profile_image',
            'vendors.name',
            'vendors.mobile',
            'vendors.email',
            'vendors.business_name',
            'vc.name as category_name',
            'vendors.status',
            'vendors.created_at',
            'vendors.is_whatsapp_msg',
            'vendors.group_name',
            DB::raw('(SELECT COUNT(*) FROM nv_lead_forwards WHERE nv_lead_forwards.forward_to = vendors.id AND (nv_lead_forwards.created_at BETWEEN vendors.start_date AND COALESCE(vendors.end_date, NOW()) OR (vendors.start_date IS NULL AND vendors.end_date IS NULL))) as total_leads')
            )->leftJoin("vendor_categories as vc", 'vendors.category_id', '=', 'vc.id')
            ->orderBy('group_name', 'asc')
            ->where('vendors.category_id', $vendor_id)
            ->where('vendors.status', 1)
            ->get();

        return datatables($vendors)->editColumn('total_leads', function ($vendor) {
            return $vendor->total_leads ?: 'No leads found';
        })->toJson();
    }
    public function vedor_leads($id)
    {
        $vendor = Vendor::select(
            'nv_lead_forwards.lead_id',
            'nv_lead_forwards.name',
            'nv_lead_forwards.mobile',
            'nv_lead_forwards.event_datetime',
            'nv_lead_forwards.created_at',
            'vendors.start_date',
            'vendors.end_date'
        )
        ->where('vendors.id', $id)
        ->join('nv_lead_forwards', 'nv_lead_forwards.forward_to', '=', 'vendors.id')
        ->whereRaw('nv_lead_forwards.created_at BETWEEN vendors.start_date AND COALESCE(vendors.end_date, NOW())')
        ->get();
        return response()->json($vendor);
        }
}
