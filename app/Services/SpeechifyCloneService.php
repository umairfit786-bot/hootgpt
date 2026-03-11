<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\ExtensionSetting;
use App\Models\CustomVoice;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SpeechifyCloneService 
{
    private $apiKey;
    private $baseUrl = 'https://api.sws.speechify.com';
    private $client;
    
    public function __construct()
    {
        $this->client = new Client();
    }
    
    /**
     * Get API key (lazy loaded)
     */
    private function getApiKey()
    {
        if ($this->apiKey === null) {
            $setting = ExtensionSetting::first();
            $this->apiKey = $setting->speechify_clone_api ?? null;
        }
        return $this->apiKey;
    }

    /**
     * Synthesize text via Speechify text to speech API
     */
    public function synthesizeSpeech(CustomVoice $voice, $text, $format, $file_name)
    {  
        if (!$this->getApiKey()) {
            throw new Exception('Speechify API key not configured');
        }

        $payload = [
            'input' => $text,
            'voice_id' => $voice->voice_id,
            'audio_format' => $format,
            'model' => 'simba-multilingual',
        ];

        try {
            $response = $this->client->request('POST', $this->baseUrl . '/v1/audio/speech', [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (!$result || !isset($result['audio_data'])) {
                throw new Exception('Invalid API response: missing audio data');
            }

            Storage::disk('audio')->put($file_name, base64_decode($result['audio_data'])); 

            return [
                'result_url' => Storage::url($file_name),
                'name' => $file_name
            ];
        } catch (RequestException $e) {
            Log::error('Speechify TTS Error: ' . $e->getMessage());
            throw new Exception('Failed to synthesize speech: ' . $e->getMessage());
        }
    }

    public function createVoiceClone(UploadedFile $audioFile, string $voiceName, ?string $description = null, array $consentData = [], array $options = [])
{
    if (!$this->getApiKey()) {
        throw new Exception('Speechify API key not configured');
    }

    // Validate audio file
    $this->validateAudioFile($audioFile);

    // Required: Extract or default gender (e.g., from options or prompt user)
    $gender = $options['gender'] ?? 'male';

    // Required: Consent
    if (!$consentData || !isset($consentData['fullName']) || !isset($consentData['email'])) {
        throw new Exception('Consent data (fullName and email) is required');
    }
    $consent = json_encode($consentData);

    // Optional: Locale (map from options['language'] if provided)
    $locale = $options['locale'] ?? ($options['language'] ?? 'en-US');

    try {
        $multipart = [
            [
                'name' => 'name',
                'contents' => $voiceName
            ],
            [
                'name' => 'gender',  // ADD THIS
                'contents' => $gender
            ],
            [
                'name' => 'consent',  // ADD THIS
                'contents' => $consent
            ],
            [
                'name' => 'sample',  // CHANGE FROM 'file'
                'contents' => fopen($audioFile->getPathname(), 'r'),
                'filename' => $audioFile->getClientOriginalName(),
                'headers' => ['Content-Type' => $audioFile->getMimeType()]
            ]
            // OMIT description (unsupported)
        ];

        $response = $this->client->request('POST', $this->baseUrl . '/v1/voices', [
            'multipart' => $multipart,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getApiKey(),
            ]
        ]);

        $result = json_decode($response->getBody(), true);
            if (!$result) {
                throw new Exception('Invalid JSON response from API');
            }
        
            Log::info('Voice clone created successfully', ['voice_id' => $result['id'] ?? 'unknown']);
            
            return $result;
        } catch (RequestException $e) {
            $errorMessage = $this->parseErrorMessage($e);
            Log::error('Speechify Voice Clone Creation Error: ' . $errorMessage);
            throw new Exception('Failed to create voice clone: ' . $errorMessage);
        }
}

    /**
     * Get voice clone status
     * 
     * @param string $voiceId
     * @return array
     */
    public function getVoiceStatus(string $voiceId)
    {
        if (!$this->getApiKey()) {
            throw new Exception('Speechify API key not configured');
        }

        try {
            $response = $this->client->request('GET', $this->baseUrl . '/v1/voices/' . urlencode($voiceId), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            if (!$result) {
                throw new Exception('Invalid JSON response from API');
            }
            
            return $result;
        } catch (RequestException $e) {
            $errorMessage = $this->parseErrorMessage($e);
            Log::error('Speechify Get Voice Status Error: ' . $errorMessage);
            throw new Exception('Failed to get voice status: ' . $errorMessage);
        }
    }

    /**
     * List all custom voices
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listCustomVoices(int $limit = 50, int $offset = 0)
    {
        if (!$this->getApiKey()) {
            throw new Exception('Speechify API key not configured');
        }

        try {
            $response = $this->client->request('GET', $this->baseUrl . '/v1/voices', [
                'query' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'type' => 'custom'
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            if (!$result) {
                throw new Exception('Invalid JSON response from API');
            }
            
            return $result;
        } catch (RequestException $e) {
            $errorMessage = $this->parseErrorMessage($e);
            Log::error('Speechify List Voices Error: ' . $errorMessage);
            throw new Exception('Failed to list voices: ' . $errorMessage);
        }
    }

    /**
     * Delete a custom voice
     * 
     * @param string $voiceId
     * @return bool
     */
    public function deleteVoice(string $voiceId)
    {
        if (!$this->getApiKey()) {
            throw new Exception('Speechify API key not configured');
        }

        try {
            $response = $this->client->request('DELETE', $this->baseUrl . '/v1/voices/' . urlencode($voiceId), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                ]
            ]);

            Log::info('Voice deleted successfully', ['voice_id' => $voiceId]);
            return $response->getStatusCode() === 204;
        } catch (RequestException $e) {
            $errorMessage = $this->parseErrorMessage($e);
            Log::error('Speechify Delete Voice Error: ' . $errorMessage);
            throw new Exception('Failed to delete voice: ' . $errorMessage);
        }
    }

    /**
     * Update voice metadata
     * 
     * @param string $voiceId
     * @param array $data
     * @return array
     */
    public function updateVoice(string $voiceId, array $data)
    {
        if (!$this->getApiKey()) {
            throw new Exception('Speechify API key not configured');
        }

        try {
            $response = $this->client->request('PATCH', $this->baseUrl . '/v1/voices/' . urlencode($voiceId), [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                    'Content-Type' => 'application/json',
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            if (!$result) {
                throw new Exception('Invalid JSON response from API');
            }
            
            Log::info('Voice updated successfully', ['voice_id' => $voiceId]);
            return $result;
        } catch (RequestException $e) {
            $errorMessage = $this->parseErrorMessage($e);
            Log::error('Speechify Update Voice Error: ' . $errorMessage);
            throw new Exception('Failed to update voice: ' . $errorMessage);
        }
    }

    /**
     * Create voice clone from multiple audio files
     * 
     * @param array $audioFiles Array of UploadedFile objects
     * @param string $voiceName
     * @param string|null $description
     * @param array $options
     * @return array
     */
    public function createVoiceCloneFromMultipleFiles(array $audioFiles, string $voiceName, ?string $description = null, array $consentData = [], array $options = [])
    {
        if (!$this->getApiKey()) {
            throw new Exception('Speechify API key not configured');
        }

        if (empty($audioFiles)) {
            throw new Exception('At least one audio file is required');
        }

        // Validate all audio files
        foreach ($audioFiles as $audioFile) {
            $this->validateAudioFile($audioFile);
        }

        // Required: Consent
        if (!$consentData || !isset($consentData['fullName']) || !isset($consentData['email'])) {
            throw new Exception('Consent data (fullName and email) is required');
        }

        try {
            $multipart = [
                [
                    'name' => 'name',
                    'contents' => $voiceName
                ],
                [
                    'name' => 'gender',
                    'contents' => $options['gender'] ?? 'male'
                ],
                [
                    'name' => 'consent',
                    'contents' => json_encode($consentData)
                ]
            ];

            // Add all audio files with proper file handle management
            $fileHandles = [];
            foreach ($audioFiles as $index => $audioFile) {
                $fileHandle = fopen($audioFile->getPathname(), 'r');
                if (!$fileHandle) {
                    // Close any previously opened handles
                    foreach ($fileHandles as $handle) {
                        fclose($handle);
                    }
                    throw new Exception('Unable to open audio file: ' . $audioFile->getClientOriginalName());
                }
                $fileHandles[] = $fileHandle;
                
                $multipart[] = [
                    'name' => 'files',
                    'contents' => $fileHandle,
                    'filename' => $audioFile->getClientOriginalName()
                ];
            }

            // Add optional parameters
            if ($description) {
                $multipart[] = [
                    'name' => 'description',
                    'contents' => $description
                ];
            }

            if (isset($options['language'])) {
                $multipart[] = [
                    'name' => 'language',
                    'contents' => $options['language']
                ];
            }

            if (isset($options['gender'])) {
                $multipart[] = [
                    'name' => 'gender',
                    'contents' => $options['gender']
                ];
            }

            $response = $this->client->request('POST', $this->baseUrl . '/v1/voices', [
                'multipart' => $multipart,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (!$result) {
                throw new Exception('Invalid JSON response from API');
            }
            
            Log::info('Voice clone created from multiple files', [
                'voice_id' => $result['id'] ?? 'unknown',
                'file_count' => count($audioFiles)
            ]);
            
            return $result;
        } catch (RequestException $e) {
            $errorMessage = $this->parseErrorMessage($e);
            Log::error('Speechify Voice Clone Creation Error2: ' . $errorMessage);
            throw new Exception('Failed to create voice clone: ' . $errorMessage);
        }
    }

    /**
     * Get voice clone training progress
     * 
     * @param string $voiceId
     * @return array
     */
    public function getTrainingProgress(string $voiceId)
    {
        try {
            $voiceData = $this->getVoiceStatus($voiceId);
            
            return [
                'status' => $voiceData['status'] ?? 'unknown',
                'progress' => $voiceData['training_progress'] ?? 0,
                'estimated_completion' => $voiceData['estimated_completion'] ?? null,
                'created_at' => $voiceData['created_at'] ?? null,
                'updated_at' => $voiceData['updated_at'] ?? null
            ];
        } catch (Exception $e) {
            Log::error('Failed to get training progress: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Validate audio file for voice cloning
     * 
     * @param UploadedFile $audioFile
     * @throws Exception
     */
    private function validateAudioFile(UploadedFile $audioFile)
    {
        // Check file size (max 100MB)
        $maxSize = 100 * 1024 * 1024; // 100MB in bytes
        if ($audioFile->getSize() > $maxSize) {
            throw new Exception('Audio file size must be less than 100MB');
        }

        // Check file type
        $allowedMimeTypes = [
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/wave',
            'audio/x-wav',
            'audio/flac',
            'audio/ogg',
            'audio/webm'
        ];

        if (!in_array($audioFile->getMimeType(), $allowedMimeTypes)) {
            throw new Exception('Invalid audio file format. Supported formats: MP3, WAV, FLAC, OGG, WebM');
        }

        // Check file extension
        $allowedExtensions = ['mp3', 'wav', 'flac', 'ogg', 'webm'];
        $extension = strtolower($audioFile->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Invalid file extension. Supported extensions: ' . implode(', ', $allowedExtensions));
        }
    }

    /**
     * Parse error message from API response
     * 
     * @param RequestException $e
     * @return string
     */
    private function parseErrorMessage(RequestException $e)
    {
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody(), true);
            
            if ($body && isset($body['error']['message'])) {
                return $body['error']['message'];
            }
            
            if ($body && isset($body['message'])) {
                return $body['message'];
            }
            
            return 'HTTP ' . $response->getStatusCode() . ': ' . $response->getReasonPhrase();
        }
        
        return $e->getMessage();
    }


}