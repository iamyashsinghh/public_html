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
            $aiprompt = `$aiprompt see some example responses input: Wedding/17feb/450pax/Dinner/Veg/West Delhi
output: Lead for a wedding on 17th Feb, 450 pax, vegetarian dinner in West Delhi. Please take the necessary action to move forward with this lead
input:Wedding/17feb/450pax/Dinner/Veg/West Delhi
output:We have a wedding booking for 450 guests on February 17th. It's a vegetarian dinner event in West Delhi. Please confirm availability and proceed with the necessary steps to secure this booking.`;
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
