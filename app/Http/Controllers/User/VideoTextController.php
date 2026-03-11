<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\HelperService;
use App\Models\SubscriptionPlan;
use App\Models\VideoTextResult;
use App\Models\User;
use App\Models\ImageCredit;
use App\Models\ExtensionSetting;

class VideoTextController extends Controller
{

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   

        $check = ExtensionSetting::first();
        $credits = ImageCredit::first();

        $this->checkStatus();

        $results = VideoTextResult::where('user_id', auth()->user()->id)->where('status', '<>', 'failed')->orderBy('created_at','desc')->get();

        if (is_null(auth()->user()->plan_id)) {
            if (is_null($check->video_text_free_tier) || !$check->video_text_free_tier) {
                toastr()->warning(__('AI Text to Video feature is not available for free tier users, subscribe to get a proper access'));
                return redirect()->route('user.dashboard');
            } else {
                return view('user.video_text.index', compact('credits', 'results'));
            }
        } else {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->video_text_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for AI Text to Video feature'));
                return redirect()->back();                   
            } else {
                return view('user.video_text.index', compact('credits', 'results'));
            }
        } 

    }


    /**
	*
	* Process Davinci Image to Video
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function create(Request $request) 
    {
        if ($request->ajax()) {

            $check = ExtensionSetting::first();

            if (is_null($check->video_text_falai_api) || $check->video_text_falai_api == '') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your Fal AI API key first');
                return $data; 
            } 

            if (is_null($check->video_text_openai_api) || $check->video_text_openai_api == '') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your OpenAI API key first');
                return $data; 
            } 

            # Verify if user has enough credits
            $credit_status = $this->checkCredits($request->model);
            if (!$credit_status) {
                $data['status'] = 'error';
                $data['message'] = __('Not enough media credits to proceed, subscribe or top up your media credit balance and try again');
                return $data;
            }

            switch ($request->model) {
                case 'kling-video':
                case 'kling-video-21-master':
                    $response = $this->generate($request->prompt, $request->model, $request->duration, $request->aspect_ratio);
                    break;
                case 'haiper-video-v2':
                    $response = $this->generate($request->prompt, $request->model, $request->duration_haiper);
                    break;
                case 'minimax-video':
                    $response = $this->generate($request->prompt, $request->model);
                    break;
                case 'mochi-v1':
                    $response = $this->generate($request->prompt, $request->model);
                    break;
                case 'luma-dream-machine':
                    $response = $this->generate($request->prompt, $request->model, null, $request->aspect_ratio_luma);
                    break;
                case 'hunyuan-video':
                    $response = $this->generate($request->prompt, $request->model);
                    break;
                case 'google-veo3':
                    $response = $this->generate($request->prompt, $request->model, null, $request->aspect_ratio, $request->audio_veo);
                    break;
                case 'openai-sora-2':
                case 'openai-sora-2-pro':
                    $response = $this->generateOpenai($request->prompt, $request->model, $request->duration_sora, $request->aspect_ratio_sora);
                    break;
            }
            

            if ($response['status'] == 'success') {
                if (isset($response['request_id'])) {

                    # Update credit balance
                    $this->updateBalance($request->model);

                    $duration = ($request->duration) ? $request->duration : 5;

                    $video = new VideoTextResult([
                        'user_id' => Auth::user()->id,
                        'title' => $request->title,
                        'request_id' => $response['request_id'],
                        'status' => 'processing',
                        'model' => $request->model,
                        'duration' => $duration,
                        'prompt' => $request->prompt
                    ]);

                    $video->save();

                    $video_box = ' <div class="col-md-6 col-sm-12">
                                                    <div class="card p-4 border-0">
                                                        <video controls>
                                                            <source src="" type="video/mp4">
                                                        </video>
                                                        <div class="text-center mt-3 relative">
                                                            <h6 class="mb-1 font-weight-semibold">'. $request->title .'</h6>
                                                            <p class="text-muted fs-12 mb-1">' . date('M d, Y') . '</p> 
                                                            <p class="text-muted fs-12 mb-0">('.__('Processing') .')</p> 
                                                            <a href="" class="avatar-result-delete" data-id="'. $video->id . '" data-tippy-content="'. __('Delete Video Result') .'"><i class="fa-solid fa-trash-xmark"></i></a>
                                                            <a href="" class="avatar-result-prompt" data-tippy-content="Prompt: '. $request->prompt .'"><i class="fa-solid text-muted fa-circle-info"></i></a>
                                                        </div>
                                                    </div>
                                                </div>';

                    $data['status'] = 'success';
                    $data['result'] = $video_box;
                    $data['message'] = __('AI Text to Video task has been successfully created');                  
                    return $data; 
                } else {
                    $data['status'] = 'error';
                    $data['message'] = __('There has been an error creating AI Text to Video task');                  
                    return $data; 
                }

            } else {
                $data['status'] = 'error';
                $data['message'] = $response['message'];                  
                return $data; 
            }

          
        }
	}


    public static function generate($prompt, $model = 'kling-video', $duration = null, $aspect_ratio = null, $audio = false)
    {
        $setting = ExtensionSetting::first();

        switch ($model) {
            case 'google-veo3':
                $model = 'fal-ai/veo3';
                $prompt = [
                    'prompt' => $prompt,
                    'aspect_ratio' => $aspect_ratio,
                    'generate_audio' => $audio,
                ];
                break;
            case 'kling-video':
                $model = 'fal-ai/kling-video/v1.6/pro/text-to-video';
                $prompt = [
                    'prompt' => $prompt,
                    'duration' => $duration,
                    'aspect_ratio' => $aspect_ratio
                ];
                break;
            case 'kling-video-21-master':
                $model = 'fal-ai/kling-video/v2.1/master/image-to-video';
                $prompt = [
                    'prompt' => $prompt,
                    'duration' => $duration,
                    'aspect_ratio' => $aspect_ratio
                ];
                break;
            case 'haiper-video-v2':
                $model = 'fal-ai/haiper-video/v2.5/fast';
                $prompt = [
                    'prompt' => $prompt,
                    'duration' => $duration,
                ];
                break;
            case 'minimax-video':
                $model = 'fal-ai/minimax-video';
                $prompt = [
                    'prompt' => $prompt,
                ];
                break;
            case 'mochi-v1':
                $model = 'fal-ai/mochi-v1';
                $prompt = [
                    'prompt' => $prompt,
                ];
                break;
            case 'luma-dream-machine':
                $model = 'fal-ai/luma-dream-machine';
                $prompt = [
                    'prompt' => $prompt,
                    'aspect_ratio' => $aspect_ratio
                ];
                break;
            case 'hunyuan-video':
                $model = 'fal-ai/hunyuan-video';
                $prompt = [
                    'prompt' => $prompt,
                ];
                break;
            default:
                $model = 'fal-ai/kling-video/v1.6/pro/text-to-video';
                $prompt = [
                    'prompt' => $prompt,
                ];
                break;
        }
       

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Key ' . $setting->video_text_falai_api,
        ])->post('https://queue.fal.run/' . $model, $prompt);


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


    public static function generateOpenai($prompt, $model, $duration = null, $aspect_ratio = null)
    {
        $setting = ExtensionSetting::first();

        $modelName = ($model == 'openai-sora-2') ? 'sora-2' : 'sora-2-pro';
        
        $payload = ['prompt' => $prompt, 'model' => $modelName];
        
        if ($duration) {
            $payload['seconds'] = (int)$duration;
        }
        
        if ($aspect_ratio) {
            $payload['size'] = $aspect_ratio;
        }

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $setting->video_text_openai_api,
        ])->post('https://api.openai.com/v1/videos', $payload);

        if ($http->status() == 200) {
            $data['status'] = 'success';
            $data['request_id'] = $http->json('id');
            return $data;
        } else {
            $data['status'] = 'error';
            $data['message'] = $http->json();
            return $data;
        }
    }


    public function checkStatus()
    {
        $tasks = VideoTextResult::where('user_id', auth()->user()->id)->where('status', 'processing')->get();

        if ($tasks) {
            foreach ($tasks as $task) {
                $result = $this->status($task->request_id, $task->model);

                if ($result == 'COMPLETED' || $result == 'completed') {

                    $result = $this->get($task->request_id, $task->model);

                    if ($result) {

                        if ($task->model != 'openai-sora-2' && $task->model != 'openai-sora-2-pro') {
                            $url = (data_get($result, 'video.url') == '') ? data_get($result, 'video') : data_get($result, 'video.url');
                        } else {
                            $filename = 'video_' . '_' . time() . '.mp4';
                            Storage::disk('public')->put('/storage/video/' . $filename, $result['video']);
                            $url = URL::to('/') . '/storage/video/' . $filename;
                        }

                        $task->url = $url;
                        $task->status = 'completed';
                        $task->save();
                    }
                   
                }
            }
        }
    }


    public function status($id, $model) 
    {
        $setting = ExtensionSetting::first();

        switch ($model) {
            case 'openai-sora-2':
            case 'openai-sora-2-pro':
                $http = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $setting->video_text_openai_api,
                ])->get('https://api.openai.com/v1/videos/' . $id);
                return $http->json('status');
                break;
            case 'kling-video':
            case 'kling-video-21-master':
                $url = 'https://queue.fal.run/fal-ai/kling-video/requests/'. $id . '/status';
                break;
            case 'haiper-video-v2':
                $url = 'https://queue.fal.run/fal-ai/haiper-video/requests/'. $id .'/status';
                break;
            case 'minimax-video':
                $url = 'https://queue.fal.run/fal-ai/minimax-video/requests/'. $id .'/status';
                break;
            case 'mochi-v1':
                $url = 'https://queue.fal.run/fal-ai/mochi-v1/requests/'. $id .'/status';
                break;
            case 'luma-dream-machine':
                $url = 'https://queue.fal.run/fal-ai/luma-dream-machine/requests/'. $id .'/status';
                break;
            case 'hunyuan-video':
                $url = 'https://queue.fal.run/fal-ai/hunyuan-video/requests/'. $id .'/status';
                break;
            case 'google-veo3':
                $url = 'https://queue.fal.run/fal-ai/veo3/requests/'. $id .'/status';
                break;
        }        

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Key ' . $setting->video_text_falai_api,
        ])->get($url);

        return $http->json('status');

	}


    public static function get($id, $model)
    {
        $setting = ExtensionSetting::first();

        switch ($model) {
            case 'openai-sora-2':
            case 'openai-sora-2-pro':
                $http = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $setting->video_text_openai_api,
                ])->get('https://api.openai.com/v1/videos/' . $id . '/content');
                
                if ($http->status() == 200) {
                    return ['video' => $http->body()];
                }
                return false;
            case 'kling-video':
            case 'kling-video-21-master':
                $url = 'https://queue.fal.run/fal-ai/kling-video/requests/'. $id;
                break;
            case 'haiper-video-v2':
                $url = 'https://queue.fal.run/fal-ai/haiper-video/requests/'. $id;
                break;
            case 'minimax-video':
                $url = 'https://queue.fal.run/fal-ai/minimax-video/requests/'. $id;
                break;
            case 'mochi-v1':
                $url = 'https://queue.fal.run/fal-ai/mochi-v1/requests/'. $id;
                break;
            case 'luma-dream-machine':
                $url = 'https://queue.fal.run/fal-ai/luma-dream-machine/requests/'. $id;
                break;
            case 'hunyuan-video':
                $url = 'https://queue.fal.run/fal-ai/hunyuan-video/requests/'. $id;
                break;
            case 'google-veo3':
                $url = 'https://queue.fal.run/fal-ai/veo3/requests/'. $id;
                break;
        }
        

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Key ' . $setting->video_text_falai_api,
        ])->get($url);

        if ($videos = $http->json('video')) {
            if (is_array($videos)) {
                $video = Arr::first($videos);

                return ['video' => $video];
            }
        }

        return false;
    }


    public function checkCredits($model)
    {
        $status = true;
        
        switch ($model) {
            case 'openai-sora-2-pro':
                $status = HelperService::checkMediaCredits('openai_sora_2_pro_video');
                break;
            case 'openai-sora-2':
                $status = HelperService::checkMediaCredits('openai_sora_2_video');
                break;
            case 'google-veo3':
                $status = HelperService::checkMediaCredits('google_veo3_video');
                break;
            case 'kling-video':
                $status = HelperService::checkMediaCredits('kling_15_video');
                break;
            case 'kling-video-21-master':
                $status = HelperService::checkMediaCredits('kling_21_master_video');
                break;
            case 'haiper-video-v2':
                $status = HelperService::checkMediaCredits('haiper_2_video');
                break;
            case 'minimax-video':
                $status = HelperService::checkMediaCredits('minimax_video');
                break;
            case 'luma-dream-machine':
                $status = HelperService::checkMediaCredits('luma_dream_machine_video');
                break;
            case 'mochi-v1':
                $status = HelperService::checkMediaCredits('mochi_1_video');
                break;
            case 'hunyuan-video':
                $status = HelperService::checkMediaCredits('hunyuan_video');
                break;
        }

        return $status;
            
    }


    /**
	*
	* Update user image balance
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function updateBalance($model) 
    {
        switch ($model) {
            case 'openai-sora-2-pro':
                HelperService::updateMediaBalance('openai_sora_2_pro_video');
                break;
            case 'openai-sora-2':
                HelperService::updateMediaBalance('openai_sora_2_video');
                break;
            case 'google-veo3':
                HelperService::updateMediaBalance('google_veo3_video');
                break;
            case 'kling-video':
                HelperService::updateMediaBalance('kling_15_video');
                break;
            case 'kling-video-21-master':
                HelperService::updateMediaBalance('kling_21_master_video');
                break;
            case 'haiper-video-v2':
                HelperService::updateMediaBalance('haiper_2_video');
                break;
            case 'minimax-video':
                HelperService::updateMediaBalance('minimax_video');
                break;
            case 'mochi-v1':
                HelperService::updateMediaBalance('mochi_1_video');
                break;
            case 'luma-dream-machine':
                HelperService::updateMediaBalance('luma_dream_machine_video');
                break;
            case 'hunyuan-video':
                HelperService::updateMediaBalance('hunyuan_video');
                break;
        }
    }


    /**
	*
	* Delete File
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function delete(Request $request) 
    {
        if ($request->ajax()) {

            $image = VideoTextResult::where('id', request('id'))->first(); 

            if ($image->user_id == auth()->user()->id){

                $image->delete();

                return response()->json(200);
    
            } else{
                return response()->json(400);
            }  
        }
	}

}
