<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use App\Models\ExtensionSetting;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class Clipdrop
{

    public static function generate($prompt)
    {
        $setting = ExtensionSetting::first();


        $headers = [
            'x-api-key' => $setting->clipdrop_api
        ];
        
        $body = [
            "prompt" => $prompt,
        ];
        
        $response = Http::withHeaders($headers)
                        ->post('https://clipdrop-api.co/text-to-image/v1', $body);

        if ($response) {
            $data['status'] = 'success';
            $data['result'] = $response;
            return $data;
        } else {
            $data['status'] = 'error';
            $data['message'] = __('There was an error with image generation');
            return $data;
        }

    }
    
}
