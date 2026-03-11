<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\HelperService;
use App\Services\MergeService;
use App\Services\ElevenlabsTTSService;
use App\Models\VoiceoverResult;
use App\Models\User;
use App\Models\VoiceoverLanguage;
use App\Models\SubscriptionPlan;
use App\Models\Workbook;
use App\Models\CustomVoice;
use App\Models\ExtensionSetting;
use DataTables;
use Exception;
use DB;

class VoiceoverCloneController extends Controller
{
    private $merge_files;

    public function __construct()
    {
        $this->merge_files = new MergeService();
    }


    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        # Today's TTS Results for Datatable
        if ($request->ajax()) {
            $data = VoiceoverResult::where('user_id', Auth::user()->id)->where('voice_type', 'custom')->where('mode', 'file')->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    $actionBtn = '<div>
                                        <a href="' . route("user.voiceover.show", $row["id"]) . '"><i class="fa-solid fa-list-music table-action-buttons view-action-button" title="' . __('View Result') . '"></i></a>
                                        <a class="deleteResultButton" id="' . $row["id"] . '" href="#"><i class="fa-solid fa-trash-xmark table-action-buttons delete-action-button" title="' . __('Delete Result') . '"></i></a>
                                    </div>';
                    return $actionBtn;
                })
                ->addColumn('created-on', function ($row) {
                    $created_on = '<span>' . date_format($row["created_at"], 'd/m/Y') . '</span>';
                    return $created_on;
                })
                ->addColumn('download', function ($row) {
                    $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                    $result = '<a class="" href="' . $url . '" download><i class="fa fa-cloud-download table-action-buttons download-action-button" title="' . __('Download Result') . '"></i></a>';
                    return $result;
                })
                ->addColumn('single', function ($row) {
                    $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                    $result = '<button type="button" class="result-play p-0" onclick="resultPlay(this)" src="' . $url . '" type="' . $row['audio_type'] . '" id="' . $row['id'] . '"><i class="fa fa-play table-action-buttons view-action-button" title="' . __('Play Result') . '"></i></button>';
                    return $result;
                })
                ->addColumn('result', function ($row) {
                    $result = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                    return $result;
                })
                ->addColumn('custom-language', function ($row) {
                    $language = '<span class="vendor-image-sm overflow-hidden"><img class="mr-2" src="' . URL::asset($row['language_flag']) . '">' . $row['language'] . '</span> ';
                    return $language;
                })
                ->rawColumns(['actions', 'created-on', 'result', 'download', 'single', 'custom-language'])
                ->make(true);

        }

        # Set Voice Types
        $voices = DB::table('custom_voices')
            ->where('user_id', auth()->user()->id)
            ->where('status', 'active')
            ->orderBy('voice', 'asc')
            ->get();


        $projects = Workbook::where('user_id', auth()->user()->id)->get();

        $check = ExtensionSetting::first();
        $voices_count = count($voices);
        $voice_limit = 0;

        if (is_null(auth()->user()->plan_id)) {
            $voice_limit = $check->voice_clone_limit + auth()->user()->voice_clones_prepaid;
            if (is_null($check->voice_clone_free_tier) || !$check->voice_clone_free_tier) {
                if (auth()->user()->voice_clones_prepaid == 0) {
                    toastr()->warning(__('Voice Clone feature is not available for free tier users, subscribe to get a proper access'));
                    return redirect()->route('user.plans');
                } else {
                    return view('user.clone.index', compact('voices', 'projects', 'voice_limit', 'voices_count'));
                }
            } else {
                return view('user.clone.index', compact('voices', 'projects', 'voice_limit', 'voices_count'));
            }
        } else {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $voice_limit = $plan->voice_clone_number + auth()->user()->voice_clones_prepaid;
            if ($plan->voice_clone_feature == false) {
                if (auth()->user()->voice_clones_prepaid == 0) {
                    toastr()->warning(__('Your current subscription plan does not include support for Voice Clone feature'));
                    return redirect()->back();
                } else {
                    return view('user.clone.index', compact('voices', 'projects', 'voice_limit', 'voices_count'));
                }
            } else {
                return view('user.clone.index', compact('voices', 'projects', 'voice_limit', 'voices_count'));
            }
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
                $no_ssml_tags = preg_replace('/<[\s\S]+?>/', '', $value);

                if ($length > 1) {
                    $total_text .= $voice->voice . ': ' . preg_replace('/<[\s\S]+?>/', '', $value) . '. ';
                    $total_text_raw .= $voice->voice . ': ' . $value . '. ';
                } else {
                    $total_text = preg_replace('/<[\s\S]+?>/', '', $value) . '. ';
                    $total_text_raw = $value . '. ';
                }


                # Count characters based on vendor requirements
                switch ($voice->vendor) {
                    case 'elevenlabs':
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
                        'title' => htmlspecialchars(request('title')),
                        'project' => request('project'),
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

                $this->merge_files->merge('mp3', $inputAudioFiles, 'storage/' . $file_name);

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
                $no_ssml_tags = preg_replace('/<[\s\S]+?>/', '', $value);


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

                $this->merge_files->merge('mp3', $inputAudioFiles, 'storage/' . $file_name);

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


    public function create(Request $request)
    {
        if ($request->ajax()) {

            $voices = CustomVoice::where('user_id', auth()->user()->id)->count();
            $check = ExtensionSetting::first();

            if (is_null(auth()->user()->plan_id)) {
                if (($check->voice_clone_limit + auth()->user()->voice_clones_prepaid) <= $voices) {
                    $data['status'] = 400;
                    $data['message'] = __('You have reached voice clone limits, subscribe or buy prepaid clones to create more');
                    return $data;
                }
            } else {
                $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                if (($plan->voice_clone_number + auth()->user()->voice_clones_prepaid) <= $voices) {
                    $data['status'] = 400;
                    $data['message'] = __('You have reached voice clone limits included in your subscription and prepaid plans');
                    return $data;
                }
            }

            $elevenlabs = new ElevenlabsTTSService();
            $files = [];

            $count = count($_FILES['samples']['name']);
            for ($i = 0; $i < $count; $i++) {
                $files[$i] = $_FILES['samples']['tmp_name'][$i];
            }

            try {
                $response = $elevenlabs->addVoice($request->name, $request->description, $files);

                if ($response['status'] == 200) {

                    $avatar = ($request->gender == 'male') ? '/voices/elevenlabs/avatars/22.jpg' : '/voices/elevenlabs/avatars/1.jpg';
                    $result = new CustomVoice([
                        'user_id' => Auth::user()->id,
                        'voice' => $request->name,
                        'voice_id' => $response['voice_id'],
                        'gender' => $request->gender,
                        'description' => $request->description,
                        'avatar_url' => $avatar,
                    ]);

                    $result->save();

                    $data['status'] = 200;
                    $data['voice'] = $result;

                    return $data;
                } else {
                    $data['status'] = 400;
                    $data['message'] = $response['message'];

                    return $data;
                }
            } catch (\Exception $exception) {
                \Log::info($exception->getMessage());
            }

        }
    }


    public function edit(Request $request)
    {
        if ($request->ajax()) {

            $voice = CustomVoice::where('voice_id', request('train'))->first();

            if ($voice->user_id == Auth::user()->id) {
                $elevenlabs = new ElevenlabsTTSService();
                $files = [];

                $count = count($_FILES['samples']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $files[$i] = $_FILES['samples']['tmp_name'][$i];
                }

                try {

                    while (count($files) !== 0) {
                        $element = array_shift($files);
                        $contents = fopen($element, 'r');

                        $requestData = [
                            [
                                'name' => 'name',
                                'contents' => $voice->voice,
                            ],
                            [
                                'name' => 'files',
                                'contents' => $contents,
                            ],
                            [
                                'name' => 'description',
                                'contents' => $voice->description,
                            ],
                            [
                                'name' => 'labels',
                                'contents' => '',
                            ],
                        ];

                        $response = $elevenlabs->editVoice($requestData, request('train'));
                    }

                    $data['status'] = 200;

                    return $data;

                } catch (\Exception $exception) {
                    \Log::info($exception->getMessage());
                }
            }

        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(VoiceoverResult $id)
    {
        if ($id->user_id == Auth::user()->id) {

            return view('user.voiceover.show', compact('id'));

        } else {
            return redirect()->route('user.voiceover');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if ($request->ajax()) {

            $result = VoiceoverResult::where('id', request('id'))->firstOrFail();

            if ($result->user_id == Auth::user()->id) {

                switch ($result->storage) {
                    case 'local':
                        if (Storage::disk('audio')->exists($result->file_name)) {
                            Storage::disk('audio')->delete($result->file_name);
                        }
                        break;
                    case 'aws':
                        if (Storage::disk('s3')->exists($result->result_url)) {
                            Storage::disk('s3')->delete($result->result_url);
                        }
                        break;
                    case 'r2':
                        if (Storage::disk('r2')->exists($result->result_url)) {
                            Storage::disk('r2')->delete($result->result_url);
                        }
                        break;
                    case 'wasabi':
                        if (Storage::disk('wasabi')->exists($result->result_url)) {
                            Storage::disk('wasabi')->delete($result->result_url);
                        }
                        break;
                    default:
                        # code...
                        break;
                }

                $result->delete();

                return response()->json('success');

            } else {
                return response()->json('error');
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
     * Process text synthesizes based on the vendor/voice selected
     */
    private function processText(CustomVoice $voice, $text, $format, $file_name)
    {
        $elevenlabs = new ElevenlabsTTSService();

        return $elevenlabs->synthesizeSpeechCustom($voice, $text, $file_name);

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
     * Delete cloned voice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voiceDelete(Request $request)
    {
        if ($request->ajax()) {

            $result = CustomVoice::where('voice_id', request('id'))->firstOrFail();

            if ($result->user_id == Auth::user()->id) {

                $elevenlabs = new ElevenlabsTTSService();

                $elevenlabs->deleteVoice(request('id'));

                $result->delete();

                return response()->json('success');

            } else {
                return response()->json('error');
            }
        }
    }

}
