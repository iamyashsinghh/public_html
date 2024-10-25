<?php

use App\Models\BdmLead;
use App\Models\CrmMeta;
use App\Models\Lead;
use App\Models\LoginInfo;
use App\Models\nvLead;
use App\Models\nvrmLeadForward;
use App\Models\whatsappMessages;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Vendor;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

if (!function_exists('getAssigningRm')) {
    function getAssigningRm()
    {
        DB::beginTransaction();
        try {
            $firstRmWithIsNext = TeamMember::where(['role_id' => 4, 'status' => 1, 'is_next' => 1, 'is_active' => 1])
                ->orderBy('id', 'asc')
                ->first();
            if ($firstRmWithIsNext) {
                TeamMember::where(['role_id' => 4, 'status' => 1])
                    ->where('id', '!=', $firstRmWithIsNext->id)
                    ->update(['is_next' => 0]);
            }
            if (!$firstRmWithIsNext) {
                $firstRmWithIsNext = TeamMember::where(['role_id' => 4, 'status' => 1, 'is_active' => 1])
                    ->orderBy('id', 'asc')
                    ->first();
                if (!$firstRmWithIsNext) {
                    throw new Exception('No RM available');
                }
                $firstRmWithIsNext->is_next = 1;
                $firstRmWithIsNext->save();
                DB::commit();
                return $firstRmWithIsNext;
            } else {
                $firstRmWithIsNext->is_next = 0;
                $firstRmWithIsNext->save();
                $nextRm = TeamMember::where(['role_id' => 4, 'status' => 1, 'is_active' => 1])
                    ->where('id', '>', $firstRmWithIsNext->id)
                    ->orderBy('id', 'asc')
                    ->first();

                if (!$nextRm) {
                    $nextRm = TeamMember::where(['role_id' => 4, 'status' => 1, 'is_active' => 1])
                        ->orderBy('id', 'asc')
                        ->first();
                }
                $nextRm->is_next = 1;
                $nextRm->save();
            }
            DB::commit();
            return $firstRmWithIsNext;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

if (!function_exists('getRmName')) {
    function getRmName($name)
    {
        $rm_name = TeamMember::where(['role_id' => 4, 'status' => 1, 'name' => $name])->first();
        return $rm_name;
    }
}

if (!function_exists('getAssigningBdm')) {
    function getAssigningBdm()
    {
        DB::beginTransaction();
        try {
            $firstRmWithIsNext = TeamMember::where(['role_id' => 6, 'status' => 1, 'is_next' => 1])
                ->orderBy('id', 'asc')
                ->first();
            if ($firstRmWithIsNext) {
                TeamMember::where(['role_id' => 6, 'status' => 1])
                    ->where('id', '!=', $firstRmWithIsNext->id)
                    ->update(['is_next' => 0]);
            }
            if (!$firstRmWithIsNext) {
                $firstRmWithIsNext = TeamMember::where(['role_id' => 6, 'status' => 1])
                    ->orderBy('id', 'asc')
                    ->first();
                if (!$firstRmWithIsNext) {
                    throw new Exception('No RM available');
                }
                $firstRmWithIsNext->is_next = 1;
                $firstRmWithIsNext->save();
                DB::commit();
                return $firstRmWithIsNext;
            } else {
                $firstRmWithIsNext->is_next = 0;
                $firstRmWithIsNext->save();
                $nextRm = TeamMember::where(['role_id' => 6, 'status' => 1])
                    ->where('id', '>', $firstRmWithIsNext->id)
                    ->orderBy('id', 'asc')
                    ->first();
                if (!$nextRm) {
                    $nextRm = TeamMember::where(['role_id' => 6, 'status' => 1])
                        ->orderBy('id', 'asc')
                        ->first();
                }
                $nextRm->is_next = 1;
                $nextRm->save();
            }
            DB::commit();
            return $firstRmWithIsNext;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
if (!function_exists('send_wa_normal_text_msg')) {
    function send_wa_normal_text_msg($number, $msg)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');
        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => "91$number",
            "type" => "text",
            "text" => [
                "body" => $msg,
            ]
        ]);
        if ($response->successful()) {
            $current_timestamp = date('Y-m-d H:i:s');
            $newWaMsgSave = new whatsappMessages();
            $newWaMsgSave->msg_id = "$number";
            $newWaMsgSave->msg_from = "$number";
            $newWaMsgSave->time = $current_timestamp;
            $newWaMsgSave->type = 'text';
            $newWaMsgSave->is_sent = "1";
            $newWaMsgSave->body = $msg;
            $newWaMsgSave->save();
        }
    }
}

if (!function_exists('assignLeadsToRMs')) {
    function assignLeadsToRMs()
    {
        set_time_limit(300);
        $leads = Lead::select('lead_id')->whereNull('deleted_at')->get();
        DB::beginTransaction();
        try {
            foreach ($leads as $lead) {
                $get_rm = getAssigningRm();
                if (!$get_rm) {
                    throw new Exception('No RM available for assignment.');
                }
                Lead::where('lead_id', $lead->lead_id)->update([
                    'assign_to' => $get_rm->name,
                    'assign_id' => $get_rm->id,
                ]);
            }
            DB::commit();
            return "Successfully assigned leads to RMs.";
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to assign leads to RMs: ' . $e->getMessage());
            return "Error during lead assignment: " . $e->getMessage();
        }
    }
}

Route::get ('/getlol', function () {
    // $oneYearAgo = Carbon::now()->subYear();
    // $oneYearAgo0 = Carbon::now()->subMonth();

    // $uniqueLeadsCount = Lead::join('events', 'leads.lead_id', '=', 'events.lead_id')
    //     ->where('leads.lead_datetime', '<', $oneYearAgo)
    //     ->where('events.event_datetime', '<', $oneYearAgo0)
    //     ->groupBy('leads.lead_id')
    //     ->get();

    // return $uniqueLeadsCount;
});

Route::post('/save_wa', function (Request $request) {
    $name = $request['contacts'][0]['profile']['name'];
    $number = $request['messages']['from'];
    $timestamp = $request['messages']['timestamp'];
    $type = $request['messages']['type'];
    $textMsg = '';
    $id = "";
    $current_timestamp = date('Y-m-d H:i:s', $timestamp);
    if (strlen($number) > 10) {
        if (substr($number, 0, 2) == '91') {
            $number = substr($number, 2);
        }
    }
    $newWaMsg = new whatsappMessages();
    $newWaMsg->msg_id = $request['id'];
    $newWaMsg->msg_from = $number;
    $newWaMsg->time = $current_timestamp;
    $newWaMsg->type = $type;
    $newWaMsg->is_sent = "0";
    if ($type == 'text') {
        $textMsg = $request['messages'][$type]['body'];
        $newWaMsg->body = $textMsg;
        $newWaMsg->save();
    } elseif ($type == 'document') {
        $id = $request['messages'][$type]['id'];
        $newWaMsg->doc = $id;
    } elseif ($type == "audio") {
        $id = $request['messages'][$type]['id'];
        $newWaMsg->doc = $id;
    } elseif ($type == "video") {
        $id = $request['messages'][$type]['id'];
        $newWaMsg->doc = $id;
    } elseif ($type == "image") {
        $id = $request['messages'][$type]['id'];
        $newWaMsg->doc = $id;
    } elseif ($type == "button") {
        $textMsg = $request['messages'][$type]['text'];
        $newWaMsg->body = $textMsg;
        if (strtolower(trim($textMsg)) === "yes log me in") {
            $user = TeamMember::where('mobile', $number)->first();
            if($user){
                $login_info = LoginInfo::where([
                    'login_type' => 'team',
                    'user_id' => $user->id,
                ])->first();
                if (!$login_info || $login_info == null) {
                    $msg = "Error: Invalid Login.";
                    send_wa_normal_text_msg($number, $msg);
                    $newWaMsg->save();
                    return true;
                }
                $request_otp_at = date('YmdHis', strtotime($login_info->request_otp_at));
                $ten_minutes_ago = date('YmdHis', strtotime('-10 minutes'));
                if ($request_otp_at < $ten_minutes_ago) {
                    if ($login_info !== null) {
                        $login_info->otp_code = null;
                        $login_info->save();
                    }
                    $msg = "Error: Timeout! Please try again.";
                    send_wa_normal_text_msg($number, $msg);
                    $newWaMsg->save();
                    return true;
                }else{
                    $msg = "*Hey $user->name*,\nSuccess: Now you will automatically logged in.";
                    $login_info->login_for_whatsapp_otp = $login_info->otp_code;
                    $login_info->save();
                    send_wa_normal_text_msg($number, $msg);
                    $newWaMsg->save();
                    return true;
                }
            }
        }
        if(strtolower(trim($textMsg)) === "i would like to login"){
            $user = Vendor::where('mobile', $number)->first();
            if($user){
                $login_info = LoginInfo::where([
                    'login_type' => 'vendor',
                    'user_id' => $user->id,
                ])->first();
                if (!$login_info || $login_info == null) {
                    $msg = "Error: Invalid Login.";
                    send_wa_normal_text_msg($number, $msg);
                    $newWaMsg->save();
                    return true;
                }
                $request_otp_at = date('YmdHis', strtotime($login_info->request_otp_at));
                $ten_minutes_ago = date('YmdHis', strtotime('-10 minutes'));
                if ($request_otp_at < $ten_minutes_ago) {
                    if ($login_info !== null) {
                        $login_info->otp_code = null;
                        $login_info->save();
                    }
                    $msg = "Error: Timeout! Please try again.";
                    send_wa_normal_text_msg($number, $msg);
                    $newWaMsg->save();
                    return true;
                }else{
                    $msg = "*Hey $user->name*,\nSuccess: Now you will automatically logged in.";
                    $login_info->login_for_whatsapp_otp = $login_info->otp_code;
                    $login_info->save();
                    send_wa_normal_text_msg($number, $msg);
                    $newWaMsg->save();
                    return true;
                }
            }
        }
    } elseif ($type == "contacts") {
        $contact_name = $request['messages'][$type][0]['name']['formatted_name'];
        $contact_number = $request['messages'][$type][0]['phones'][0]['phone'];
        $data = json_encode([
            'name' => $contact_name,
            'mobile' => $contact_number,
        ]);
        $newWaMsg->doc = $data;
    } elseif ($type == "location") {
        $latitude = $request['messages'][$type]['latitude'];
        $longitude = $request['messages'][$type]['longitude'];
        $locationurl = "https://www.google.com/maps/@$latitude,$longitude,12z";
        $newWaMsg->doc = $locationurl;
    } else {
    }
    $newmsg_saved = true;
    if ($newmsg_saved) {
        $lastMessage = WhatsappMessages::where('msg_from', $number)->latest()->first();
        $newWaMsg->save();
        $sentAutoMsg = 0;
        if ($lastMessage) {
            $now = Carbon::now();
            $created_at = Carbon::parse($lastMessage->created_at);
            if ($now->diffInHours($created_at) > 24) {
                $sentAutoMsg = 1;
            }
        } else {
            $sentAutoMsg = 1;
        }
        if ($sentAutoMsg === 1) {
            if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
                return false;
            }
            $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
            $authKey = env('TATA_AUTH_KEY');
            $response = Http::withHeaders([
                'Authorization' => "Bearer $authKey",
                'Content-Type' => 'application/json'
            ])->post($url, [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => "91$number",
                "type" => "text",
                "text" => [
                    "body" => "Thanks for reaching out to us. We are glad to have you with us and committed to delivering a superior customer experience. \n \n *Have a great day!*"
                ]
            ]);
            if ($response->successful()) {
                $current_timestamp = date('Y-m-d H:i:s');
                $newWaMsgSave = new whatsappMessages();
                $newWaMsgSave->msg_id = "$number";
                $newWaMsgSave->msg_from = "$number";
                $newWaMsgSave->time = $current_timestamp;
                $newWaMsgSave->type = 'text';
                $newWaMsgSave->is_sent = "1";
                $newWaMsgSave->body = "Thanks for reaching out to us. We are glad to have you with us and committed to delivering a superior customer experience. \n \n *Have a great day!*";
                $newWaMsgSave->save();
            }
        }
    }

    $getlead = Lead::where('mobile', $number)->first();
    $getlead2 = nvrmLeadForward::where('mobile', $number)->first();
    $getlead3 = nvLead::where('mobile', $number)->first();
    $getlead4 = Vendor::where('mobile', $number)->first();
    $getlead5 = Vendor::where('alt_mobile_number', $number)->first();
    $getlead6 = BdmLead::where('mobile', $number)->first();


    if ($getlead) {
        $getlead->is_whatsapp_msg = 1;
        $getlead->whatsapp_msg_time = $current_timestamp;
        $getlead->save();
    }
    if ($getlead2) {
        $getlead2->is_whatsapp_msg = 1;
        $getlead2->whatsapp_msg_time = $current_timestamp;
        $getlead2->save();
    }
    if ($getlead3) {
        $getlead3->is_whatsapp_msg = 1;
        $getlead3->save();
    }
    if ($getlead4) {
        $getlead4->is_whatsapp_msg = 1;
        $getlead4->whatsapp_msg_time = $current_timestamp;
        $getlead4->save();
    }
    if ($getlead5) {
        $getlead5->is_whatsapp_msg = 1;
        $getlead5->whatsapp_msg_time = $current_timestamp;
        $getlead5->save();
    }
    if ($getlead6) {
        $getlead6->is_whatsapp_msg = 1;
        $getlead6->whatsapp_msg_time = $current_timestamp;
        $getlead6->save();
    }
    if (!$getlead && !$getlead2 && !$getlead3 && !$getlead4 && !$getlead5 && !$getlead6) {
        $current_timestamp = date('Y-m-d H:i:s');
        $lead = new Lead();
        $lead->name = $name;
        $lead->mobile = $number;
        $lead->lead_datetime = $current_timestamp;
        $lead->source = "WB|WhatsApp";
        $lead->preference = 'whatsapp';
        $lead->locality = null;
        $lead->lead_status = "Super Hot Lead";
        $lead->read_status = false;
        $lead->service_status = false;
        $lead->done_title = null;
        $lead->done_message = null;
        $lead->lead_color = "#4bff0033";
        $lead->virtual_number = null;
        $lead->is_whatsapp_msg = 1;
        $lead->whatsapp_msg_time = $current_timestamp;
        $get_rm = getAssigningRm();
        $lead->assign_to = $get_rm->name;
        $lead->assign_id = $get_rm->id;
        $lead->save();
    }

    if ($newmsg_saved) {
        return response()->json([
            'success' => true,
            'message' => 'Message saved successfully',
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save the message',
        ], 500);
    }
});

Route::get('/manupulate_csrf', function () {
    $randomValue = Str::random(10);
    DB::connection('mysql')->table('randomnes')->updateOrInsert(
        ['id' => 1],
        ['random_pass' => $randomValue]
    );
});

Route::get('/csrf-token', function () {
    $randompassvalue = DB::connection('mysql')->table('randomnes')->where('id', 1)->value('random_pass');
    $hashvalue = md5(sha1(md5($randompassvalue)));
    $randomnum = rand(74365874, 74365874);
    $randomnum2 = rand(1, 19);
    $maincode = "$randomnum2-546674$hashvalue@$randomnum";
    $out = base64_encode(base64_encode($maincode));
    return json_encode(['csrfToken' => $out]);
});

if (!function_exists('simpleDecrypt')) {

    function simpleDecrypt($encoded)
    {
        $encoded = base64_decode($encoded);
        $decoded = "";
        for ($i = 0; $i < strlen($encoded); $i++) {
            $b = ord($encoded[$i]);
            $a = $b ^ 10;
            $decoded .= chr($a);
        }
        return base64_decode(base64_decode($decoded));
    }
}

if (!function_exists('notify_users_about_lead_interakt_async')) {
    function notify_users_about_lead_interakt_async($mobile, $name)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $client = new Client();

        if (empty($name)) {
            $name = "Sir/Mam";
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $token = env("TATA_AUTH_KEY");
        $authToken = "Bearer $token";
        $response = Http::withHeaders([
            'Authorization' => $authToken,
            'Content-Type' => 'application/json',
        ])->post($url, [
            "to" => "91{$mobile}",
            "type" => "template",
            "template" => [
                "name" => "notify_users_about_lead",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$name",
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        return $response;
    }
}

if (!function_exists('get_business_cat')) {
    function get_business_cat($value)
    {
        $data = collect([
            ['data' => 'best-wedding-photographers', 'value' => 1],
            ['data' => 'top-makeup-artists', 'value' => 2],
            ['data' => 'best-mehndi-artists', 'value' => 3],
            ['data' => 'band-baja-ghodiwala', 'value' => 5],
            ['data' => 'best-choreographers', 'value' => 6],
            ['data' => 'best-decorators', 'value' => 7],
            ['data' => 'bridal-wear', 'value' => 8],
            ['data' => 'groom-wear', 'value' => 9],
            ['data' => 'wedding-transportation-and-vintage-cars', 'value' => 10],
            ['data' => 'invitation-cards', 'value' => 11]
        ]);
        $matchedItem = $data->firstWhere('data', $value);
        return $matchedItem ? $matchedItem['value'] : null;
    }
}

Route::post('/leads_get_tata_ive_call_from_post_method_hidden_url', function (Request $request) {
    try {
        $mobile = $request->input('caller_id_number');
        $pattern = "/^\d{10}$/";

        $caller_agent_name = null;
        $lead_cat_data = 'Venue';
        $get_rm = null;
        $recording_url = $request->input('recording_url');

        if ($request->input('answered_agent') !== null) {
            $caller_agent_name = $request->input('answered_agent.name');
        } elseif ($request->input('missed_agent') !== null) {
            $missed_agents = $request->input('missed_agent');
            if (!empty($missed_agents)) {
                $caller_agent_name = $missed_agents[0]['name'];
            }
        }
        if (!$caller_agent_name) {
            $get_rm = getAssigningRm();
        } else {
            $get_rm = getRmName($caller_agent_name);
        }

        if (!preg_match($pattern, $mobile)) {
            return response()->json(['status' => false, 'msg' => "Invalid mobile number."]);
        }
        $current_timestamp = now();
        $call_to_wb_api_virtual_number = $request->input('call_to_number');
        $lead_source = "WB|Call";

        $crm_meta = CrmMeta::find(1);
        $preference = $crm_meta ? $crm_meta->meta_value : 'la-fortuna-banquets-mayapuri';
        $id_ad = $crm_meta ? $crm_meta->is_ad : '0';

         $listing_data = DB::connection('mysql2')->table('venues')->where('slug', $preference)->first();
            if (!$listing_data) {
                $listing_data = DB::connection('mysql2')->table('vendors')->where('slug', $preference)->first();
                if ($listing_data && isset($listing_data->vendor_category_id)) {
                    $cat_data_cms = DB::connection('mysql2')->table('vendor_categories')->where('id', $listing_data->vendor_category_id)->first();
                    if ($cat_data_cms && isset($cat_data_cms->name)) {
                        $lead_cat_data = $cat_data_cms->name;
                    }
                }
            }
            $locality = null;
            if ($listing_data && isset($listing_data->location_id)) {
                $locality = DB::connection('mysql2')->table('locations')->where('id', $listing_data->location_id)->first();
            } else {
                $lead_cat_data = "Phone Nav";
            }

        $lead = Lead::where('mobile', $mobile)->first();
        if ($lead) {
            if ($recording_url !== null) {
                $metadata = [
                    'datetime' => $current_timestamp,
                    'caller_agent' => $caller_agent_name
                ];
                $metadata_json = json_encode($metadata);

                $recording_urls = json_decode($lead->recording_url, true) ?? [];
                $recording_urls[] = [
                    'url' => $recording_url,
                    'metadata' => $metadata_json
                ];
                $recording_url_json = json_encode($recording_urls);

                $lead->recording_url = $recording_url_json;
            }
            $lead->enquiry_count += 1;
        } else {
            $lead = new Lead();
            $lead->name = $request->input('name');
            $lead->email = $request->input('email');
            $lead->mobile = $mobile;
            if ($recording_url !== null) {
                $metadata = [
                    'datetime' => $current_timestamp,
                    'caller_agent' => $caller_agent_name
                ];
                $metadata_json = json_encode($metadata);

                $recording_urls = [
                    [
                        'url' => $recording_url,
                        'metadata' => $metadata_json
                    ]
                ];
                $recording_url_json = json_encode($recording_urls);
                $lead->recording_url = $recording_url_json;
            }
        }
        $lead->lead_datetime = $current_timestamp;
        $lead->source = $lead_source;
        $lead->lead_catagory = $lead_cat_data;
        $lead->preference = $preference;
        $lead->locality = $locality ? $locality->name : null;
        $lead->lead_status = "Super Hot Lead";
        $lead->read_status = false;
        $lead->service_status = false;
        $lead->done_title = null;
        $lead->is_ad = $id_ad;
        $lead->done_message = null;
        $lead->lead_color = "#4bff0033";
        $lead->virtual_number = $call_to_wb_api_virtual_number;
        $lead->whatsapp_msg_time = $current_timestamp;
        $lead->save();
        if ($lead->last_forwarded_by == null) {
            $lead->assign_to = $get_rm ? $get_rm->name : null;
            $lead->assign_id = $get_rm ? $get_rm->id : null;
        $lead->save();

        }
        return response()->json(['status' => true, 'msg' => 'Thank you for contacting us. Our team will reach you soon with the best price..!']);
    } catch (\Throwable $th) {
        Log::error('Error processing Tata call data: ' . $th->getMessage());
        return response()->json(['status' => false, 'msg' => 'Something went wrong.', 'err' => $th->getMessage()], 500);
    }
});

Route::post('/new_lead', function (Request $request) {
    Log::info($request);
    $startSubstring = "-546674";
    $endSubstring = "@";
    $encoded = $request->post('token');
    $extractedValue = $encoded;
    $string = base64_decode(base64_decode($extractedValue));
    $startPos = strpos($string, $startSubstring);
    $endPos = strpos($string, $endSubstring, $startPos);
    $finalValue = substr($string, $startPos + strlen($startSubstring), $endPos - $startPos - strlen($startSubstring));
    $randompassvalue = DB::connection('mysql')->table('randomnes')->where('id', 1)->value('random_pass');
    $outvalue = md5(sha1(md5($randompassvalue)));

    if ($finalValue == $outvalue) {
        try {
            $is_name_valid = $request->post('name') != null ? "required|string|max:255" : "";
            $is_email_valid = $request->post('email') != null ? "required|email" : "";
            $is_preference_valid = $request->post('preference') != null ? "required|string|max:255" : "";
            $mobile = $request->post('mobile');
            $validate = Validator::make($request->all(), [
                'name' => $is_name_valid,
                'email' => $is_email_valid,
                'preference' => $is_preference_valid,
            ]);
            $lead_from = '';
            if ($validate->fails()) {
                return response()->json(['status' => false, 'msg' => $validate->errors()->first()]);
            }
            $mobile = $request->post('mobile') ?: $request->post('caller_id_number');
            $pattern = "/^\d{10}$/";
            if (!preg_match($pattern, $mobile)) {
                return response()->json(['status' => false, 'msg' => "Invalid mobile number."]);
            } elseif ($mobile <= 6000000000) {
                return response()->json(['status' => false, 'msg' => "Invalid mobile number."]);
            }
            $current_timestamp = date('Y-m-d H:i:s');
            if ($request->post('call_to_number')) {
                $call_to_wb_api_virtual_number = $request->post('call_to_number');
                $lead_source = "WB|Call";
                $crm_meta = CrmMeta::find(1);
                $preference = $crm_meta->meta_value;
                $lead_from = $crm_meta->lead_from;
            } else {
                $call_to_wb_api_virtual_number = null;
                $lead_source = "WB|Form";
                $preference = $request->post('preference');
                $lead_from = $request->post('lead_from');
            }

            $url_components = parse_url($preference);
            if (isset($url_components['query'])) {
                parse_str($url_components['query'], $query_params);
                $cleaned_query_params = array_filter($query_params, function ($value) {
                    return !empty($value);
                });
                $cleaned_query_string = http_build_query($cleaned_query_params);
                $cleaned_url = $url_components['path'] . ($cleaned_query_string ? '?' . $cleaned_query_string : '');
                $preference = $cleaned_url;
            }

            $lead_cat_data = "Venue";
            $listing_data = null;

            $listing_data = DB::connection('mysql2')->table('venues')->where('slug', $preference)->first();
            if (!$listing_data) {
                $listing_data = DB::connection('mysql2')->table('vendors')->where('slug', $preference)->first();
                if ($listing_data && isset($listing_data->vendor_category_id)) {
                    $cat_data_cms = DB::connection('mysql2')->table('vendor_categories')->where('id', $listing_data->vendor_category_id)->first();
                    if ($cat_data_cms && isset($cat_data_cms->name)) {
                        $lead_cat_data = $cat_data_cms->name;
                    }
                }
            }
            $locality = null;
            if ($listing_data && isset($listing_data->location_id)) {
                $locality = DB::connection('mysql2')->table('locations')->where('id', $listing_data->location_id)->first();
            } else {
                $lead_cat_data = "Phone Nav";
            }

            $lead = Lead::where('mobile', $mobile)->first();
            if ($lead) {
                $lead->enquiry_count = $lead->enquiry_count + 1;
            } else {
                $lead = new Lead();
                $lead->name = $request->post('name');
                $lead->email = $request->post('email');
                $lead->mobile = $mobile;
            }
            $lead->lead_from = $lead_from;
            $lead->lead_datetime = $current_timestamp;
            $lead->source = $lead_source;
            $lead->lead_catagory = $lead_cat_data;
            $lead->preference = $preference;
            $lead->is_ad = $request->post('is_ad');
            $lead->locality = $locality ? $locality->name : null;
            $lead->lead_status = "Super Hot Lead";
            $lead->read_status = false;
            $lead->service_status = false;
            $lead->done_title = null;
            $lead->done_message = null;
            $lead->whatsapp_msg_time = $current_timestamp;
            $lead->lead_color = "#4bff0033"; //green color
            $lead->virtual_number = $call_to_wb_api_virtual_number;
            $lead->user_ip = $request->post('user_ip');
            $lead->save();
            if ($lead->last_forwarded_by == null) {
                $get_rm = getAssigningRm();
                $lead->assign_to = $get_rm->name;
                $lead->assign_id = $get_rm->id;
                $lead->save();
            }
            return response()->json(['status' => true, 'msg' => 'Thank you for contacting us. Our team will reach you soon with best price..!']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'msg' => 'Something went wrong.', 'err' => $th->getMessage()], 500);
        }
    } else {
        return response()->json(['status' => false, 'msg' => 'Something went wrong.'], 500);
    }
});

Route::post('/business_lead', function (Request $request) {
    Log::info($request);
    try {
        $is_name_valid = $request->post('name') != null ? "required|string|max:255" : "";
        $validate = Validator::make($request->all(), [
            'name' => $is_name_valid,
        ]);

        $name = $request->post('name');
        $mobile = $request->post('phone');
        $current_timestamp = date('Y-m-d H:i:s');
        $source = "WB|Site";
        $lead = BdmLead::where('mobile', $mobile)->first();

        $bussinesCatId = 4;
        if ($request->post('business_type') == '2') {
            $bussinesCatId = get_business_cat($request->post('business_category'));
        }
        if ($lead) {
            $lead->enquiry_count = $lead->enquiry_count + 1;
        } else {
            $lead = new BdmLead();
            $lead->name = $name;
            $lead->mobile = $mobile;
            $lead->business_cat = $bussinesCatId;
            $lead->business_name = $request->post('business_name');
        }
        $lead->lead_datetime = $current_timestamp;
        $lead->email = $request->post('email');
        $lead->source = $source;
        $lead->lead_status = 'Hot';
        $lead->city = $request->post('city');
        $get_bdm = getAssigningBdm();
        $lead->assign_to = $get_bdm->name;
        $lead->assign_id = $get_bdm->id;
        $lead->whatsapp_msg_time = $current_timestamp;
        $lead->user_ip = $request->post('user_ip');;
        $lead->lead_color = "#0066ff33";
        $lead->save();
        return response()->json(['status' => true, 'msg' => 'Thank you for SignUp. Our team will reach you soon with best price..!'], 200);
    } catch (\Throwable $th) {
        return response()->json(['status' => false, 'msg' => 'Something went wrong.', 'err' => $th->getMessage()], 500);
    }
});

Route::post('handle_calling_request', function (Request $request) {
    $validate = Validator::make($request->all(), [
        'slug' => 'required|string|max:255',
    ]);
    if ($validate->fails()) {
        return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
    }
    try {
        $crm_meta = CrmMeta::find(1);
        $crm_meta->meta_value = $request->slug;
        $crm_meta->is_ad = $request->is_ad;
        $crm_meta->lead_from = $request->lead_from;
        $crm_meta->save();
        return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Data stored successfully.']);
    } catch (\Throwable $th) {
        return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Somethin went wrong, please try again later.']);
    }
});
