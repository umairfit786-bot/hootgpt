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
use App\Models\VideoImageResult;
use App\Models\ImageCredit;
use App\Models\User;
use App\Models\ApiKey;
use App\Models\ExtensionSetting;
use DataTables;

class VideoImageController extends Controller
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

        $results = VideoImageResult::where('user_id', auth()->user()->id)->where('status', '<>', 'failed')->orderBy('created_at','desc')->get();

        if (is_null(auth()->user()->plan_id)) {
            if (is_null($check->video_image_free_tier) || !$check->video_image_free_tier) {
                toastr()->warning(__('AI Image to Video feature is not available for free tier users, subscribe to get a proper access'));
                return redirect()->route('user.plans');
            } else {
                return view('user.video_image.index', compact('credits', 'results'));
            }
        } else {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->video_image_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for AI Image to Video feature'));
                return redirect()->back();                   
            } else {
                return view('user.video_image.index', compact('credits', 'results'));
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

            # Verify if user has enough credits
            $credit_status = $this->checkCredits($request->model);
            if (!$credit_status) {
                $data['status'] = 'error';
                $data['message'] = __('Not enough media credits to proceed, subscribe or top up your media credit balance and try again');
                return $data;
            }

            $image_path = request()->file('image')->getRealPath();
            $image_extension = request()->file('image')->getClientOriginalExtension();

            $name = 'source-' . Str::random(10) . '.' . $image_extension;
            Storage::disk('audio')->put('source/' . $name, file_get_contents($image_path));
            $video_url = 'storage/source/' . $name;
            $prompt = '';

            if ($request->model != 'stable-diffusion') {

                if (is_null($check->video_image_falai_api) || $check->video_image_falai_api == '') {
                    $data['status'] = 'error';
                    $data['message'] = __('You must include your Fal AI API key first');
                    return $data; 
                } 

                switch ($request->model) {
                    case 'kling-video':
                    case 'kling-video-21-master':
                    case 'kling-video-21-pro':
                        $prompt = [
                            'image_url' => asset($video_url),
                            'prompt' => $request->prompt,
                            'duration' => $request->duration_kling,
                            'aspect_ratio' => $request->aspect_ratio_kling
                        ];
                        $response = $this->generate($prompt, $request->model);
                        break;
                    case 'kling-video-21-standard':
                        $prompt = [
                            'image_url' => asset($video_url),
                            'prompt' => $request->prompt,
                            'duration' => $request->duration_kling,
                        ];
                        $response = $this->generate($prompt, $request->model);
                        break;
                    case 'haiper-video-v2':
                        $prompt = [
                            'image_url' => asset($video_url),
                            'prompt' => $request->prompt,
                            'duration' => $request->duration_haiper
                        ];
                        $response = $this->generate($prompt, $request->model);
                        break;
                    case 'luma-dream-machine':
                        $prompt = [
                            'image_url' => asset($video_url),
                            'prompt' => $request->prompt,
                            'aspect_ratio' => $request->aspect_ratio_luma
                        ];
                        $response = $this->generate($prompt, $request->model);
                        break;
                    case 'google-veo2':
                        $prompt = [
                            'image_url' => asset($video_url),
                            'prompt' => $request->prompt,
                            'aspect_ratio' => $request->aspect_ratio_veo2,
                            'duration' => $request->duration_veo2
                        ];
                        $response = $this->generate($prompt, $request->model);
                        break;
                }
                
    
                if ($response['status'] == 'success') {
                    if (isset($response['request_id'])) {
    
                        # Update credit balance
                        $this->updateBalance($request->model);
    
                        $video = new VideoImageResult([
                            'user_id' => Auth::user()->id,
                            'title' => $request->title,
                            'request_id' => $response['request_id'],
                            'status' => 'processing',
                            'model' => $request->model,
                            'data' => serialize($prompt)
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
                                                            </div>
                                                        </div>
                                                    </div>';
    
                        $data['status'] = 'success';
                        $data['result'] = $video_box;
                        $data['message'] = __('AI Video to Video task has been successfully created');                  
                        return $data; 
                    } else {
                        $data['status'] = 'error';
                        $data['message'] = __('There has been an error creating AI Video to Video task');                  
                        return $data; 
                    }
    
                } else {
                    $data['status'] = 'error';
                    $data['message'] = $response['message']['detail'];                  
                    return $data; 
                }

            } else {
                if (is_null($check->video_image_stability_api) || $check->video_image_stability_api == '') {
                    $data['status'] = 'error';
                    $data['message'] = __('You must include your Stable Diffusion API key first');
                    return $data; 
                } 

                $url = 'https://api.stability.ai/v2beta/image-to-video';

                $ch = curl_init();
                    
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: multipart/form-data',
                    'Accept: application/json',
                    'Authorization: Bearer '. $check->video_image_stability_api
                ));

                $postFields = array(
                    'image' => new \CURLFile($image_path),
                    'motion_bucket_id' => (int)$request->motion_bucket_id,
                    'seed' => (int)$request->seed,
                    'cfg_scale' => (int)$request->cfg_scale,
                ); 

                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->build_post_fields($postFields)); 
                $result = curl_exec($ch);
                curl_close($ch);

                $response = json_decode($result , true);

                if (isset($response['errors'])) {
                    $data['status'] = 'error';
                    $data['message'] = $response['errors'][0];
                    return $data;
                } else {
                    if (isset($response['id'])) {
                        
                        # Update credit balance
                        $this->updateBalance($request->model);
                        $inputs = [];
                        $inputs = [
                            'seed' => $request->seed,
                            'cfg_scale' => $request->cfg_scale,
                            'motion_bucket_id' => $request->motion_bucket_id
                        ];

                        $video = new VideoImageResult([
                            'user_id' => Auth::user()->id,
                            'title' => $request->title,
                            'request_id' => $response['id'],
                            'status' => 'processing',
                            'model' => $request->model,
                            'data' =>  serialize($inputs)
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
                                                            </div>
                                                        </div>
                                                    </div>';
    
                        $data['status'] = 'success';
                        $data['result'] = $video_box;
                        $data['message'] = __('AI Video to Video task has been successfully created');                  
                        return $data;  

                    } else {

                        if (isset($response['name'])) {
                            if ($response['name'] == 'insufficient_balance') {
                                $message = __('You do not have sufficent balance in your Stable Diffusion account to generate new videos');
                            } elseif ($response['name'] == 'bad_request') {
                                $message = $response['errors'][0];
                            } else {
                                $message =  __('There was an issue generating your AI Video, please try again or contact support team');
                            }
                        } else {
                            $message = __('There was an issue generating your AI Video, please try again or contact support team');
                        }

                        $data['status'] = 'error';
                        $data['message'] = $message;
                        return $data;
                    }
                }
            }          
          
        }
	}


    public static function generate($prompt, $model)
    {
        $setting = ExtensionSetting::first();

        switch ($model) {
            case 'kling-video':
                $model = 'fal-ai/kling-video/v1.6/pro/image-to-video';
                break;
            case 'kling-video-21-standard':
                $model = 'fal-ai/kling-video/v2.1/standard/image-to-video';
                break;
            case 'kling-video-21-pro':
                $model = 'fal-ai/kling-video/v2.1/pro/image-to-video';
                break;
            case 'kling-video-21-master':
                $model = 'fal-ai/kling-video/v2.1/master/image-to-video';
                break;
            case 'haiper-video-v2':
                $model = 'fal-ai/haiper-video/v2.5/image-to-video/fast';
                break;
            case 'luma-dream-machine':
                $model = 'fal-ai/luma-dream-machine/image-to-video';
                break;
            case 'google-veo2':
                $model = 'fal-ai/veo2/image-to-video';
                break;
        }
       

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Key ' . $setting->video_image_falai_api,
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


    public function checkStatus()
    {
        $tasks = VideoImageResult::where('user_id', auth()->user()->id)->where('status', 'processing')->get();

        if ($tasks) {
            foreach ($tasks as $task) {
                if ($task->model != 'stable-diffusion') {
                    $result = $this->status($task->request_id, $task->model);

                    if ($result == 'COMPLETED') {
                        $result = $this->get($task->request_id, $task->model);

                        $url = (data_get($result, 'video.url') == '') ? data_get($result, 'video') : data_get($result, 'video.url');

                        $task->url = $url;
                        $task->status = 'completed';
                        $task->save();
                    }
                } else {

                    $check = ExtensionSetting::first();

                    $id = $task->request_id;

                    $url = 'https://api.stability.ai/v2beta/image-to-video/result/' . $id;

                    $ch = curl_init();
                
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Accept: application/json',
                        'Authorization: Bearer '. $check->video_image_stability_api
                    ));

                    $result = curl_exec($ch);
                    curl_close($ch);

                    $response = json_decode($result , true);
                    
                    if (isset($response['finish_reason'])) {

                        if ($response['finish_reason'] == 'SUCCESS') {

                            $name = 'video-' . Str::random(10) . '.mp4';

                            Storage::disk('public')->put('images/' . $name, base64_decode($response['video']));
                            $video_url = 'images/' . $name;
                            
                            $task->url = asset($video_url);
                            $task->status = 'completed';
                            $task->save();
                        }
                    }

                }
                
            }
        }
    }


    public function status($id, $model) 
    {
        $setting = ExtensionSetting::first();

        if ($model != 'stable-diffusion') {
            switch ($model) {
                case 'kling-video':
                case 'kling-video-21-standard':
                case 'kling-video-21-pro':
                case 'kling-video-21-master':
                    $url = 'https://queue.fal.run/fal-ai/kling-video/requests/'. $id . '/status';
                    break;
                case 'haiper-video-v2':
                    $url = 'https://queue.fal.run/fal-ai/haiper-video/requests/'. $id .'/status';
                    break;
                case 'luma-dream-machine':
                    $url = 'https://queue.fal.run/fal-ai/luma-dream-machine/requests/'. $id .'/status';
                    break;
                case 'google-veo2':
                    $url = 'https://queue.fal.run/fal-ai/veo2/requests/'. $id .'/status';
                    break;
            }        
    
            $http = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Key ' . $setting->video_image_falai_api,
            ])->get($url);
    
            return $http->json('status');
        }

       

	}


    public static function get($id, $model)
    {
        $setting = ExtensionSetting::first();

        if ($model != 'stable-diffusion') {
            
            switch ($model) {
                case 'kling-video-21-master':
                case 'kling-video-21-pro':
                case 'kling-video-21-standard':
                case 'kling-video':
                    $url = 'https://queue.fal.run/fal-ai/kling-video/requests/'. $id;
                    break;
                case 'haiper-video-v2':
                    $url = 'https://queue.fal.run/fal-ai/haiper-video/requests/'. $id;
                    break;
                case 'luma-dream-machine':
                    $url = 'https://queue.fal.run/fal-ai/luma-dream-machine/requests/'. $id;
                    break;
                case 'google-veo2':
                    $url = 'https://queue.fal.run/fal-ai/veo2/requests/'. $id;
                    break;
            }
            
    
            $http = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Key ' . $setting->video_image_falai_api,
            ])->get($url);
    
            if ($videos = $http->json('video')) {
                if (is_array($videos)) {
                    $video = Arr::first($videos);
    
                    return ['video' => $video];
                }
            }
    
            return false;
        }

    }


    public function checkCredits($model)
    {
        $status = true;
        
        switch ($model) {
            case 'kling-video':
                $status = HelperService::checkMediaCredits('kling_15_video_image');
                break;
            case 'kling-video-21-standard':
                $status = HelperService::checkMediaCredits('kling_21_standard_video_image');
                break;
            case 'kling-video-21-pro':
                $status = HelperService::checkMediaCredits('kling_21_pro_video_image');
                break;
            case 'kling-video-21-master':
                $status = HelperService::checkMediaCredits('kling_21_master_video_image');
                break;
            case 'haiper-video-v2':
                $status = HelperService::checkMediaCredits('haiper_2_video_image');
                break;
            case 'stable-diffusion':
                $status = HelperService::checkMediaCredits('stable_diffusion_video_image');
                break;
            case 'luma-dream-machine':
                $status = HelperService::checkMediaCredits('luma_dream_machine_video_image');
                break;
            case 'google-veo2':
                $status = HelperService::checkMediaCredits('google_veo2_video_image');
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
            case 'kling-video':
                HelperService::updateMediaBalance('kling_15_video_image');
                break;
            case 'kling-video-21-standard':
                HelperService::updateMediaBalance('kling_21_standard_video_image');
                break;
            case 'kling-video-21-pro':
                HelperService::updateMediaBalance('kling_21_pro_video_image');
                break;
            case 'kling-video-21-master':
                HelperService::updateMediaBalance('kling_21_master_video_image');
                break;
            case 'haiper-video-v2':
                HelperService::updateMediaBalance('haiper_2_video_image');
                break;
            case 'luma-dream-machine':
                HelperService::updateMediaBalance('luma_dream_machine_video_image');
                break;
            case 'stable-diffusion':
                HelperService::updateMediaBalance('stable_diffusion_video_image');
                break;
            case 'google-veo2':
                HelperService::updateMediaBalance('google_veo2_video_image');
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

            $image = VideoImageResult::where('id', request('id'))->first(); 

            if ($image->user_id == auth()->user()->id){

                $image->delete();

                return response()->json(200);
    
            } else{
                return response()->json(400);
            }  
        }
	}


    public function build_post_fields( $data,$existingKeys='',&$returnArray=[])
    {
        if(($data instanceof \CURLFile) or !(is_array($data) or is_object($data))){
            $returnArray[$existingKeys]=$data;
            return $returnArray;
        }
        else{
            foreach ($data as $key => $item) {
                $this->build_post_fields($item,$existingKeys?$existingKeys."[$key]":$key,$returnArray);
            }
            return $returnArray;
        }
    }


}
