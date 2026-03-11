<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Voice;
use OpenAI\Client;

class OpenaiTTSService 
{

    public function __construct()
    {
        config(['openai.api_key' => config('services.openai.key')]);
    }


    /**
     * Synthesize text via Azure text to speech 
     *
     * 
     */
    public function synthesizeSpeech(Voice $voice, $text, $format, $file_name)
    {
        $model = ($voice->voice_type == 'standard') ? 'tts-1' : 'tts-1-hd';
        $voice_id = explode('_', $voice->voice_id);

        $client = \OpenAI::client(config('services.openai.key'));

        $audio_stream = $client->audio()->speech([
            'model' => $model,
            'input' => $text,
            'voice' => $voice_id[0],
        ]);

        Storage::disk('audio')->put($file_name, $audio_stream); 

        $data['result_url'] = Storage::url($file_name); 
        $data['name'] = $file_name;
        
        return $data;
    }
}