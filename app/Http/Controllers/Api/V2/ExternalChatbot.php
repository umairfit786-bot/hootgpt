<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\HelperService;
use App\Models\Chatbot;
use App\Models\ChatbotConversation;
use App\Models\ChatbotHistory;
use App\Models\ChatbotEmbedding;
use App\Models\MainSetting;
use App\Models\ChatbotDomain;
use App\Models\User;
use App\Models\ExtensionSetting;
use App\Models\ChatbotPersonalKey;
use Illuminate\Support\Str;

class ExternalChatbot extends Controller
{
    public function chat(Request $request, string $uuid): JsonResponse|StreamedResponse
    {

        $chatbot = Chatbot::where('uuid', $uuid)->first();
        if (!$chatbot) {
            return response()->json(['error' => 'Chatbot not found'], 404);
        }

        if (!$chatbot->active) {
            return response()->json(['error' => 'This chatbot has been deactivated'], 404);
        }

        if ($chatbot) {
            $user = User::where('id', $chatbot->user_id)->first();

            if (($user->tokens + $user->tokens_prepaid) == 0) {
                return response()->json(['error' => 'Not enough credits to proceed, please top up your credits'], 404);
            }
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');
        $history = $request->input('history', []);
        $conversationId = $request->input('conversation_id');
        $userIp = $request->input('user_ip') ?? $request->ip();
        $userDomain = $request->getHost();

        // Create or get conversation
        if (!$conversationId) {
            $conversation = ChatbotConversation::create([
                'user_id' => $chatbot->user_id,
                'chatbot_id' => $chatbot->id,
                'session_id' => session()->getId(),
                'last_activity_at' => now(),
                'ip_address' => $userIp,
                'domain_name' => $userDomain,
                'latest_message' => $message,
            ]);
            $conversationId = $conversation->id;

            $check_domain = ChatbotDomain::where('uuid', $uuid)->where('domain_name', $userDomain)->first();
            if (!$check_domain) {
                ChatbotDomain::create([
                    'uuid' => $uuid,
                    'domain_name' => $userDomain,
                    'chatbot_id' => $chatbot->id,
                    'user_id' => $chatbot->user_id,
                    'ip_address' => $userIp,
                ]);
            }

        } else {
            $conversation = ChatbotConversation::find($conversationId);
            if ($conversation && (!$conversation->ip_address || !$conversation->domain_name)) {
                $conversation->update([
                    'ip_address' => $userIp,
                    'domain_name' => $userDomain
                ]);
            }

            if ($conversation) {
                $conversation->update([
                    'latest_message' => $message,
                ]);
            }
        }

        // Save user message
        $chatbot_history = ChatbotHistory::create([
            'user_id' => $chatbot->user_id,
            'chatbot_id' => $chatbot->id,
            'conversation_id' => $conversationId,
            'model' => $chatbot->model,
            'role' => 'user',
            'prompt' => $message,
        ]);

        // Get relevant embeddings for context
        $contextFromEmbeddings = $this->getRelevantContext($chatbot, $message);

        // Route to appropriate AI service
        $model = 'openai';
        if (in_array($chatbot->model, ['gpt-5.1', 'gpt-5', 'gpt-5-mini', 'gpt-5-nano', 'gpt-5-chat-latest', 'gpt-3.5-turbo-0125', 'gpt-4', 'gpt-4o', 'gpt-4o-mini', 'gpt-4.5-preview', 'o1', 'o1-mini', 'o3-mini', 'gpt-4-0125-preview', 'gpt-4o-search-preview', 'gpt-4o-mini-search-preview', 'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano', 'o4-mini', 'o3'])) {
            $model = 'openai';
        }

        if (in_array($chatbot->model, ['claude-sonnet-4-5', 'claude-haiku-4-5', 'claude-3-7-sonnet-20250219', 'claude-3-opus-20240229', 'claude-3-5-sonnet-20241022', 'claude-3-5-haiku-20241022', 'claude-opus-4-20250514', 'claude-sonnet-4-20250514', 'claude-opus-4-1-20250805', 'claude-opus-4-5-20251101'])) {
            $model = 'anthropic';
        }
        
        if (in_array($chatbot->model, ['gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-2.0-flash', 'gemini-2.5-flash', 'gemini-2.5-pro', 'gemini-2.5-flash-lite-preview-06-17', 'gemini-3-pro-preview'])) {
            $model = 'gemini';
        }

        if (in_array($chatbot->model, ['grok-2-1212', 'grok-2-vision-1212', 'grok-3-latest', 'grok-3-fast-latest', 'grok-3-mini-latest', 'grok-3-mini-fast-latest', 'grok-4-latest', 'grok-4-1-fast-non-reasoning'])) {
            $model = 'xai';
        }

        if (in_array($chatbot->model, ['deepseek-chat', 'deepseek-reasoner'])) {
            $model = 'deepseek';
        }

        // API Keys from environment variables
        $check = ExtensionSetting::first();
        $personalAPI = $check->chatbot_external_personal_api ?? false;
        if ($personalAPI) {
            $personalKeys = ChatbotPersonalKey::where('user_id', $chatbot->user_id)->first();
            if (!$personalKeys) {
                return response()->json(['error' => 'API key missing'], 400);
            }
            if ($model === 'openai' && empty($personalKeys->openai_api)) {
                return response()->json(['error' => 'OpenAI API key missing'], 400);
            }
            if ($model === 'anthropic' && empty($personalKeys->anthropic_api)) {
                return response()->json(['error' => 'Anthropic API key missing'], 400);
            }
            if ($model === 'gemini' && empty($personalKeys->gemini_api)) {
                return response()->json(['error' => 'Gemini API key missing'], 400);
            }
            if ($model === 'xai' && empty($personalKeys->xai_api)) {
                return response()->json(['error' => 'xAI API key missing'], 400);
            }
            if ($model === 'deepseek' && empty($personalKeys->deepseek_api)) {
                return response()->json(['error' => 'Deepseek API key missing'], 400);
            }
            $openaiKey = $personalKeys->openai_api;
            $anthropicKey = $personalKeys->anthropic_api;
            $geminiKey = $personalKeys->gemini_api;
            $xaiKey = $personalKeys->xai_api;
            $deepseekKey = $personalKeys->deepseek_api;
        } else {
            $openaiKey = env('OPENAI_SECRET_KEY');
            $anthropicKey = env('ANTHROPIC_API_KEY');
            $geminiKey = env('GEMINI_API_KEY');
            $settings = MainSetting::first();
            $xaiKey = $settings->xai_api;
            $deepseekKey = $settings->deepseek_api;            
        }


        if ($model === 'openai') {
            return $this->streamOpenAI($message, $history, $chatbot->model, $openaiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings);
        }
        
        if ($model === 'anthropic') {
            return $this->streamAnthropic($message, $history, $chatbot->model, $anthropicKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings);
        }
        
        if ($model === 'gemini') {
            return $this->streamGemini($message, $history, $chatbot->model, $geminiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings);
        }

        if ($model === 'xai') {
            return $this->streamXAI($message, $history, $chatbot->model, $xaiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings);
        }

        if ($model === 'deepseek') {
            return $this->streamDeepSeek($message, $history, $chatbot->model, $deepseekKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings);
        }

    }

    public function models(string $uuid): JsonResponse
    {
        $chatbot = Chatbot::where('uuid', $uuid)->first();
        if (!$chatbot) {
            return response()->json(['error' => 'Chatbot not found'], 404);
        }

        $chatbotData = [
            'chatbot_title' => $chatbot->chatbot_title ?? 'AI Assistant',
            'show_chatbot_name' => $chatbot->hide_chatbot_name ? false : true,
            'message_placeholder' => $chatbot->message_placeholder ?? 'Type your message...',
            'chatbot_name' => $chatbot->chatbot_name ?? 'AI Assistant',
            'greeting_message' => $chatbot->greeting_message ?? 'Hello! How can I help you today?',
            'show_bubble_message' => $chatbot->hide_bubble_message ? false : true, 
            'bubble_message' => $chatbot->bubble_message ?? 'Hey there, How can I help you?',
            'show_ai_avatar' => $chatbot->hide_ai_avatar ? false : true,
            'avatar_logo' => $chatbot->ai_avatar_logo ?? '',
            'show_main_logo' => $chatbot->hide_main_header_logo ? false : true,
            'header_logo' => $chatbot->main_header_logo ?? '',
            'show_footer_brand' => $chatbot->hide_footer_brand ? false : true,
            'footer_link' => $chatbot->footer_link ?? '',
            'show_message_time' => $chatbot->hide_message_time ? false : true,
            'widget_position' => $chatbot->widget_position ?? 'right',
        ];

        $customization = [
            'header_bg_color' => $chatbot->header_bg_color ?? '#1e1e2d',
            'header_text_color' => $chatbot->header_text_color ?? '#FFF',
            'bot_bg_color' =>  $chatbot->ai_bg_color ?? '#F5FAFF',
            'bot_text_color' =>  $chatbot->ai_text_color ?? '#1e1e2d',
            'user_bg_color' => $chatbot->user_bg_color ?? '#1e1e2d',
            'user_text_color' => $chatbot->user_text_color ?? '#FFF',
            'user_bg_color' => $chatbot->user_bg_color ?? '#1e1e2d',
            'user_text_color' => $chatbot->user_text_color ?? '#FFF',
        ];

        return response()->json([
            'chatbot' => $chatbotData,
            'customization' => $customization
        ]);
    }


    private function getRelevantContext($chatbot, $message)
    {
        $embeddings = $chatbot->embeddings()->where('status', 'completed')->get();
        
        if ($embeddings->isEmpty()) {
            return '';
        }

        // Use semantic similarity with embeddings
        $relevantContent = $this->findSimilarContent($embeddings, $message);
        
        if (empty($relevantContent)) {
            // Fallback to keyword matching
            $relevantContent = $this->keywordMatching($embeddings, $message);
        }

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
    
    private function findSimilarContent($embeddings, $message)
    {
        $openaiKey = env('OPENAI_SECRET_KEY');
        if (!$openaiKey) {
            return [];
        }
        
        try {
            // Generate embedding for the user message
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.openai.com/v1/embeddings', [
                'model' => 'text-embedding-3-small',
                'input' => $message
            ]);
            
            if (!$response->successful()) {
                return [];
            }
            
            $messageEmbedding = $response->json()['data'][0]['embedding'];
            $similarities = [];
            
            foreach ($embeddings as $embedding) {
                if (empty($embedding->embedding)) continue;
                
                $embeddingVector = json_decode($embedding->embedding, true);
                if (!$embeddingVector) continue;
                
                $similarity = $this->cosineSimilarity($messageEmbedding, $embeddingVector);
                
                if ($similarity > 0.7) { // Threshold for relevance
                    $similarities[] = [
                        'similarity' => $similarity,
                        'title' => $embedding->title,
                        'content' => $embedding->content,
                        'url' => $embedding->url
                    ];
                }
            }
            
            // Sort by similarity descending
            usort($similarities, function($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });
            
            return $similarities;
            
        } catch (\Exception $e) {
            Log::error('Similarity search error: ' . $e->getMessage());
            return [];
        }
    }
    
    private function keywordMatching($embeddings, $message)
    {
        $relevantContent = [];
        $messageWords = array_filter(explode(' ', strtolower($message)), function($word) {
            return strlen($word) > 3; // Only meaningful words
        });
        
        foreach ($embeddings as $embedding) {
            if (empty($embedding->content)) continue;
            
            $contentWords = explode(' ', strtolower($embedding->content));
            $commonWords = array_intersect($messageWords, $contentWords);
            $score = count($commonWords) / max(count($messageWords), 1);
            
            if ($score > 0.2) { // At least 20% word overlap
                $relevantContent[] = [
                    'score' => $score,
                    'title' => $embedding->title,
                    'content' => substr($embedding->content, 0, 1500),
                    'url' => $embedding->url
                ];
            }
        }
        
        // Sort by score descending
        usort($relevantContent, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return $relevantContent;
    }
    
    private function cosineSimilarity($a, $b)
    {
        if (count($a) !== count($b)) {
            return 0;
        }
        
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;
        
        for ($i = 0; $i < count($a); $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }
        
        $normA = sqrt($normA);
        $normB = sqrt($normB);
        
        if ($normA == 0 || $normB == 0) {
            return 0;
        }
        
        return $dotProduct / ($normA * $normB);
    }

    private function streamOpenAI(string $message, array $history, string $model, string $apiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings = '')
    {
        if (!$apiKey) {
            return response()->json(['error' => 'OpenAI API key not configured']);
        }

        $prompt = '';
        if ($chatbot->instruction_restriction) {
            $prompt = "You must strictly follow the following instructions: " . $chatbot->instructions . ". You cannot answer questions outside of the given instructions.";
            if ($chatbot->fallback_message) {
                $prompt .= " If the user asks a question outside of the given instructions, you must respond with the following message: " . $chatbot->custom_message;
            }
        } else {
            $prompt = $chatbot->instructions;
        }
        
        // Add context from embeddings if available
        if (!empty($contextFromEmbeddings)) {
            $prompt .= "\n\nIMPORTANT: Use the following information to answer user questions when relevant:" . $contextFromEmbeddings;
            $prompt .= "\n\nAlways prioritize information from the content when answering questions. If the content contains relevant information, use it in your response.";
        }

        $messages = [];
        $messages[] = [
                'role' => 'system',
                'content' => $prompt
            ];
        foreach ($history as $msg) {
            if(isset($msg['role'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            } else {
                $messages[] = ['role' => 'user', 'content' => $msg['prompt']];
                if (!empty($chat['response'])) {
                    $messages[] = ['role' => 'assistant', 'content' => $msg['response']];
                }
            }
            
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        return response()->stream(function () use ($messages, $model, $apiKey, $chatbot, $conversationId, $chatbot_history) {
            // Send conversation_id first
            echo "data: " . json_encode(['conversation_id' => $conversationId]) . "\n\n";
            ob_flush();
            flush();
            
            $fullResponse = '';
            
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => $model,
                    'messages' => $messages,
                    'stream' => true
                ]),
                CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$fullResponse, &$inputTokens, &$outputTokens) {
                    $lines = explode("\n", $data);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (strpos($line, 'data: ') === 0) {
                            $json = trim(substr($line, 6));
                            if ($json === '[DONE]') {
                                echo "data: " . json_encode(['done' => true]) . "\n\n";
                                ob_flush();
                                flush();
                                continue;
                            }
                            
                            $decoded = json_decode($json, true);
                            if ($decoded && isset($decoded['choices'][0]['delta']['content']) && $decoded['choices'][0]['delta']['content'] !== '') {
                                $content = $decoded['choices'][0]['delta']['content'];
                                $fullResponse .= $content;
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                ob_flush();
                                flush();
                            }                          

                        }
                    }
                    return strlen($data);
                }
            ]);
            
            curl_exec($ch);
            curl_close($ch);
            
            // Save the complete response to database
            $wordCount = str_word_count($fullResponse);
            $inputTokens = intval(strlen(json_encode($messages)) / 4); // Rough estimation: 4 chars = 1 token
            $outputTokens = intval(strlen($fullResponse) / 4);

            HelperService::updateBalance($wordCount, $model, $inputTokens, $outputTokens, $chatbot_history->user_id);
            
            $chatbot_history->update([
                'response' => $fullResponse,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'words' => $wordCount
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }
    

    private function streamAnthropic(string $message, array $history, string $model, string $apiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings = '')
    {
        if (!$apiKey) {
            return response()->json(['error' => 'Anthropic API key not configured']);
        }

        $system_prompt = '';
        if ($chatbot->instruction_restriction) {
            $system_prompt = "You must strictly follow the following instructions: " . $chatbot->instructions . ". You cannot answer questions outside of the given instructions.";
            if ($chatbot->fallback_message) {
                $system_prompt .= " If the user asks a question outside of the given instructions, you must respond with the following message: " . $chatbot->custom_message;
            }
        } else {
            $system_prompt = $chatbot->instructions;
        }
        
        // Add context from embeddings if available
        if (!empty($contextFromEmbeddings)) {
            $system_prompt .= "\n\nIMPORTANT: Use the following information to answer user questions when relevant:" . $contextFromEmbeddings;
            $system_prompt .= "\n\nAlways prioritize information from the content when answering questions. If the content contains relevant information, use it in your response.";
        }

        $messages = [];
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        return response()->stream(function () use ($messages, $model, $apiKey, $chatbot, $conversationId, $chatbot_history, $system_prompt) {
            // Send conversation_id first
            echo "data: " . json_encode(['conversation_id' => $conversationId]) . "\n\n";
            ob_flush();
            flush();
            
            $fullResponse = '';
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.anthropic.com/v1/messages',
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                    'anthropic-version: 2023-06-01'
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => $model,
                    'max_tokens' => 1000,
                    'system' => $system_prompt,
                    'messages' => $messages,
                    'stream' => true
                ]),
                CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$fullResponse) {
                    $lines = explode("\n", $data);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (strpos($line, 'data: ') === 0) {
                            $json = trim(substr($line, 6));
                            if ($json === '[DONE]') {
                                echo "data: " . json_encode(['done' => true]) . "\n\n";
                                ob_flush();
                                flush();
                                continue;
                            }
                            
                            $decoded = json_decode($json, true);
                            if ($decoded && isset($decoded['delta']['text']) && $decoded['delta']['text'] !== '') {
                                $content = $decoded['delta']['text'];
                                $fullResponse .= $content;
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                ob_flush();
                                flush();
                            }
                        }
                    }
                    return strlen($data);
                }
            ]);
            
            curl_exec($ch);
            curl_close($ch);
            
            // Save the complete response to database
            $wordCount = str_word_count($fullResponse);
            $inputTokens = intval(strlen(json_encode($messages)) / 4);
            $outputTokens = intval(strlen($fullResponse) / 4);

            HelperService::updateBalance($wordCount, $model, $inputTokens, $outputTokens, $chatbot_history->user_id);
            
            $chatbot_history->update([
                'response' => $fullResponse,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'words' => $wordCount
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }


    private function streamGemini(string $message, array $history, string $model, string $apiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings = '')
    {
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API key not configured']);
        }
        Log::info('Gemini API call', ['model' => $model, 'api_key_set' => !empty($apiKey)]);
        $system_instruction = '';
        if ($chatbot->instruction_restriction) {
            $system_instruction = "You must strictly follow the following instructions: " . $chatbot->instructions . ". You cannot answer questions outside of the given instructions.";
            if ($chatbot->fallback_message) {
                $system_instruction .= " If the user asks a question outside of the given instructions, you must respond with the following message: " . $chatbot->custom_message;
            }
        } else {
            $system_instruction = $chatbot->instructions;
        }
        
        // Add context from embeddings if available
        if (!empty($contextFromEmbeddings)) {
            $system_instruction .= "\n\nIMPORTANT: Use the following information to answer user questions when relevant:" . $contextFromEmbeddings;
            $system_instruction .= "\n\nAlways prioritize information from the content when answering questions. If the content contains relevant information, use it in your response.";
        }

        $contents = [];
        foreach ($history as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]]
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        return response()->stream(function () use ($contents, $model, $apiKey, $chatbot, $conversationId, $chatbot_history, $system_instruction) {
            // Send conversation_id first
            echo "data: " . json_encode(['conversation_id' => $conversationId]) . "\n\n";
            ob_flush();
            flush();
            
            $fullResponse = '';
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':streamGenerateContent?key=' . $apiKey,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'contents' => $contents,
                    'systemInstruction' => ['parts' => [['text' => $system_instruction]]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1000
                    ]
                ]),
                CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$fullResponse) {
                    $lines = explode("\n", $data);
                    foreach ($lines as $line) {
                        $line = trim($line);

                        if (!empty($line) && strpos($line, '{') === 0) {
                            $decoded = json_decode($line, true);
                            if ($decoded) {
                                // Handle error responses
                                if (isset($decoded['error'])) {
                                    echo "data: " . json_encode(['error' => $decoded['error']['message'] ?? 'Gemini API Error']) . "\n\n";
                                    ob_flush();
                                    flush();
                                    return strlen($data);
                                }
                                // Handle successful streaming response
                                if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
                                    $content = $decoded['candidates'][0]['content']['parts'][0]['text'];
                                    $fullResponse .= $content;
                                    echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                    ob_flush();
                                    flush();
                                }
                            }
                        }
                    }
                    return strlen($data);
                }
            ]);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            // Handle cURL errors
            if ($result === false || !empty($error)) {
                echo "data: " . json_encode(['error' => 'Gemini connection error: ' . $error]) . "\n\n";
                ob_flush();
                flush();
            } elseif ($httpCode >= 400) {
                echo "data: " . json_encode(['error' => 'Gemini API returned HTTP ' . $httpCode]) . "\n\n";
                ob_flush();
                flush();
            }
            
            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
            
            // Save the complete response to database
            $wordCount = str_word_count($fullResponse);
            $inputTokens = intval(strlen(json_encode($contents)) / 4);
            $outputTokens = intval(strlen($fullResponse) / 4);

            HelperService::updateBalance($wordCount, $model, $inputTokens, $outputTokens, $chatbot_history->user_id);
            
            $chatbot_history->update([
                'response' => $fullResponse,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'words' => $wordCount
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }


    private function streamXAI(string $message, array $history, string $model, string $apiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings = '')
    {
        if (!$apiKey) {
            return response()->json(['error' => 'xAI API key not configured']);
        }

        $system_prompt = '';
        if ($chatbot->instruction_restriction) {
            $system_prompt = "You must strictly follow the following instructions: " . $chatbot->instructions . ". You cannot answer questions outside of the given instructions.";
            if ($chatbot->fallback_message) {
                $system_prompt .= " If the user asks a question outside of the given instructions, you must respond with the following message: " . $chatbot->custom_message;
            }
        } else {
            $system_prompt = $chatbot->instructions;
        }
        
        // Add context from embeddings if available
        if (!empty($contextFromEmbeddings)) {
            $system_prompt .= "\n\nIMPORTANT: Use the following information to answer user questions when relevant:" . $contextFromEmbeddings;
            $system_prompt .= "\n\nAlways prioritize information from the content when answering questions. If the content contains relevant information, use it in your response.";
        }

        $messages = [];
        if ($system_prompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $system_prompt
            ];
        }
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        return response()->stream(function () use ($messages, $model, $apiKey, $chatbot, $conversationId, $chatbot_history) {
            // Send conversation_id first
            echo "data: " . json_encode(['conversation_id' => $conversationId]) . "\n\n";
            ob_flush();
            flush();
            
            $fullResponse = '';
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.x.ai/v1/chat/completions',
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                    'stream' => true
                ]),
                CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$fullResponse) {
                    $lines = explode("\n", $data);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (strpos($line, 'data: ') === 0) {
                            $json = trim(substr($line, 6));
                            if ($json === '[DONE]') {
                                echo "data: " . json_encode(['done' => true]) . "\n\n";
                                ob_flush();
                                flush();
                                continue;
                            }
                            
                            $decoded = json_decode($json, true);
                            if ($decoded && isset($decoded['choices'][0]['delta']['content']) && $decoded['choices'][0]['delta']['content'] !== '') {
                                $content = $decoded['choices'][0]['delta']['content'];
                                $fullResponse .= $content;
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                ob_flush();
                                flush();
                            }
                        }
                    }
                    return strlen($data);
                }
            ]);
            
            curl_exec($ch);
            curl_close($ch);
            
            // Save the complete response to database
            $wordCount = str_word_count($fullResponse);
            $inputTokens = intval(strlen(json_encode($messages)) / 4);
            $outputTokens = intval(strlen($fullResponse) / 4);

            HelperService::updateBalance($wordCount, $model, $inputTokens, $outputTokens, $chatbot_history->user_id);
            
            $chatbot_history->update([
                'response' => $fullResponse,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'words' => $wordCount
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }


    private function streamDeepSeek(string $message, array $history, string $model, string $apiKey, $chatbot, $conversationId, $chatbot_history, $contextFromEmbeddings = '')
    {
        if (!$apiKey) {
            return response()->json(['error' => 'DeepSeek API key not configured']);
        }

        $system_prompt = '';
        if ($chatbot->instruction_restriction) {
            $system_prompt = "You must strictly follow the following instructions: " . $chatbot->instructions . ". You cannot answer questions outside of the given instructions.";
            if ($chatbot->fallback_message) {
                $system_prompt .= " If the user asks a question outside of the given instructions, you must respond with the following message: " . $chatbot->custom_message;
            }
        } else {
            $system_prompt = $chatbot->instructions;
        }
        
        // Add context from embeddings if available
        if (!empty($contextFromEmbeddings)) {
            $system_prompt .= "\n\nIMPORTANT: Use the following information to answer user questions when relevant:" . $contextFromEmbeddings;
            $system_prompt .= "\n\nAlways prioritize information from the content when answering questions. If the content contains relevant information, use it in your response.";
        }

        $messages = [];
        if ($system_prompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $system_prompt
            ];
        }
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        return response()->stream(function () use ($messages, $model, $apiKey, $chatbot, $conversationId, $chatbot_history) {
            // Send conversation_id first
            echo "data: " . json_encode(['conversation_id' => $conversationId]) . "\n\n";
            ob_flush();
            flush();
            
            $fullResponse = '';
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.deepseek.com/v1/chat/completions',
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                    'stream' => true
                ]),
                CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$fullResponse) {
                    $lines = explode("\n", $data);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (strpos($line, 'data: ') === 0) {
                            $json = trim(substr($line, 6));
                            if ($json === '[DONE]') {
                                echo "data: " . json_encode(['done' => true]) . "\n\n";
                                ob_flush();
                                flush();
                                continue;
                            }
                            
                            $decoded = json_decode($json, true);
                            if ($decoded && isset($decoded['choices'][0]['delta']['content']) && $decoded['choices'][0]['delta']['content'] !== '') {
                                $content = $decoded['choices'][0]['delta']['content'];
                                $fullResponse .= $content;
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                ob_flush();
                                flush();
                            }
                        }
                    }
                    return strlen($data);
                }
            ]);
            
            curl_exec($ch);
            curl_close($ch);
            
            // Save the complete response to database
            $wordCount = str_word_count($fullResponse);
            $inputTokens = intval(strlen(json_encode($messages)) / 4);
            $outputTokens = intval(strlen($fullResponse) / 4);

            HelperService::updateBalance($wordCount, $model, $inputTokens, $outputTokens, $chatbot_history->user_id);
            
            $chatbot_history->update([
                'response' => $fullResponse,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'words' => $wordCount
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    public function conversations(Request $request, string $uuid): JsonResponse
    {
        $chatbot = Chatbot::where('uuid', $uuid)->first();
        if (!$chatbot) {
            return response()->json(['error' => 'Chatbot not found'], 404);
        }

        $request->validate([
            'user_ip' => 'required|string'
        ]);

        $userIp = $request->input('user_ip');
        
        $conversations = ChatbotConversation::where('chatbot_id', $chatbot->id)
            ->where('ip_address', $userIp)
            ->orderBy('last_activity_at', 'desc')
            ->get()
            ->map(function ($conversation) {                    
                // Get message count
                $messageCount = ChatbotHistory::where('conversation_id', $conversation->id)
                    ->count();
                    
                return [
                    'id' => $conversation->id,
                    'latest_response' => Str::limit($conversation->latest_message, 60),
                    'message_count' => $messageCount,
                    'last_activity_at' => $conversation->last_activity_at
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }


    public function createConversation(Request $request, string $uuid): JsonResponse
    {
        $chatbot = Chatbot::where('uuid', $uuid)->first();
        if (!$chatbot) {
            return response()->json(['error' => 'Chatbot not found'], 404);
        }

        $conversation = ChatbotConversation::create([
            'user_id' => $chatbot->user_id,
            'chatbot_id' => $chatbot->id,
            'session_id' => session()->getId(),
            'last_activity_at' => now(),
            'ip_address' => $request->input('user_ip'),
            'domain_name' => $request->getHost(),
        ]);

        return response()->json(['conversation' => $conversation]);
    }
    

    public function getConversation(Request $request, string $uuid, int $conversationId): JsonResponse
    {
        $chatbot = Chatbot::where('uuid', $uuid)->first();
        if (!$chatbot) {
            return response()->json(['error' => 'Chatbot not found'], 404);
        }
        
        $conversation = ChatbotConversation::where('id', $conversationId)
            ->where('chatbot_id', $chatbot->id)
            ->first();
            
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }
        
        $messages = ChatbotHistory::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'prompt' => $message->prompt,
                    'response' => $message->response,
                    'created_at' => $message->created_at
                ];
            });
            
        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

}