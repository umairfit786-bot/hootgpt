<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\HelperService;
use App\Models\Chatbot;
use App\Models\ChatbotConversation;
use App\Models\ChatbotHistory;
use App\Models\MainSetting;
use App\Models\User;

class TelegramChatbot extends Controller
{
    public function processMessage($message, $chatbot, $userIp = null)
    {
        if (!$chatbot->active) {
            return 'This chatbot has been deactivated';
        }

        $user = User::where('id', $chatbot->user_id)->first();
        if (($user->tokens + $user->tokens_prepaid) == 0) {
            return 'Not enough credits to proceed, please top up your credits';
        }

        // Create conversation
        $conversation = ChatbotConversation::create([
            'user_id' => $chatbot->user_id,
            'chatbot_id' => $chatbot->id,
            'session_id' => session()->getId(),
            'last_activity_at' => now(),
            'ip_address' => $userIp ?? request()->ip(),
            'latest_message' => $message,
        ]);

        // Save user message
        $chatbot_history = ChatbotHistory::create([
            'user_id' => $chatbot->user_id,
            'chatbot_id' => $chatbot->id,
            'conversation_id' => $conversation->id,
            'model' => $chatbot->model,
            'role' => 'user',
            'prompt' => $message,
        ]);

        // Get relevant embeddings for context
        $contextFromEmbeddings = $this->getRelevantContext($chatbot, $message);

        // Route to appropriate AI service
        $model = $this->getModelProvider($chatbot->model);
        
        $response = match($model) {
            'openai' => $this->callOpenAI($message, $chatbot, $contextFromEmbeddings),
            'anthropic' => $this->callAnthropic($message, $chatbot, $contextFromEmbeddings),
            'gemini' => $this->callGemini($message, $chatbot, $contextFromEmbeddings),
            'xai' => $this->callXAI($message, $chatbot, $contextFromEmbeddings),
            'deepseek' => $this->callDeepSeek($message, $chatbot, $contextFromEmbeddings),
            default => 'Model not supported'
        };

        // Save response and update tokens
        $wordCount = str_word_count($response);
        $inputTokens = intval(strlen($message) / 4);
        $outputTokens = intval(strlen($response) / 4);

        HelperService::updateBalance($wordCount, $chatbot->model, $inputTokens, $outputTokens, $chatbot->user_id);
        
        $chatbot_history->update([
            'response' => $response,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'words' => $wordCount
        ]);

        return $response;
    }

    private function getModelProvider($model)
    {
        if (in_array($model, ['gpt-3.5-turbo-0125', 'gpt-4', 'gpt-4o', 'gpt-4o-mini', 'gpt-4.5-preview', 'o1', 'o1-mini', 'o3-mini', 'gpt-4-0125-preview', 'gpt-4o-search-preview', 'gpt-4o-mini-search-preview', 'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano', 'o4-mini', 'o3'])) {
            return 'openai';
        }
        if (in_array($model, ['claude-3-7-sonnet-20250219', 'claude-3-opus-20240229', 'claude-3-5-sonnet-20241022', 'claude-3-5-haiku-20241022'])) {
            return 'anthropic';
        }
        if (in_array($model, ['gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-2.0-flash'])) {
            return 'gemini';
        }
        if (in_array($model, ['grok-2-1212'])) {
            return 'xai';
        }
        return 'openai'; // default
    }

    private function buildPrompt($chatbot, $contextFromEmbeddings = '')
    {
        $prompt = '';
        if ($chatbot->instruction_restriction) {
            $prompt = "You must strictly follow the following instructions: " . $chatbot->instructions . ". You cannot answer questions outside of the given instructions.";
            if ($chatbot->fallback_message) {
                $prompt .= " If the user asks a question outside of the given instructions, you must respond with the following message: " . $chatbot->custom_message;
            }
        } else {
            $prompt = $chatbot->instructions;
        }
        
        if (!empty($contextFromEmbeddings)) {
            $prompt .= "\n\nIMPORTANT: Use the following information to answer user questions when relevant:" . $contextFromEmbeddings;
            $prompt .= "\n\nAlways prioritize information from the content when answering questions. If the content contains relevant information, use it in your response.";
        }

        return $prompt;
    }

    private function callOpenAI($message, $chatbot, $contextFromEmbeddings = '')
    {
        $apiKey = env('OPENAI_SECRET_KEY');
        if (!$apiKey) return 'OpenAI API key not configured';

        $prompt = $this->buildPrompt($chatbot, $contextFromEmbeddings);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $chatbot->model,
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $message]
            ],
            'max_tokens' => 1000
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'];
        }

        return 'Sorry, I couldn\'t process your request right now.';
    }

    private function callAnthropic($message, $chatbot, $contextFromEmbeddings = '')
    {
        $apiKey = env('ANTHROPIC_API_KEY');
        if (!$apiKey) return 'Anthropic API key not configured';

        $systemPrompt = $this->buildPrompt($chatbot, $contextFromEmbeddings);

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01'
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $chatbot->model,
            'max_tokens' => 1000,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $message]
            ]
        ]);

        if ($response->successful()) {
            return $response->json()['content'][0]['text'];
        }

        return 'Sorry, I couldn\'t process your request right now.';
    }

    private function callGemini($message, $chatbot, $contextFromEmbeddings = '')
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) return 'Gemini API key not configured';

        $systemInstruction = $this->buildPrompt($chatbot, $contextFromEmbeddings);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/' . $chatbot->model . ':generateContent?key=' . $apiKey, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $message]]
                ]
            ],
            'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1000
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated';
        }

        return 'Sorry, I couldn\'t process your request right now.';
    }

    private function callXAI($message, $chatbot, $contextFromEmbeddings = '')
    {
        $settings = MainSetting::first();
        $apiKey = $settings->xai_api;
        if (!$apiKey) return 'xAI API key not configured';

        $prompt = $this->buildPrompt($chatbot, $contextFromEmbeddings);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.x.ai/v1/chat/completions', [
            'model' => $chatbot->model,
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $message]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'];
        }

        return 'Sorry, I couldn\'t process your request right now.';
    }

    private function callDeepSeek($message, $chatbot, $contextFromEmbeddings = '')
    {
        $settings = MainSetting::first();
        $apiKey = $settings->deepseek_api;
        if (!$apiKey) return 'DeepSeek API key not configured';

        $prompt = $this->buildPrompt($chatbot, $contextFromEmbeddings);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.deepseek.com/v1/chat/completions', [
            'model' => $chatbot->model,
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $message]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'];
        }

        return 'Sorry, I couldn\'t process your request right now.';
    }

    private function getRelevantContext($chatbot, $message)
    {
        $embeddings = $chatbot->embeddings()->where('status', 'completed')->get();
        
        if ($embeddings->isEmpty()) {
            return '';
        }

        $relevantContent = $this->keywordMatching($embeddings, $message);

        if (empty($relevantContent)) {
            return '';
        }

        $context = "\n\nBased on the available content, here is relevant information:\n";
        foreach (array_slice($relevantContent, 0, 3) as $content) {
            $context .= "\n--- {$content['title']} ---\n{$content['content']}\n";
        }
        $context .= "\nPlease use this information to answer the user's question accurately.\n";
        
        return $context;
    }
    
    private function keywordMatching($embeddings, $message)
    {
        $relevantContent = [];
        $messageWords = array_filter(explode(' ', strtolower($message)), function($word) {
            return strlen($word) > 3;
        });
        
        foreach ($embeddings as $embedding) {
            if (empty($embedding->content)) continue;
            
            $contentWords = explode(' ', strtolower($embedding->content));
            $commonWords = array_intersect($messageWords, $contentWords);
            $score = count($commonWords) / max(count($messageWords), 1);
            
            if ($score > 0.2) {
                $relevantContent[] = [
                    'score' => $score,
                    'title' => $embedding->title,
                    'content' => substr($embedding->content, 0, 1500),
                    'url' => $embedding->url
                ];
            }
        }
        
        usort($relevantContent, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return $relevantContent;
    }
}