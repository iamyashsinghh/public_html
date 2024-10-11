<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatGptController extends Controller
{
    public function generateMessage(Request $request)
{
    $aiprompt = $request->input('aiprompt');

    $apiKey = env('OPENAI_API_KEY');

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
    ])->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-3.5-turbo', // Or 'gpt-4' if available
        'messages' => [
            ['role' => 'system', 'content' => 'You are a CRM message generator for vendors. Write professional messages.'],
            ['role' => 'user', 'content' => $aiprompt],
        ]
    ]);

    $generatedMessage = $response->json('choices')[0]['message']['content'];

    return response()->json(['message' => $response]);
}

}
