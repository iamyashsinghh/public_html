<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WhatsappCampain;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;
use App\Models\WhatsappTemplates;
use App\Models\WhatsappMsgLogs;

use Illuminate\Support\Facades\Validator;

class WhatsappController extends Controller
{
    public function index(){
        $teamdata = TeamMember::whereIn('role_id', [3, 4, 6])->get();
        $templates = WhatsappTemplates::select('template_name')->get();
        return view("admin.whatsapp.campain.campain", compact('teamdata','templates'));
    }
    public function whatsappLogs_ajax(){
        $whatsapp_msg_logs = WhatsappMsgLogs::select(
            'whatsapp_msg_logs.id',
            'whatsapp_msg_logs.number',
            'whatsapp_msg_logs.campaign_name',
            'whatsapp_msg_logs.status',
            'whatsapp_msg_logs.created_at',
        )->get();
        return datatables($whatsapp_msg_logs)->toJson();
    }
    public function whatsappCampain_ajax(){
        $whatsapp_campains = WhatsappCampain::select(
            'whatsapp_campains.id',
            'whatsapp_campains.name',
            'whatsapp_campains.team_name',
            'whatsapp_campains.template_name',
            'whatsapp_campains.status',
            'whatsapp_campains.created_at',
        )->get();
        return datatables($whatsapp_campains)->toJson();
    }
    public function manageWhatsappCampainStatus($campaign_id, $status){
        $campaign = WhatsappCampain::find($campaign_id);
        if (!$campaign) {
            return abort(404);
        }
        $campaign->status = $status;
        $campaign->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Status updated."]);
        return redirect()->back();
    }

    public function delete($campaign_id)
    {
        $WhatsappCampain = WhatsappCampain::find($campaign_id);
        if (!$WhatsappCampain) {
            return abort(404);
        }
        $WhatsappCampain->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Campaign deleted."]);
        return redirect()->back();
    }

    public function manage_ajax($campaign_id)
    {
        $WhatsappCampain = WhatsappCampain::find($campaign_id);
        if (!$WhatsappCampain) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
        return response()->json(['success' => true, 'alert_type' => 'success', 'campaign' => $WhatsappCampain]);
    }

    public function manage_process($campaign_id = 0, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'template_name' => 'required',
            'team_member' => "required",
            'is_rm_name' => 'required',
            'name' => "required|string",
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        if ($campaign_id > 0) {
            $msg = "Campaign updated successfully.";
            $WhatsappCampain = WhatsappCampain::find($campaign_id);
        }
        if ($campaign_id == 0) {
                $WhatsappCampain = new WhatsappCampain();
                $msg = "Campaign added successfully.";
        }
        $teamMembername = TeamMember::select('name')->where('id', $request->team_member)->first();
        $WhatsappCampain->template_name = $request->template_name;
        $WhatsappCampain->assign_to = $request->team_member;
        $WhatsappCampain->name = $request->name;
        $WhatsappCampain->team_name = $teamMembername->name;
        $WhatsappCampain->is_rm_name =  $request->is_rm_name;
        $WhatsappCampain->status = 1;
        $WhatsappCampain->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }

    public function get_temo_img($temp_name){
        $template = WhatsappTemplates::select('img')->where('template_name', $temp_name)->first();
        return $template->img;
    }
    public function update_temp_image(Request $request) {
        $validate = Validator::make($request->all(), [
            'temp_image' => 'mimes:jpg,jpeg,png,webp|max:1024',
        ]);
        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        $template = WhatsappTemplates::where('template_name', $request->temp_name)->first();
        if (!$template) {
            abort(404);
        }
        if (is_file($request->temp_image)) {
            $file = $request->file('temp_image');
            $ext = $file->getClientOriginalExtension();
            $sub_str =  substr($template->template_name, 0, 5);
            $file_name = strtolower(str_replace(' ', '_', $sub_str)) . "_template" . date('dmyHis') . "." . $ext;
            $path = "whatsappTemplateImage/$file_name";
            Storage::put("public/" . $path, file_get_contents($file));
            $temp_image_url = asset("storage/" . $path);
            $template->img = $temp_image_url;
            $template->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Image updated.']);
        } else {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Someting went wrong, please contact to administrator.']);
        }
        return redirect()->back();
    }
}
