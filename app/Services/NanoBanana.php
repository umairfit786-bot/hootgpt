<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use App\Models\ExtensionSetting;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NanoBanana
{

    public static function generate($prompt, $model)
    {
        $setting = ExtensionSetting::first();
        $apiKey = $setting->google_nano_banana_api;
        
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseModalities' => ['image']
            ]
        ];

        $client = new \GuzzleHttp\Client([
            'timeout' => 120,
            'http_errors' => false
        ]);
        
        $apiEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
  
        $response = $client->post($apiEndpoint, [
            'json' => $payload,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
   
        $statusCode = $response->getStatusCode();
        \Log::info($statusCode);
        if ($statusCode !== 200) {
            $errorBody = $response->getBody()->getContents();
            $errorData = json_decode($errorBody, true);
            
            $errorMessage = isset($errorData['error']['message']) 
                ? $errorData['error']['message'] 
                : "Gemini API Error: HTTP Status $statusCode";
            
            Log::error("Gemini API Error: $errorMessage", [
                'status_code' => $statusCode,
                'error_data' => $errorData
            ]);
            
            return [
                'status' => 'error',
                'message' => $errorMessage
            ];
        }

        $responseBody = json_decode($response->getBody()->getContents(), true);
        \Log::info('Gemini Response', $responseBody);
        if (isset($responseBody['candidates'][0]['content']['parts'][0]['inlineData']['data'])) {
            return [
                'status' => 'success',
                'result' => $responseBody['candidates'][0]['content']['parts'][0]['inlineData']['data']
            ];
        }

        return [
            'status' => 'error',
            'message' => __('There was an error with image generation')
        ];
    }
    
}
