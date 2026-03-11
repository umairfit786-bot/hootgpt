<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SpeechifyCloneService;
use App\Services\MergeService;
use App\Services\HelperService;
use App\Models\VoiceoverResult;
use App\Models\VoiceoverLanguage;
use App\Models\CustomVoice;
use App\Models\SubscriptionPlan;
use App\Models\ExtensionSetting;
use App\Models\User;
use DataTables;
use Exception;
use DB;

class SpeechifyController extends Controller
{
    private $speechifyService;
    private $merge_files;

    public function __construct(SpeechifyCloneService $speechifyService)
    {
        $this->speechifyService = $speechifyService;
        $this->merge_files = new MergeService();
    }

    /**
     * Show voice cloning interface
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = VoiceoverResult::where('user_id', Auth::user()->id)->where('vendor', 'speechify')->where('mode', 'file')->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>
                                        <a href="'. route("user.voiceover.show", $row["id"] ). '"><i class="fa-solid fa-list-music table-action-buttons view-action-button" title="'. __('View Result') .'"></i></a>
                                        <a class="deleteResultButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark table-action-buttons delete-action-button" title="'. __('Delete Result') .'"></i></a>
                                    </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'd/m/Y').'</span>';
                        return $created_on;
                    })
                    ->addColumn('download', function($row){
                        $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                        $result = '<a class="" href="' . $url . '" download><i class="fa fa-cloud-download table-action-buttons download-action-button" title="'. __('Download Result') .'"></i></a>';
                        return $result;
                    })
                    ->addColumn('single', function($row){
                        $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                        $result = '<button type="button" class="result-play p-0" onclick="resultPlay(this)" src="' . $url . '" type="'. $row['audio_type'].'" id="'. $row['id'] .'"><i class="fa fa-play table-action-buttons view-action-button" title="'. __('Play Result') .'"></i></button>';
                        return $result;
                    })
                    ->addColumn('result', function($row){ 
                        $result = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                    return $result;
                    })
                    ->addColumn('custom-language', function($row) {
                        $language = '<span class="vendor-image-sm overflow-hidden"><img class="mr-2" src="' . URL::asset($row['language_flag']) . '">'. $row['language'] .'</span> ';            
                        return $language;
                    })
                    ->rawColumns(['actions', 'created-on', 'result', 'download', 'single', 'custom-language'])
                    ->make(true);
                    
        }
        
        $voices = DB::table('custom_voices')
            ->where('user_id', auth()->user()->id)           
            ->where('vendor', 'speechify')            
            ->orderBy('voice', 'asc')
            ->get();

        $check = ExtensionSetting::first();

        if (is_null(auth()->user()->plan_id)) {
            if (is_null($check->speechify_clone_free_tier) || !$check->speechify_clone_free_tier) {
                toastr()->warning(__('Speechify Voice Clone feature is not available for free tier users, subscribe to get a proper access'));
                return redirect()->route('user.dashboard');
            } else {
                return view('user.speechify_clone.index', compact('voices'));
            }
        } else {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->speechify_voice_clone_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for Speechify Voice Clone feature'));
                return redirect()->back();                   
            } else {
                return view('user.speechify_clone.index', compact('voices'));
            }
        } 

    }

    /**
     * Create a new voice clone
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string|max:1000',
            'language' => 'nullable|string|max:10',
            'gender' => 'nullable|in:male,female',
            'file' => 'required|array|min:1|max:10',
            'file.*' => 'required|file|mimes:mp3,wav,flac,ogg,webm|max:102400' // 100MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $audioFiles = $request->file('file');
            $voiceName = $request->input('name');
            $description = $request->input('description');
            
            $consentData = [
                'fullName' => $request->input('fullName'),
                'email' => $request->input('email')
            ];
            
            $options = [];
            if ($request->filled('language')) {
                $options['language'] = $request->input('language');
            }
            if ($request->filled('gender')) {
                $options['gender'] = $request->input('gender');
            }


            if (count($audioFiles) === 1) {
                $result = $this->speechifyService->createVoiceClone(
                    $audioFiles[0],
                    $voiceName,
                    $description,
                    $consentData,
                    $options
                );
            } else {
                $result = $this->speechifyService->createVoiceCloneFromMultipleFiles(
                    $audioFiles,
                    $voiceName,
                    $description,
                    $consentData,
                    $options
                );
            }

            // Save voice to database
            $voice = new CustomVoice();
            $voice->voice = $voiceName;
            $voice->voice_id = $result['id'];
            $voice->gender = ucfirst($request->input('gender', 'male'));
            $voice->vendor_id = 'speechify_clone';
            $voice->voice_type = 'cloned';
            $voice->status = 'training';
            $voice->vendor = 'speechify';
            $voice->vendor_img = '/img/csp/speechify-sm.png';
            $voice->avatar_url = '/voices/speechify/avatars/clone.jpg';
            $voice->user_id = auth()->user()->id;
            $voice->description = $description;
            $voice->save();

            return response()->json([
                'success' => true,
                'message' => 'Voice clone created successfully',
                'data' => $result
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

     /**
     * Process text synthesize request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function synthesize(Request $request)
    {   
        $input = json_decode(request('input_text'), true);
        $length = count($input);

        if ($request->ajax()) {
        
            request()->validate([                
                'title' => 'nullable|string|max:255',
            ]);


            # Count characters based on vendor requirements
            $total_characters = mb_strlen(request('input_text_total'), 'UTF-8');

            # Protection from overusage of credits
            if ($total_characters > config('settings.voiceover_max_chars_limit')) {
                return response()->json(["error" => __("Total characters of your text is more than allowed. Please decrease the length of your text.")], 422);
            }
            
            
            # Check if user has enough characters to proceed
            if (auth()->user()->characters != -1) {
                if ((Auth::user()->characters + Auth::user()->characters_prepaid) < $total_characters) {
                    return response()->json(["error" => __("Not enough available characters to process")], 422);
                }
            }


            # Variables for recording
            $total_text = '';
            $total_text_raw = '';
            $total_text_characters = 0;
            $inputAudioFiles = [];
            $plan_type = (Auth::user()->group == 'subscriber') ? 'paid' : 'free'; 

            # Audio Format
            $audio_type = 'audio/mpeg';

            # Process each textarea row
            foreach ($input as $key => $value) {
                $voice_id = explode('___', $key);
                $voice = CustomVoice::where('voice_id', $voice_id[0])->first();
                $language = VoiceoverLanguage::where('language_code', $request->language)->first();

                if ($length > 1) {
                    $total_text .= $voice->voice . ': '. preg_replace('/<[\s\S]+?>/', '', $value) . '. ';
                    $total_text_raw .= $voice->voice . ': '. $value . '. ';
                } else {
                    $total_text = preg_replace('/<[\s\S]+?>/', '', $value) . '. ';
                    $total_text_raw = $value . '. ';
                }


                # Count characters based on vendor requirements
                switch ($voice->vendor) {
                    case 'speechify':
                            $text_characters = mb_strlen($value, 'UTF-8');
                            $total_text_characters += $text_characters;
                        break;
                }
                
                
                # Check if user has characters available to proceed
                if (auth()->user()->characters != -1) {
                    if ((Auth::user()->characters + Auth::user()->characters_prepaid) < $text_characters) {
                        return response()->json(["error" => __("Not enough available characters to process")], 422);
                    } else {
                        $this->updateAvailableCharacters($text_characters);
                    }       
                }


                # Name and extention of the result audio file
                $temp_file_name = Str::random(10) . '.mp3';

                $response = $this->processText($voice, $value, 'mp3', $temp_file_name);

                if ($length == 1) {

                    if (config('settings.voiceover_default_storage') === 'aws') {
                        Storage::disk('s3')->writeStream($temp_file_name, Storage::disk('audio')->readStream($temp_file_name));
                        $result_url = Storage::disk('s3')->url($temp_file_name); 
                        Storage::disk('audio')->delete($temp_file_name);   
                    } elseif (config('settings.voiceover_default_storage') === 'r2') {
                        Storage::disk('r2')->writeStream($temp_file_name, Storage::disk('audio')->readStream($temp_file_name));
                        $result_url = Storage::disk('r2')->url($temp_file_name); 
                        Storage::disk('audio')->delete($temp_file_name); 
                    } elseif (config('settings.voiceover_default_storage') == 'wasabi') {
                        Storage::disk('wasabi')->writeStream($temp_file_name, Storage::disk('audio')->readStream($temp_file_name));
                        $result_url = Storage::disk('wasabi')->url($temp_file_name);
                        Storage::disk('audio')->delete($temp_file_name);                   
                    } elseif (config('settings.voiceover_default_storage') == 'gcp') {
                        Storage::disk('gcs')->put($temp_file_name, Storage::disk('audio')->readStream($temp_file_name));
                        Storage::disk('gcs')->setVisibility($temp_file_name, 'public');
                        $result_url = Storage::disk('gcs')->url($temp_file_name);
                        Storage::disk('audio')->delete($temp_file_name);
                        $storage = 'gcp';
                    } elseif (config('settings.voiceover_default_storage') == 'storj') {
                        Storage::disk('storj')->put($temp_file_name, Storage::disk('audio')->readStream($temp_file_name), 'public');
                        Storage::disk('storj')->setVisibility($temp_file_name, 'public');
                        $result_url = Storage::disk('storj')->temporaryUrl($temp_file_name, now()->addHours(167));
                        Storage::disk('audio')->delete($temp_file_name);
                        $storage = 'storj';                        
                    } elseif (config('settings.voiceover_default_storage') == 'dropbox') {
                        Storage::disk('dropbox')->put($temp_file_name, Storage::disk('audio')->readStream($temp_file_name));
                        $result_url = Storage::disk('dropbox')->url($temp_file_name);
                        Storage::disk('audio')->delete($temp_file_name);
                        $storage = 'dropbox';
                    } else {                
                        $result_url = Storage::url($temp_file_name);                
                    }                

                    # Update user synthesize task number
                    $this->updateSynthesizeTasks();

                    $result = new VoiceoverResult([
                        'user_id' => Auth::user()->id,
                        'language' => $language->language,
                        'language_flag' => $language->language_flag,
                        'voice' => $voice->voice,
                        'voice_id' => $voice_id[0],
                        'gender' => $voice->gender,
                        'text' => $total_text,
                        'text_raw' => $total_text_raw,
                        'characters' => $text_characters,
                        'file_name' => $temp_file_name,                    
                        'result_ext' => 'mp3',
                        'result_url' => $result_url,
                        'title' =>  htmlspecialchars(request('title')),
                        'voice_type' => $voice->voice_type,
                        'vendor' => $voice->vendor,
                        'vendor_id' => $voice->vendor_id,
                        'audio_type' => $audio_type,
                        'storage' => config('settings.voiceover_default_storage'),
                        'plan_type' => $plan_type,
                        'mode' => 'file',
                        'type' => 'custom'
                    ]); 
                        
                    $result->save();

                    $data = [];
                    $data['old'] = auth()->user()->characters + auth()->user()->characters_prepaid;
                    $data['current'] = (auth()->user()->characters + auth()->user()->characters_prepaid) - $text_characters;
                    $data['status'] = __("Success! Text was synthesized successfully");
                    return $data;

                } else {

                    array_push($inputAudioFiles, 'storage/' . $response['name']);

                    $result = new VoiceoverResult([
                        'user_id' => Auth::user()->id,
                        'language' => $language->language,
                        'voice' => $voice->voice,
                        'voice_id' => $voice_id[0],
                        'text_raw' => $value,
                        'vendor' => $voice->vendor,
                        'vendor_id' => $voice->vendor_id,
                        'characters' => $text_characters,
                        'voice_type' => $voice->voice_type,
                        'plan_type' => $plan_type,
                        'storage' => config('settings.voiceover_default_storage'),
                        'mode' => 'hidden',
                        'type' => 'custom'
                    ]); 
                        
                    $result->save();
                }
            }      

            # Process multi voice merge process
            if ($length > 1) {

                # Name and extention of the main audio file
                $file_name = Str::random(10) . '.mp3';

                # Update user synthesize task number
                $this->updateSynthesizeTasks();

                $this->merge_files->merge('mp3', $inputAudioFiles, 'storage/'. $file_name);

                if (config('settings.voiceover_default_storage') === 'aws') {
                    Storage::disk('s3')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('s3')->url($file_name); 
                    Storage::disk('audio')->delete($file_name);   
                } elseif (config('settings.voiceover_default_storage') === 'r2') {
                    Storage::disk('r2')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('r2')->url($file_name); 
                    Storage::disk('audio')->delete($file_name); 
                } elseif (config('settings.voiceover_default_storage') == 'wasabi') {
                    Storage::disk('wasabi')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('wasabi')->url($file_name);
                    Storage::disk('audio')->delete($file_name);                   
                } elseif (config('settings.voiceover_default_storage') == 'gcp') {
                    Storage::disk('gcs')->put($file_name, Storage::disk('audio')->readStream($file_name));
                    Storage::disk('gcs')->setVisibility($file_name, 'public');
                    $result_url = Storage::disk('gcs')->url($file_name);
                    Storage::disk('audio')->delete($file_name);
                    $storage = 'gcp';
                } elseif (config('settings.voiceover_default_storage') == 'storj') {
                    Storage::disk('storj')->put($file_name, Storage::disk('audio')->readStream($file_name), 'public');
                    Storage::disk('storj')->setVisibility($file_name, 'public');
                    $result_url = Storage::disk('storj')->temporaryUrl($file_name, now()->addHours(167));
                    Storage::disk('audio')->delete($file_name);
                    $storage = 'storj';                        
                } elseif (config('settings.voiceover_default_storage') == 'dropbox') {
                    Storage::disk('dropbox')->put($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('dropbox')->url($file_name);
                    Storage::disk('audio')->delete($file_name);
                    $storage = 'dropbox';
                } else {                
                    $result_url = Storage::url($file_name);                
                }
                 

                $result = new VoiceoverResult([
                    'user_id' => Auth::user()->id,
                    'language' => $language->language,
                    'language_flag' => $language->language_flag,
                    'voice' => $voice->voice,
                    'voice_id' => $voice_id[0],
                    'gender' => $voice->gender,
                    'text' => $total_text,
                    'text_raw' => $total_text_raw,
                    'characters' => $total_text_characters,
                    'file_name' => $file_name,
                    'result_url' => $result_url,
                    'result_ext' => 'mp3',
                    'title' => htmlspecialchars(request('title')),
                    'project' => request('project'),
                    'voice_type' => 'mixed',
                    'vendor' => $voice->vendor,
                    'vendor_id' => $voice->vendor_id,
                    'storage' => config('settings.voiceover_default_storage'),
                    'plan_type' => $plan_type,
                    'audio_type' => $audio_type,
                    'mode' => 'file',
                    'type' => 'custom'
                ]); 
                    
                $result->save();

                # Clean all temp audio files
                foreach ($inputAudioFiles as $value) {
                    $name_array = explode('/', $value);
                    $name = end($name_array);
                    if (Storage::disk('audio')->exists($name)) {
                        Storage::disk('audio')->delete($name);
                    }
                }              
                
                $data = [];
                $data['old'] = auth()->user()->characters + auth()->user()->characters_prepaid;
                $data['current'] = (auth()->user()->characters + auth()->user()->characters_prepaid) - $text_characters;
                $data['status'] = __("Success! Text was synthesized successfully");
                return $data;

            }
        }
    }


    /**
     * Process listen synthesize request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listen(Request $request)
    {   
        $input = json_decode(request('input_text'), true);
        $length = count($input);

        if ($request->ajax()) {

            request()->validate([                
                'title' => 'nullable|string|max:255',
            ]);

            # Count characters based on vendor requirements
            $total_characters = mb_strlen(request('input_text_total'), 'UTF-8');

            if ($total_characters > config('settings.voiceover_max_chars_limit')) {
                return response()->json(["error" => __('Total characters of your text is more than allowed. Please decrease the length of your text.')], 422);
            }
            
            if (auth()->user()->characters != -1) {
                if ((Auth::user()->characters + Auth::user()->characters_prepaid) < $total_characters) {
                    return response()->json(["error" => __("Not enough available characters to process")], 422);
                }
            }

            # Variables for recording
            $total_text_raw = '';
            $total_text_characters = 0;
            $inputAudioFiles = [];
            $plan_type = (Auth::user()->group == 'subscriber') ? 'paid' : 'free';

            # Audio Format
            $audio_type = 'audio/mpeg';


            # Process each textarea row
            foreach ($input as $key => $value) { 
    
                $total_text_raw .= $value . ' ';
                $voice_id = explode('___', $key);
                $voice = CustomVoice::where('voice_id', $voice_id[0])->first();
                $language = VoiceoverLanguage::where('language_code', $request->language)->first();


                # Count characters based on vendor requirements
                $text_characters = mb_strlen($value, 'UTF-8');
                $total_text_characters += $text_characters;
                
                
                # Check if user has characters available to proceed
                if (auth()->user()->characters != -1) {
                    if ((Auth::user()->characters + Auth::user()->characters_prepaid) < $total_characters) {
                        return response()->json(["error" => __("Not enough available characters to process")], 422);
                    } else {
                        $this->updateAvailableCharacters($total_characters);
                    } 
                }
                

                # Name and extention of the audio file
                $file_name = 'LISTEN--' . Str::random(10) . '.mp3';

                $response = $this->processText($voice, $value, 'mp3', $file_name);



                if ($length == 1) {

                    if (config('settings.voiceover_default_storage') === 'aws') {
                        Storage::disk('s3')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                        $result_url = Storage::disk('s3')->url($file_name); 
                        Storage::disk('audio')->delete($file_name);   
                    } elseif (config('settings.voiceover_default_storage') === 'r2') {
                        Storage::disk('r2')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                        $result_url = Storage::disk('r2')->url($file_name); 
                        Storage::disk('audio')->delete($file_name); 
                    } elseif (config('settings.voiceover_default_storage') == 'wasabi') {
                        Storage::disk('wasabi')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                        $result_url = Storage::disk('wasabi')->url($file_name);
                        Storage::disk('audio')->delete($file_name);                   
                    } elseif (config('settings.voiceover_default_storage') == 'gcp') {
                        Storage::disk('gcs')->put($file_name, Storage::disk('audio')->readStream($file_name));
                        Storage::disk('gcs')->setVisibility($file_name, 'public');
                        $result_url = Storage::disk('gcs')->url($file_name);
                        Storage::disk('audio')->delete($file_name);
                        $storage = 'gcp';
                    } elseif (config('settings.voiceover_default_storage') == 'storj') {
                        Storage::disk('storj')->put($file_name, Storage::disk('audio')->readStream($file_name), 'public');
                        Storage::disk('storj')->setVisibility($file_name, 'public');
                        $result_url = Storage::disk('storj')->temporaryUrl($file_name, now()->addHours(167));
                        Storage::disk('audio')->delete($file_name);
                        $storage = 'storj';                        
                    } elseif (config('settings.voiceover_default_storage') == 'dropbox') {
                        Storage::disk('dropbox')->put($file_name, Storage::disk('audio')->readStream($file_name));
                        $result_url = Storage::disk('dropbox')->url($file_name);
                        Storage::disk('audio')->delete($file_name);
                        $storage = 'dropbox';
                    } else {                
                        $result_url = Storage::url($file_name);                
                    }

                    $result = new VoiceoverResult([
                        'user_id' => Auth::user()->id,
                        'language' => $language->language,
                        'voice' => $voice->voice,
                        'voice_id' => $voice_id[0],
                        'characters' => $text_characters,
                        'voice_type' => $voice->voice_type,
                        'file_name' => $file_name,
                        'text_raw' => $value,
                        'result_ext' => 'mp3',
                        'result_url' => $result_url,
                        'audio_type' => $audio_type,
                        'plan_type' => $plan_type,
                        'vendor' => $voice->vendor,
                        'vendor_id' => $voice->vendor_id,
                        'mode' => 'live',
                        'type' => 'custom'
                    ]); 
                        
                    $result->save();

                    $data = [];
                    $data['old'] = auth()->user()->characters + auth()->user()->characters_prepaid;
                    $data['current'] = (auth()->user()->characters + auth()->user()->characters_prepaid) - $text_characters;
                    $data['audio_type'] = 'audio/mpeg';

                    if (config('settings.voiceover_default_storage') == 'local') 
                        $data['url'] = URL::asset($result_url);  
                    else            
                        $data['url'] = $result_url; 
                    
                    return $data;
                
                } else {

                    array_push($inputAudioFiles, 'storage/' . $response['name']);

                    $result = new VoiceoverResult([
                        'user_id' => Auth::user()->id,
                        'language' => $language->language,
                        'voice' => $voice->voice,
                        'vendor' => $voice->vendor,
                        'vendor_id' => $voice->vendor_id,
                        'voice_id' => $voice_id[0],
                        'text_raw' => $value,
                        'characters' => $text_characters,
                        'voice_type' => $voice->voice_type,
                        'plan_type' => $plan_type,
                        'mode' => 'hidden',
                        'type' => 'custom'
                    ]); 
                        
                    $result->save();
                }  
            }

            if ($length > 1) {

                # Name and extention of the main audio file
                $file_name = Str::random(10) . '.mp3';

                $this->merge_files->merge('mp3', $inputAudioFiles, 'storage/'. $file_name);

                if (config('settings.voiceover_default_storage') === 'aws') {
                    Storage::disk('s3')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('s3')->url($file_name); 
                    Storage::disk('audio')->delete($file_name);   
                } elseif (config('settings.voiceover_default_storage') === 'r2') {
                    Storage::disk('r2')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('r2')->url($file_name); 
                    Storage::disk('audio')->delete($file_name); 
                } elseif (config('settings.voiceover_default_storage') == 'wasabi') {
                    Storage::disk('wasabi')->writeStream($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('wasabi')->url($file_name);
                    Storage::disk('audio')->delete($file_name);                   
                } elseif (config('settings.voiceover_default_storage') == 'gcp') {
                    Storage::disk('gcs')->put($file_name, Storage::disk('audio')->readStream($file_name));
                    Storage::disk('gcs')->setVisibility($file_name, 'public');
                    $result_url = Storage::disk('gcs')->url($file_name);
                    Storage::disk('audio')->delete($file_name);
                    $storage = 'gcp';
                } elseif (config('settings.voiceover_default_storage') == 'storj') {
                    Storage::disk('storj')->put($file_name, Storage::disk('audio')->readStream($file_name), 'public');
                    Storage::disk('storj')->setVisibility($file_name, 'public');
                    $result_url = Storage::disk('storj')->temporaryUrl($file_name, now()->addHours(167));
                    Storage::disk('audio')->delete($file_name);
                    $storage = 'storj';                        
                } elseif (config('settings.voiceover_default_storage') == 'dropbox') {
                    Storage::disk('dropbox')->put($file_name, Storage::disk('audio')->readStream($file_name));
                    $result_url = Storage::disk('dropbox')->url($file_name);
                    Storage::disk('audio')->delete($file_name);
                    $storage = 'dropbox';
                } else {                
                    $result_url = Storage::url($file_name);                
                }

                $result = new VoiceoverResult([
                    'user_id' => Auth::user()->id,
                    'language' => $language->language,
                    'language_flag' => $language->language_flag,
                    'voice' => $voice->voice,
                    'voice_id' => $voice_id[0],
                    'characters' => $total_text_characters,
                    'voice_type' => 'mixed',
                    'file_name' => $file_name,
                    'text_raw' => $total_text_raw,
                    'result_ext' => 'mp3',
                    'result_url' => $result_url,
                    'audio_type' => $audio_type,
                    'plan_type' => $plan_type,
                    'vendor' => $voice->vendor,
                    'vendor_id' => $voice->vendor_id,
                    'mode' => 'live',
                    'type' => 'custom'
                ]); 
                    
                $result->save();

                # Clean all temp audio files
                foreach ($inputAudioFiles as $value) {
                    $name_array = explode('/', $value);
                    $name = end($name_array);
                    if (Storage::disk('audio')->exists($name)) {
                        Storage::disk('audio')->delete($name);
                    }
                }                

                $data = [];
                $data['old'] = auth()->user()->characters + auth()->user()->characters_prepaid;
                $data['current'] = (auth()->user()->characters + auth()->user()->characters_prepaid) - $total_text_characters;

                $data['audio_type'] = 'audio/mpeg';

                if (config('settings.voiceover_default_storage') == 'local') 
                    $data['url'] = URL::asset($result->result_url);  
                else            
                    $data['url'] = $result->result_url; 
                
                return $data;
            }
        }
    }


    /**
     * Update user characters number
     */
    private function updateAvailableCharacters($total)
    {
        HelperService::updateCharacterBalance($total);
    }


    /**
     * Update user synthesize task number
     */
    private function updateSynthesizeTasks()
    {
        if (Auth::user()->synthesize_tasks > 0) {
            $user = User::find(Auth::user()->id);
            $user->synthesize_tasks = Auth::user()->synthesize_tasks - 1;
            $user->update();
        } 
    }


    /**
     * Send settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configuration(Request $request)
    {   
        if ($request->ajax()) { 

            $data['char_limit'] = config('settings.voiceover_max_chars_limit');
            $data['voice_limit'] = config('settings.voiceover_max_voice_limit');

            return response()->json($data);   
        }    
    }


    /**
     * Get voice clone status
     */
    public function status($voiceId)
    {
        try {
            $status = $this->speechifyService->getVoiceStatus($voiceId);
            $progress = $this->speechifyService->getTrainingProgress($voiceId);

            // Update voice status in database
            $voice = CustomVoice::where('voice_id', $voiceId)->first();
            if ($voice) {
                $voice->status = $status['status'] === 'ready' ? 'active' : 'training';
                $voice->save();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $status,
                    'progress' => $progress
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * List custom voices
     */
    public function list(Request $request)
    {
        try {
            $limit = $request->input('limit', 50);
            $offset = $request->input('offset', 0);

            $voices = $this->speechifyService->listCustomVoices($limit, $offset);

            return response()->json([
                'success' => true,
                'data' => $voices
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update voice metadata
     */
    public function update(Request $request, $voiceId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = array_filter([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);

            $result = $this->speechifyService->updateVoice($voiceId, $data);

            // Update voice in database
            $voice = Voice::where('voice_id', $voiceId)->first();
            if ($voice && isset($data['name'])) {
                $voice->voice = $data['name'];
                $voice->save();
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete voice clone
     */
    public function delete($voiceId)
    {
        try {
            $result = $this->speechifyService->deleteVoice($voiceId);

            // Remove voice from database
            Voice::where('voice_id', $voiceId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Voice deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Process text synthesizes based on the vendor/voice selected
     */
    private function processText(CustomVoice $voice, $text, $format, $file_name)
    {   
        $speechify = new SpeechifyCloneService();
    
        return $speechify->synthesizeSpeech($voice, $text, $format, $file_name);
              
    }

}