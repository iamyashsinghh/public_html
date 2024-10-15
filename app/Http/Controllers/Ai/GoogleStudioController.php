<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleStudioController extends Controller
{
    public function generateMessage(Request $request)
    {
        $aiprompt = $request->input('aiprompt');
        $msg_type = $request->input('msg_type');

        if($msg_type == 'rm_msg'){
            $aiprompt .= " \n see some example responses \n
            input: Engagement/ 22 sep/ 300pax/ veg / dinner/ west delhi\n
            output: We have an engagement booking for September 22nd, with 300 guests, vegetarian dinner in West Delhi.  Additionally, we have a wedding lead for February 17th, with 450 guests for a vegetarian dinner in West Delhi. Please confirm availability and proceed with the necessary steps for both events. \n
            input: Engagement/ 22 sep/ 300pax/ veg / dinner/ west delhi\n
            output: We have an engagement lead for September 22nd, with 300 guests for a vegetarian dinner in West Delhi.  Additionally, we have a wedding lead for February 17th, with 450 guests for a vegetarian dinner in West Delhi. Please confirm availability for both events and proceed with the necessary steps. \n
            input: Engagement / 20 or 21 Nov / Pax - 150 / Veg / Dinner / Location - West Delhi\n
            output: We have an engagement booking for November 20th or 21st, with 150 guests for a vegetarian dinner in West Delhi. Please confirm availability and proceed with the necessary steps for this event. \n
            input:Wedding/9 Oct/600 pax/Dinner/Veg/South Delhi \n
            output: We have a wedding lead for October 9th, with 600 guests for a vegetarian dinner in South Delhi. Please confirm availability and proceed with the necessary steps.\n
            only give 1 output in formal tone";
        }elseif($msg_type == 'nvrm_msg'){
            $aiprompt .= " \n see some example responses \n
            input: Customer Required photographer /Birthday/15dec/100pax/west delhi/Firstly share all details on whatsapp and call customer right now\n
            output: Customer requires a photographer for a birthday event on December 15th in West Delhi, with approximately 100 guests. Please share all service details via WhatsApp first, then call the customer immediately. \n
            input: Customer required photographer/Wedding/19jan/300pax/Mayapuri location/Firstly share details on whatsapp and call after 7 p.m\n
            output: We have a customer looking for a wedding photographer for an event on January 19th. The event will host approximately 300 guests and will take place in Mayapuri. The client has requested that you first share your service details via WhatsApp, and then follow up with a call after 7 p.m. \n
            input: Customer required Band/Wedding/23Feb/225pax/Rohini location/Firstly share details on whatsapp and call in evening\n
            output: Customer requires a band for a wedding on February 23rd in Rohini, with approximately 225 guests. Please share the details via WhatsApp first, then call in the evening.\n
            input:Wedding/9 Oct/600 pax/Dinner/Veg/South Delhi \n
            output: Customer requires a band for a wedding on December 6th at Maharaja Banquet, Punjabi Bagh, with approximately 150 guests. Please share the details via WhatsApp first; the customer will review and update accordingly\n
            only give 1 output in formal tone";
        }

        $apiKey = env('GOOGLE_AI_STUDIO_API_KEY');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $aiprompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $generatedMessage = $responseData['candidates'][0]['content']['parts'][0]['text'];
            } else {
                $generatedMessage = 'No valid message found in the response.';
            }
        } else {
            $generatedMessage = 'Error: Unable to generate message.';
        }

        return response()->json(['message' => $generatedMessage]);
    }
}
