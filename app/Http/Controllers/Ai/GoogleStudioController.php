<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleStudioController extends Controller
{
    public function generateMessage(Request $request)
    {
        $aiprompt = $request->input('aiprompt');

        // Use your Gemini API key
        $apiKey = env('GOOGLE_AI_STUDIO_API_KEY');

        // Send the POST request to the Gemini 1.5 Flash API
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
        return response()->json(['message' => $generatedMessage]);
    }
}
