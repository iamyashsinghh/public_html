<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BdmLead;
use App\Models\Lead;
use App\Models\nvLead;
use App\Models\nvrmLeadForward;
use App\Models\Vendor;
use App\Models\WhatsappBulkMsgTask;
use App\Models\whatsappMessages;
use App\Models\WhatsappCampain;
use App\Models\WhatsappMsgLogs;
use App\Models\WhatsappTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsappMsgController extends Controller
{

    public function ajax_tasks()
    {
        $whatsapp_bulk_msg_task = WhatsappBulkMsgTask::select(
            'whatsapp_bulk_msg_task.id',
            'whatsapp_bulk_msg_task.campaign_name',
            'whatsapp_bulk_msg_task.img',
            'whatsapp_bulk_msg_task.msg',
            'whatsapp_bulk_msg_task.status',
            'whatsapp_bulk_msg_task.created_at',
        )->get();
        return datatables($whatsapp_bulk_msg_task)->toJson();
    }

    public function create_task_by_number(Request $request)
    {
        $WhatsappCampain = WhatsappCampain::select('name', 'template_name', 'is_rm_name', 'team_name')->where('id', $request->input('camp_name'))->first();
        $newtask = new WhatsappBulkMsgTask();
        $newtask->campaign_name = $WhatsappCampain->name;
        $newtask->numbers = $request->input('recipient');
        $WhatsappTemplates = WhatsappTemplates::where('template_name', $WhatsappCampain->template_name)->first();
        $newtask->img = $WhatsappTemplates->img;
        $newtask->msg = $WhatsappTemplates->msg;
        $newtask->template_name = $WhatsappCampain->template_name;
        $newtask->is_rm_name = $WhatsappCampain->is_rm_name;
        $newtask->team_name = $WhatsappCampain->team_name;
        $newtask->status = "0";
        if ($newtask->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Task saved successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save task',
            ]);
        }
    }

    public function create_task_by_id(Request $request)
    {
        $WhatsappCampain = WhatsappCampain::select('name', 'template_name', 'is_rm_name', 'team_name')->where('id', $request->input('camp_name'))->first();
        $newtask = new WhatsappBulkMsgTask();
        $newtask->campaign_name = $WhatsappCampain->name;
        $newtask->lead_ids = $request->input('recipient');
        $WhatsappTemplates = WhatsappTemplates::where('template_name', $WhatsappCampain->template_name)->first();
        $newtask->img = $WhatsappTemplates->img;
        $newtask->msg = $WhatsappTemplates->msg;
        $newtask->template_name = $WhatsappCampain->template_name;
        $newtask->is_rm_name = $WhatsappCampain->is_rm_name;
        $newtask->team_name = $WhatsappCampain->team_name;
        $newtask->lead_id_type = $request->post('lead_id_type');
        $newtask->status = "0";
        if ($newtask->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Task saved successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save task',
            ]);
        }
    }

    public function get_whatsapp_doc($id)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $authKey = env('TATA_AUTH_KEY');
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/media/download/$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $authKey",
            ],
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            // Log::error("Curl error: " . curl_error($curl));
            curl_close($curl);
            return null;
        }
        curl_close($curl);
        if ($httpcode >= 200 && $httpcode < 300) {
            $data = json_decode($response, true);
            // Log::info("Media URL for message: " . $response);
            return $data['url'] ?? null;
        } else {
            // Log::error("Request failed with HTTP status $httpcode and response: $response");
            return null;
        }
    }

    public function whatsapp_msg_get($id)
    {
        $perPage = 5;
        $messages = whatsappMessages::where('msg_from', $id)
            ->orderBy('time', 'desc')
            ->paginate($perPage);
        foreach ($messages as $message) {
            if (!empty($message->doc)) {
                $mediaUrl = $this->get_whatsapp_doc($message->doc);
                $message->doc = $mediaUrl;
            }
        }
        return response()->json($messages);
    }

    private function countVariables($text)
    {
        preg_match_all('/\{\{(\d+)\}\}/', $text, $matches);
        return count($matches[0]);
    }

    public function fetchTemplates(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = 'https://wb.omni.tatatelebusiness.com/templates';
        $token = env("TATA_AUTH_KEY");
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
        ])->get($url);

        if ($response->successful()) {
            $apiData = $response->json()['data'];
            $templates = collect($apiData);

            $data = $templates->map(function ($template) {
                $templateExists = WhatsappTemplates::where('template_name', $template['name'])->first();

                $headerVariableCount = 0;
                $bodyVariableCount = 0;
                foreach ($template['components'] as $component) {
                    if ($component['type'] === 'HEADER' && isset($component['text'])) {
                        $headerVariableCount += $this->countVariables($component['text']);
                    } elseif ($component['type'] === 'BODY') {
                        $bodyVariableCount += $this->countVariables($component['text']);
                    }
                }

                $img = WhatsappTemplates::where('template_name', $template['name'])->first()->img ?? null;
                $msg = $template['components'][1]['text'] ?? 'No message';

                if ($templateExists) {
                } else {
                    WhatsappTemplates::create([
                        'template_name' => $template['name'],
                        'is_variable_template' => $headerVariableCount > 0 || $bodyVariableCount > 0,
                        'img' => $img,
                        'msg' => $msg,
                        'status' => $template['status'],
                        'head_val' => $headerVariableCount,
                        'body_val' => $bodyVariableCount,
                        'last_updated_time' => $template['last_updated_time'],
                    ]);
                }
                return [
                    'id' => $template['id'],
                    'temp_name' => $template['name'],
                    'img' => $img,
                    'msg' => $msg,
                    'status' => $template['status'],
                    'created_at' => $template['last_updated_time'],
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $templates->count(),
                'recordsFiltered' => $templates->count(),
                'data' => $data,
            ]);
        } else {
            return response()->json(['error' => 'Failed to fetch templates.'], 500);
        }
    }


    public function whatsapp_msg_get_new($id, Request $request)
    {
        $lastTimestamp = $request->input('lastTimestamp');

        $messages = whatsappMessages::where('msg_from', $id)
            ->where('time', '>', $lastTimestamp)
            ->orderBy('time', 'asc')
            ->get();

        foreach ($messages as $message) {
            if (!empty($message->doc)) {
                $mediaUrl = $this->get_whatsapp_doc($message->doc);
                $message->doc = $mediaUrl;
            }
        }

        return response()->json($messages);
    }

    public function whatsapp_msg_send(Request $request)
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
            "to" => "91$request->recipient",
            "type" => "text",
            "text" => [
                "body" => "$request->message"
            ]
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "$request->message";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_multiple()
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $messagesSent = 0;
        $maxMessages = 5;
        $token = env("TATA_AUTH_KEY");
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authToken = "Bearer $token";
        $WhatsappBulkMsgTask = WhatsappBulkMsgTask::where('status', '0')->first();
        if (!$WhatsappBulkMsgTask) {
            echo "No task available.";
            return;
        }
        $loggedNumbers = WhatsappMsgLogs::where('task_id', $WhatsappBulkMsgTask->id)
            ->pluck('number')->toArray();
        $phoneNumbers = [];
        if ($WhatsappBulkMsgTask->lead_id_type == '1') {
            $leadIds = explode(',', $WhatsappBulkMsgTask->lead_ids);
            foreach ($leadIds as $leadid) {
                $lead = Lead::select('mobile')->where('lead_id', $leadid)->first();
                if ($lead !== null && !in_array($lead->mobile, $loggedNumbers)) {
                    $phoneNumbers[] = $lead->mobile;
                } else {
                }
            }
            print_r($phoneNumbers);
        } elseif ($WhatsappBulkMsgTask->lead_id_type == '2') {
            $leadIds = explode(',', $WhatsappBulkMsgTask->lead_ids);
            foreach ($leadIds as $leadid) {
                $lead = nvLead::select('mobile')->where('id', $leadid)->first();
                if ($lead !== null && !in_array($lead->mobile, $loggedNumbers)) {
                    $phoneNumbers[] = $lead->mobile;
                } else {
                }
            }
        } elseif ($WhatsappBulkMsgTask->lead_id_type == '3') {
            $leadIds = explode(',', $WhatsappBulkMsgTask->lead_ids);
            foreach ($leadIds as $leadid) {
                $lead = BdmLead::select('mobile')->where('lead_id', $leadid)->first();
                if ($lead !== null && !in_array($lead->mobile, $loggedNumbers)) {
                    $phoneNumbers[] = $lead->mobile;
                } else {
                }
            }
        } else {
            if (!empty($WhatsappBulkMsgTask->numbers)) {
                $tempNumbers = explode(',', $WhatsappBulkMsgTask->numbers);
                foreach ($tempNumbers as $number) {
                    if (isset($number) && !in_array(trim($number), $loggedNumbers)) {
                        $phoneNumbers[] = trim($number);
                    }
                }
            }
        }
        if (count($phoneNumbers) == 0) {
            $WhatsappBulkMsgTask->status = "1";
            $WhatsappBulkMsgTask->save();
        }
        foreach ($phoneNumbers as $phoneNumber) {
            if ($messagesSent >= $maxMessages) {
                break;
            }
            $messagesSent++;
            $phoneNumber = trim($phoneNumber);
            if ($WhatsappBulkMsgTask->lead_id_type == '3') {
                $getname = BdmLead::select('name')->where('mobile', $phoneNumber)->first();
            } else {
                $getname = Lead::select('name')->where('mobile', $phoneNumber)->first();
                if (!$getname) {
                    $getname = nvLead::select('name')->where('mobile', $phoneNumber)->first();
                }
            }

            if (empty($getname->name)) {
                $getname->name = "sir/mam";
            }
            $var_name = $getname->name;
            if ($WhatsappBulkMsgTask->is_rm_name == 1) {
                $var_name = $WhatsappBulkMsgTask->team_name;
            }
            $response = Http::withHeaders([
                'Authorization' => $authToken,
                'Content-Type' => 'application/json',
            ])->post($url, [
                "to" => "91{$phoneNumber}",
                "type" => "template",
                "source" => "external",
                "template" => [
                    "name" => "$WhatsappBulkMsgTask->template_name",
                    "language" => [
                        "code" => "en"
                    ],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "image",
                                    "image" => [
                                        "link" => "$WhatsappBulkMsgTask->img"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => "$var_name",
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $tempMsg = WhatsappTemplates::where('template_name', $WhatsappBulkMsgTask->template_name)->first()->msg;
            $bodyMsg = Str::replace('{{1}}', $var_name, $tempMsg);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = $phoneNumber;
            $newWaMsg->msg_from = $phoneNumber;
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "$bodyMsg";
            $newWaMsg->save();

            $whatsappLogs = new WhatsappMsgLogs([
                'campaign_name' => $WhatsappBulkMsgTask->campaign_name,
                'task_id' => $WhatsappBulkMsgTask->id,
                'number' => $phoneNumber,
                'status' => $response->successful() ? '0' : '1',
            ]);
            $whatsappLogs->save();
        }
    }

    public function whatsapp_msg_status(Request $request)
    {
        $guards = ['team'];
        $auth_user = null;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $auth_user = Auth::guard($guard)->user();
                break;
            }
        }
        if ($auth_user) {
            $getlead = Lead::where('mobile', $request->mobile)->first();

            if (!$getlead) {
                return response()->json(['message' => 'Lead not found.'], 404);
            }

            if ($getlead->assign_id == $auth_user->id) {
                $getlead->is_whatsapp_msg = 0;
                if ($getlead->save()) {
                    return response()->json(['message' => 'WhatsApp message status updated successfully.'], 200);
                } else {
                    return response()->json(['message' => 'Failed to update the WhatsApp message status.'], 500);
                }
            } else {
                return response()->json(['message' => 'Unauthorized to update this lead.'], 403);
            }
        } else {
            return response()->json(['message' => 'Unauthorized access. No authenticated user found.'], 403);
        }
    }


    public function whatsapp_msg_status_nv_team(Request $request)
    {
        $getlead = nvrmLeadForward::where('mobile', $request->mobile)->first();
        if ($getlead) {
            $getlead->is_whatsapp_msg = 0;
            $getlead->save();
        }
    }

    public function whatsapp_msg_status_vendor(Request $request)
    {
        $getlead = nvLead::where('mobile', $request->mobile)->first();
        if ($getlead) {
            $getlead->is_whatsapp_msg = 0;
            $getlead->save();
        }
    }

    public function whatsapp_msg_status_nv_team_vendor(Request $request)
    {
        $getvendor = Vendor::where('mobile', $request->mobile)->first();
        if ($getvendor) {
            $getvendor->is_whatsapp_msg = 0;
            $getvendor->save();
        }
    }

    public function whatsapp_msg_status_bdm(Request $request)
    {
        $guards = ['bdm'];
        $auth_user = null;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $auth_user = Auth::guard($guard)->user();
                break;
            }
        }
        if ($auth_user) {
            $getlead = BdmLead::where('mobile', $request->mobile)->first();

            if (!$getlead) {
                return response()->json(['message' => 'Lead not found.'], 404);
            }

            if ($getlead->assign_id == $auth_user->id) {
                $getlead->is_whatsapp_msg = 0;
                if ($getlead->save()) {
                    return response()->json(['message' => 'WhatsApp message status updated successfully.'], 200);
                } else {
                    return response()->json(['message' => 'Failed to update the WhatsApp message status.'], 500);
                }
            } else {
                return response()->json(['message' => 'Unauthorized to update this lead.'], 403);
            }
        } else {
            return response()->json(['message' => 'Unauthorized access. No authenticated user found.'], 403);
        }
    }

    public function whatsapp_msg_send_hi(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');
        $latestMessage = whatsappMessages::where('msg_from', $request->recipient)->where('is_sent', 0)->orderBy('created_at', 'desc')->first();
        $now = Carbon::now();
        $sendTemplateMessage = false;
        if ($latestMessage) {
            $createdAt = new Carbon($latestMessage->created_at);
            $hoursDiff = $now->diffInHours($createdAt);
            if ($hoursDiff <= 24) {
                $sendTemplateMessage = true;
            }
        }

        // if value is false then temple else normal i dont know why but this code is running like this

        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, $sendTemplateMessage ? [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => "91$request->recipient",
            "type" => "text",
            "text" => [
                "body" => "hi"
            ]
        ] : [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "hi_msg",
                "language" => [
                    "code" => "en"
                ],
                "components" => [],
            ]
        ]);
        if ($response->successful()) {
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = $request->recipient;
            $newWaMsg->msg_from = $request->recipient;
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = $sendTemplateMessage ? "*Hi*" : "Hi";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_hello(Request $request)
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
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "hello",
                "language" => [
                    "code" => "en"
                ],
                "components" => [],
            ]
        ]);
        // Log::info($response);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "Hello";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_greet_btn(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $nameOfLead = Lead::where('mobile', $request->recipient)->first();
        if (!$nameOfLead) {
            $nameOfLead = nvLead::where('mobile', $request->recipient)->first();
        }
        $nameOfRecipient = $nameOfLead ? $nameOfLead->name : 'Sir/Madam';
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');
        $img = WhatsappTemplates::where('template_name', 'chat_section_btn_01')->first()->img ?? null;
        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "chat_section_btn_01",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "image",
                                "image" => [
                                    "link" => "$img"
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$nameOfRecipient",
                            ],
                            [
                                "type" => "text",
                                "text" => "$request->greetmsg",
                            ]
                        ]
                    ]
                ],
            ]
        ]);
        // Log::info($response);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "*Hi $nameOfRecipient*, \n I'm $request->greetmsg, your Wedding Planning assistant. Would you like me to help you find venues and wedding vendors for your wedding?";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_nv_greet_btn(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $nameOfVendor = Vendor::where('mobile', $request->recipient)->first();

        $nameOfRecipient = $nameOfVendor ? $nameOfVendor->name : 'Sir/Madam';
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "nv_vendor_chat_inisiate_btn",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$nameOfRecipient",
                            ]
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$request->greetmsg",
                            ]
                        ]
                    ]
                ],
            ]
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "*Hi $nameOfRecipient*, \n I'm $request->greetmsg, your *Relationship Manager*. Do you need any support to improve your lead conversion? If yes, please let me know.";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_bdm_greet_btn(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $nameOfVendor = BdmLead::where('mobile', $request->recipient)->first();

        $nameOfRecipient = $nameOfVendor ? $nameOfVendor->name : 'Sir/Madam';
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "bdm_nv_chat_btn_re",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$nameOfRecipient",
                            ]
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$request->greetmsg",
                            ]
                        ]
                    ]
                ],
            ]
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "*Hi $nameOfRecipient*, \n I'm $request->greetmsg, Your Key Account Manager, Are you looking for a source to grow your Business in Wedding industry? Wedding Banquets take pride in being a leading platform for booking Banquet Halls. Our platform helps *500+* clients in booking banquet halls every month. As, every Indian Wedding requires vendor services such as Professional *Photographers*, Celebrity *Makeup Artists*, Expert *Mehendi Artists* and Best *Band & Dhol* services. So, with our strong presence in the wedding industry, we aim to help you provide  💯% Assured and Guaranteed business by providing a platform for you to get more clientele traffic for expanding your availability to potential clients.✅Let's schedule a face-to-face meeting to discuss how we can help grow your business. Don't miss this business opportunity";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function uploadDocumentbdm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|mimes:jpg,jpeg,png,webp,pdf|max:20480',
            'documentTitle' => 'required|string',
            'phone_inp_id_doc' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $sanitizedFileName = time() . '-' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '-') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads/documents', $sanitizedFileName, 'public');

            $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
            $authKey = env('TATA_AUTH_KEY');
            $documentTitle = $request->input('documentTitle');
            $recipent = $request->input('phone_inp_id_doc');
            $doc_url = asset("storage/$filePath");

            $response = Http::withHeaders([
                'Authorization' => "Bearer $authKey",
                'Content-Type' => 'application/json'
            ])->post($url, [
                "to" => "91$recipent",
                "type" => "document",
                "document" => [
                    "link" => $doc_url,
                    "caption" => $documentTitle
                ]
            ]);

            if ($response->successful()) {
                $currentTimestamp = Carbon::now();
                $newWaMsg = new whatsappMessages();
                $newWaMsg->msg_id = $recipent;
                $newWaMsg->msg_from = $recipent;
                $newWaMsg->time = $currentTimestamp;
                $newWaMsg->type = 'text';
                $newWaMsg->is_sent = 1;
                $newWaMsg->body = "Document URL: $doc_url  && caption $documentTitle";
                $newWaMsg->save();

                return response()->json(['message' => 'Message sent successfully.'], 200);
            } else {
                return response()->json(['error' => 'Failed to send message.'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }

    public function whatsapp_msg_send_nv_subscription_btn_one(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $nameOfVendor = Vendor::where('mobile', $request->recipient)->first();

        $nameOfRecipient = $nameOfVendor ? $nameOfVendor->name : 'Sir/Madam';
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "subscription_reminder_1",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$nameOfRecipient",
                            ]
                        ]
                    ],
                ],
            ]
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "*Hi $nameOfRecipient*, \n\nHope you're doing well !! \n\n This is a reminder that your Premium Membership Subscription with Wedding Banquets is due for renewal in 5 days. To ensure the continued benefits of your Premium Listing and avoid any disruption to your business visibility, we kindly request you to make the payment at your earliest convenience. \n\nKindly feel free to reach out to me at 8882198989 for any questions. \n\nBest Regards, \nWedding Banquets \ninfo@weddingbanquets.in \n\n(Call Us Btn)";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_nv_subscription_btn_reminder(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $nameOfVendor = Vendor::where('mobile', $request->recipient)->first();

        $nameOfRecipient = $nameOfVendor ? $nameOfVendor->name : 'Sir/Madam';
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "subscription_reminder_reminder",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$nameOfRecipient",
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "*Hi $nameOfRecipient*, \n\n This is your final reminder that your Premium Membership Subscription with Wedding Banquets is due for renewal. To ensure the continued benefits of your premium listing and avoid any disruption to your business visibility, we kindly request you to make the payment at your earliest convenience.";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_nv_subscription_btn_two(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $nameOfVendor = Vendor::where('mobile', $request->recipient)->first();

        $nameOfRecipient = $nameOfVendor ? $nameOfVendor->name : 'Sir/Madam';
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "subscription_reminder_2",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$nameOfRecipient",
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "*Hi $nameOfRecipient*, \n\n Hope you're doing well !! \n\nPlease note that your CRM profile has been temporarily suspended due to the non-payment. This suspension has impacted your business operations, and you will not receive any new business leads until the payment is made. \n\nWe’re sorry to see you go! As you know, Our Premium showcases are invite-only memberships on Wedding Banquets that offer significantly higher visibility compared to Lite vendors on average, 9-10 times more. This increased visibility helps you attract more leads, allowing you to fill up your future wedding dates efficiently. \n\nWe hope you’ve had a great association with us so far and will reconnect as soon as you deem fit. Given that we're in the middle of the wedding season, we hope for a mutual benefit that the right time is very very soon. \n\nKindly feel free to reach out to me at *8882198989* for any questions. \n\nBest Regards,\nWedding Banquets \ninfo@weddingbanquets.in \n\n(Call Us Btn)";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }

    public function whatsapp_msg_send_pending_payment(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $nameOfVendor = Vendor::where('mobile', $request->recipient)->first();

        $nameOfRecipient = $nameOfVendor ? $nameOfVendor->name : 'Sir/Madam';
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "to" => "91$request->recipient",
            "type" => "template",
            "template" => [
                "name" => "pending_payment_noti",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "$nameOfRecipient",
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "*Hi $nameOfRecipient*, \n\nI hope this message finds you well. We are reaching out to remind you that your Membership Subscription with Wedding Banquets is currently Pending Payment. To continue enjoying the full benefits of your Premium Listing and ensure your business remains visible, please clear your balance at your earliest convenience. Please note that failure to make the payment may result in the suspension of your membership, which could affect your business visibility and promotional opportunities. \n\nIf you have any questions or need assistance, please don't hesitate to contact me at 8882198989. \n\nBest Regards,\n\nWedding Banquets \ninfo@weddingbanquets.in \n\n(Call Us Btn)";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }
}
