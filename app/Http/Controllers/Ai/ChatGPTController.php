<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTController extends Controller
{
    public function generateMessage(Request $request)
    {
        $aiprompt = $request->input('aiprompt');
        $msg_type = $request->input('msg_type');

        $apiKey = env('CHATGPT_API_KEY');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant.write this msg in a formal way 1 sentence only  i am the rm of the company i am sahring this info to the venue manager so the venue manager work on that lead do not include the venue manager name just a sentence',
                ],
                [
                    'role' => 'user',
                    'content' => $aiprompt,
                ],
            ],
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if (isset($responseData['choices'][0]['message']['content'])) {
                $generatedMessage = trim($responseData['choices'][0]['message']['content']);
            } else {
                $generatedMessage = 'No valid message found in the response.';
            }
        } else {
            $generatedMessage = 'Error: Unable to generate message.';
        }

        return response()->json(['message' => $generatedMessage]);
    }
}
