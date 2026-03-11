<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use App\Models\ExtensionSetting;

class FalAI
{

    public static function generate($prompt, $model = 'flux-pro/new', $size)
    {
        $setting = ExtensionSetting::first();

        $model = 'fal-ai/' . $model;

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Key ' . $setting->flux_api,
        ])->post('https://queue.fal.run/' . $model, [
            'prompt' => $prompt,
            'image_size' => $size,
        ]);

        if ($http->status() == 200) {
            $data['status'] = 'success';
            $data['request_id'] = $http->json('request_id');
            return $data;
        } else {
            $data['status'] = 'error';
            $data['message'] = $http->json();
            return $data;
        }

    }
    

    public static function status($id, $model = 'flux')
    {
        $setting = ExtensionSetting::first();

        if ($model == 'flux-pro/new') {
            $url = 'https://queue.fal.run/fal-ai/flux-pro/requests/' . $id . '/status'; 
        } elseif ($model == 'flux-realism') {
            $url = 'https://queue.fal.run/fal-ai/flux-realism/requests/' . $id . '/status';
        } else {
            $url = 'https://queue.fal.run/fal-ai/flux/requests/' . $id . '/status';
        }
        

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Key ' . $setting->flux_api,
        ])->get($url);

        return $http->json('status');
    }


    public static function get($id, $model = 'flux')
    {
        $setting = ExtensionSetting::first();

        if ($model == 'flux-pro/new') {
            $url = 'https://queue.fal.run/fal-ai/flux-pro/requests/' . $id;
        } elseif ($model == 'flux-realism') {
            $url = 'https://queue.fal.run/fal-ai/flux-realism/requests/' . $id;
        } else {
            $url = 'https://queue.fal.run/fal-ai/flux/requests/' . $id;
        }
        

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Key ' . $setting->flux_api,
        ])->get($url);

        if ($images = $http->json('images')) {
            if (is_array($images)) {
                $image = Arr::first($images);

                return ['image' => $image];
            }
        }

        return false;
    }
}
