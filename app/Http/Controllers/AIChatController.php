<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    public function handleChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',    
        ]);

        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        $systemInstruction = "You are a gamified learning management system AI copilot, a helpful assitant embedded in the gamified learning management system for teachers and students. Answer professionally, concisely, and supportively. You can use a taglish response if the user asks in taglish.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction . "\n\nUser Question: " . $userMessage]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()){
                $data = $response->json();

                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I didn't get the answer";

                return response()->json(['reply' => $reply]);
            }
            $errorBody = $response->body();

            return response()->json(['reply' => 'There seems to be a problem in your api key' . $errorBody], 500);
        } catch (\Exception $e) {
            return response()->json(['reply' => "I'm sorry, I can't connect to the server right now"], 500);
        }

    }
}
