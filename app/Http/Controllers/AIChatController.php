<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    public function getChatHistory()
    {
        $user = Auth::user();

        $conversation = AiConversation::where('user_id', '=', $user->id)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$conversation){
            return response()->json([
                'conversation_id' => null,
                'messages' => [],
            ]);
        }
        
        //Gets all messages inside of the conversation
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get(['role', 'content']);

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $messages,
        ]);
    }

    public function handleChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'conversation_id' => 'nullable|integer',    
        ]);

        $userMessage = $request->input('message');
        $conversationId = $request->input('conversation_id');
        $apiKey = env('GEMINI_API_KEY');
        $user = Auth::user();

        if (!$conversationId){
            //Add an automatic title using the first 30 characters of the chat
            $title = substr($userMessage, 0, 30) . (strlen($userMessage) > 30 ? '...' : '');

            $conversation = AiConversation::create([
                'user_id' => $user->id,
                'title' => $title
            ]);

            $conversationId = $conversation->id;
        } else {
            $conversation = AiConversation::find($conversationId);
            if ($conversation){
                $conversation->touch();
            }
        }

        AiMessage::create([
            'ai_conversation_id' => $conversationId,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        if ($user && ($user->role === 'teacher' || str_contains(strtolower($user->email), 'teacher'))){
            $systemInstruction = "You are a gamified learning management system AI copilot, a helpful assitant embedded in the gamified learning management system for teachers. Answer professionally, concisely, and supportively. You can use a taglish response if the user asks in taglish.";
        } else {
            $systemInstruction = "You are a gamified learning management system AI copilot, a helpful assitant embedded in the gamified learning management system for and students. Answer professionally, concisely, and supportively. You can use a taglish response if the user asks in taglish.";
        }


        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "System Context: " . $systemInstruction . "\n\nUser Name: " . ($user->name ?? 'User') . "\nQuestion: " . $userMessage]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()){
                $data = $response->json();

                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I didn't get the answer";

                //Save the answer of ai to db
                AiMessage::create([
                    'ai_conversation_id' => $conversationId,
                    'role' => 'assistant',
                    'content' => $reply,
                ]);

                return response()->json(['reply' => $reply]);
            }
            $errorBody = $response->body();

            return response()->json(['reply' => 'There seems to be a problem in your api key' . $errorBody], 500);
        } catch (\Exception $e) {
            return response()->json(['reply' => "I'm sorry, I can't connect to the server right now"], 500);
        }

    }
}
