<?php

namespace App\Http\Controllers;

use App\Models\BdmLead;
use App\Models\Lead;
use App\Models\nvLead;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Str;
use App\Models\whatsappMessages;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Http;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function interakt_wa_msg_send(int $phone_no, string $name, string $message, string $template_type) {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $token = env("TATA_AUTH_KEY");
        $authToken = "Bearer $token";
        $response = Http::withHeaders([
            'Authorization' => $authToken,
            'Content-Type' => 'application/json',
        ])->post($url, [
                    "to" => "91{$phone_no}",
                    "type" => "template",
                    "template" => [
                        "name" => "login_otp_new",
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
                            ],
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => "$message",
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);
            $currentTimestamp = Carbon::now();
            $msg = "Hi $name, /n We are excited to have you on board! Use OTP {{1}} to log in.  Kindly enter the OTP to proceed. Please note, that this OTP is valid for the next 10 minutes and can be used only once. For optimum security, please don’t share your OTP with anyone. Thank You,";
            $bodyMsg = Str::replace('{{1}}', $message, $msg);
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$phone_no";
            $newWaMsg->msg_from = "$phone_no";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "$bodyMsg";
            $newWaMsg->save();
            return $response;
    }


    function notify_vendor_lead_using_interakt($phone, $name, $message, $lead_id) {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $token = env("TATA_AUTH_KEY");
        $authToken = "Bearer $token";
        $response = Http::withHeaders([
            'Authorization' => $authToken,
            'Content-Type' => 'application/json',
        ])->post($url, [
                    "to" => "91{$phone}",
                    "type" => "template",
                    "source" => "external",
                    "template" => [
                        "name" => "notify_vendor_lead_wb",
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
                            ],
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => "$message",
                                    ]
                                ]
                            ],
                            [
                                "type"=> "button",
                                "sub_type"=> "URL",
                                "index"=> 0,
                                "parameters"=> [
                                  [
                                    "type"=> "text",
                                    "text"=> "$lead_id"
                                  ]
                                ]
                            ]
                        ]
                    ] 
                ]);
                $currentTimestamp = Carbon::now();
                $msg = "Hi $name, /n You have just received a new inquiry from *{{1}}* have booked with Wedding Banquets. For any Query:- Don't hesitate to get in touch with our *RM* Team: 8882198989 *Respond to leads on the go with the Wedding Banquets for Business CRM.*";
                $bodyMsg = Str::replace('{{1}}', $message, $msg);
                $newWaMsg = new whatsappMessages();
                $newWaMsg->msg_id = "$phone";
                $newWaMsg->msg_from = "$phone";
                $newWaMsg->time = $currentTimestamp;
                $newWaMsg->type = 'text';
                $newWaMsg->is_sent = "1";
                $newWaMsg->body = "$bodyMsg";
                $newWaMsg->save();
            return $response;
    }

    function notify_wbvendor_lead_using_interakt($phone, $name, $number, $eventdate, $pax, $lead_id) {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $token = env("TATA_AUTH_KEY");
        $authToken = "Bearer $token";
        $response = Http::withHeaders([
            'Authorization' => $authToken,
            'Content-Type' => 'application/json',
        ])->post($url, [
                    "to" => "91{$phone}",
                    "type" => "template",
                    "source" => "external",
                    "template" => [
                        "name" => "wb_venue_lead_alert",
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
                            ],
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => "$number",
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "$eventdate",
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "$pax",
                                    ]
                                ]
                            ],
                            [
                                "type"=> "button",
                                "sub_type"=> "URL",
                                "index"=> 0,
                                "parameters"=> [
                                  [
                                    "type"=> "text",
                                    "text"=> "$lead_id"
                                  ]
                                ]
                            ]
                        ]
                    ]
                ]);

                $currentTimestamp = Carbon::now();
                $msg = "Hi $name, /n You have just received a new inquiry from  *{{1}},* they require a wedding itinerary from *Wedding Banquets.*, *Event Date:- {{2}}*, *Pax:- {{3}}*, For any Query:- Don't hesitate to get in touch with our *RM* Team: *8882198989*, *Respond to leads on the go with the Wedding Banquets for Business CRM.*";
                $bodyMsg = Str::replace('{{1}}', $number, $msg);
                $bodyMsg = Str::replace('{{2}}', $eventdate, $bodyMsg);
                $bodyMsg = Str::replace('{{3}}', $pax, $bodyMsg);
                $newWaMsg = new whatsappMessages();
                $newWaMsg->msg_id = "$phone";
                $newWaMsg->msg_from = "$phone";
                $newWaMsg->time = $currentTimestamp;
                $newWaMsg->type = 'text';
                $newWaMsg->is_sent = "1";
                $newWaMsg->body = "$bodyMsg";
                $newWaMsg->save();
                return $response;

    }

    public function validate_venue_lead_phone_number($phone_number) {
        $pattern = "/^\d{10}$/";
        try {
            if (preg_match($pattern, $phone_number)) {
                $lead = Lead::where('mobile', $phone_number)->first();
                if (!$lead) { // lead should not exist
                    return response()->json(['success' => true, 'alert_type' => 'success', 'message' => "Phone number validated."]);
                } else {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Lead is already exist with this phone number, Contact to your manager or admin."]);
                }
            } else {
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Invalid phone number."]);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
        }
    }

    public function sendNotification($userid, $title = 'hello', $body= '9565676128')
    {

        $token = TeamMember::where('id',$userid)->first();
        if(empty($token->device_token)){
            return 'done';
        }
        $msg = "Hey $token->name, you have recived a new lead. Mobile No: $body";
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_MSG_SERVER_KEY');
        $data = [
            "registration_ids" => [$token->device_token],
            "notification" => [
                "title" => $title,
                "body" => $msg,
                "icon" => "https://wbcrm.in/favicon.jpg",
                "click_action" => "https://wbcrm.in/team/venue-crm/leads/list"
            ]
        ];
        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        dd($result);
    }

    public function validate_nonvenue_lead_phone_number($phone_number) {
        $pattern = "/^\d{10}$/";
        try {
            if (preg_match($pattern, $phone_number)) {
                $lead = nvLead::where('mobile', $phone_number)->first();
                if (!$lead) {
                    return response()->json(['success' => true, 'alert_type' => 'success', 'message' => "Phone number validated."]);
                } else {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Lead is already exist with this phone number, Contact to your manager or admin."]);
                }
            } else {
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Invalid phone number."]);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
        }
    }

    public function validate_bdm_lead_phone_number($phone_number) {
        $pattern = "/^\d{10}$/";
        try {
            if (preg_match($pattern, $phone_number)) {
                $lead = BdmLead::where('mobile', $phone_number)->first();
                if (!$lead) {
                    return response()->json(['success' => true, 'alert_type' => 'success', 'message' => "Phone number validated."]);
                } else {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Lead is already exist with this phone number, Contact to your manager or admin."]);
                }
            } else {
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Invalid phone number."]);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => "Something went wrong."]);
        }
    }

    public function getCalender() {
        $calendar = [];
        for ($i = 0; $i <= 12; $i++) {
            if ($i == 0) {
                array_push($calendar, Carbon::today()->setDay(1)->addMonth(-1));
            } elseif ($i == 1) {
                array_push($calendar, Carbon::today()->startOfMonth());
            } else {
                array_push($calendar, Carbon::today()->setDay(1)->addMonth($i - 1));
            }
        }
        return $calendar;
    }
}
