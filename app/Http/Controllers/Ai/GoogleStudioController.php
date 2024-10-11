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
        $auth_user = Auth::guard('team')->user();

        if($auth_user->role_id == 4){
            $aiprompt .= " \n see some example responses \n
            input: Engagement/ 22 sep/ 300pax/ veg / dinner/ west delhi\n
            output: We have an engagement booking for September 22nd, with 300 guests, vegetarian dinner in West Delhi.  Additionally, we have a wedding lead for February 17th, with 450 guests for a vegetarian dinner in West Delhi. Please confirm availability and proceed with the necessary steps for both events. \n
            input: Engagement/ 22 sep/ 300pax/ veg / dinner/ west delhi\n
            output: We have an engagement lead for September 22nd, with 300 guests for a vegetarian dinner in West Delhi.  Additionally, we have a wedding lead for February 17th, with 450 guests for a vegetarian dinner in West Delhi. Please confirm availability for both events and proceed with the necessary steps. \n
            input: Engagement / 20 or 21 Nov / Pax - 150 / Veg / Dinner / Location - West Delhi\n
            output: We have an engagement booking for November 20th or 21st, with 150 guests for a vegetarian dinner in West Delhi. Please confirm availability and proceed with the necessary steps for this event. \n
            input:Wedding/9 Oct/600 pax/Dinner/Veg/South Delhi \n
            output: We have a wedding lead for October 9th, with 600 guests for a vegetarian dinner in South Delhi. Please confirm availability and proceed with the necessary steps.
             ";
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
