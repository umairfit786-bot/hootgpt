<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExtensionSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Avatar;
use App\Models\AvatarAsset;
use App\Models\AvatarCreditUsage;
use App\Models\AvatarFavorite;
use App\Models\AvatarResult;
use App\Models\SubscriptionPlan;
use Exception;

class AvatarController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = ExtensionSetting::first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $setting = ExtensionSetting::first();

        if (is_null(auth()->user()->plan_id)) {
            if (is_null($setting->heygen_avatar_feature) || !$setting->heygen_avatar_feature) {
                return redirect()->route('user.dashboard');
            } else {
                return view('user.avatar.index');
            }
        } else {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->avatar_feature == false) {
                toastr()->warning(__('Your current subscription plan does not include support for AI Avatar feature'));
                return redirect()->back();
            } else {
                return view('user.avatar.index');
            }
        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function image(Request $request)
    {
        $avatars = $this->getAvatars();
        $voices = $this->getVoices();
        $favorites = AvatarFavorite::where('type', 'talking_photo')->pluck('avatar_id')->toArray();
        $favorite_voices = AvatarFavorite::where('type', 'voice')->pluck('avatar_id')->toArray();
        $backgrounds = AvatarAsset::where('user_id', auth()->user()->id)->where('asset_type', '<>', 'avatar')->where('file_type', 'image')->get();

        return view('user.avatar.image', compact('avatars', 'voices', 'favorites', 'backgrounds', 'favorite_voices'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function video(Request $request)
    {
        $avatars = Avatar::get();
        $voices = $this->getVoices();
        $favorites = AvatarFavorite::where('type', 'avatars')->pluck('avatar_id')->toArray();
        $favorite_voices = AvatarFavorite::where('type', 'voice')->pluck('avatar_id')->toArray();
        $backgrounds = AvatarAsset::where('user_id', auth()->user()->id)->where('asset_type', '<>', 'avatar')->where('file_type', 'image')->get();

        return view('user.avatar.video', compact('avatars', 'voices', 'favorites', 'backgrounds', 'favorite_voices'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateImage(Request $request)
    {
        if ($request->ajax()) {

            $check = \App\Services\HelperService::checkMediaCredits('heygen_image');

            if (!$check) {
                return response()->json(502);
            }

            $response = $this->createAvatarVideo($request, 'image');

            if (is_null($response['error'])) {
                $avatar = new AvatarResult([
                    'user_id' => auth()->user()->id,
                    'title' => $request->title,
                    'avatar_id' => $request->avatar_id,
                    'task' => 'talking_photo',
                    'status' => 'processing',
                    'public' => false,
                    'video_id' => $response['data']['video_id']
                ]);

                $avatar->save();

                \App\Services\HelperService::updateMediaBalance('heygen_image');

                return response()->json(200);

            } else {
                Log::info($response);
                return response()->json(500);
            }

        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateVideo(Request $request)
    {
        if ($request->ajax()) {

            $check = \App\Services\HelperService::checkMediaCredits('heygen_video');

            if (!$check) {
                return response()->json(502);
            }

            $response = $this->createAvatarVideo($request, 'video');

            $title = ($request->title) ? $request->title : 'Untitled Video';

            if (is_null($response['error'])) {
                $avatar = new AvatarResult([
                    'user_id' => auth()->user()->id,
                    'title' => $title,
                    'avatar_id' => $request->avatar_id,
                    'task' => 'avatar',
                    'status' => 'processing',
                    'public' => false,
                    'video_id' => $response['data']['video_id']
                ]);

                $avatar->save();

                \App\Services\HelperService::updateMediaBalance('heygen_video');

                return response()->json(200);

            } else {
                Log::info($response);
                return response()->json(500);
            }

        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        $this->checkResultStatus();

        $results = AvatarResult::where('status', '<>', 'failed')->where('user_id', auth()->user()->id)->get();

        return view('user.avatar.results', compact('results'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listImageAvatars(Request $request)
    {
        $avatarList = $this->getAvatars();

        $favorites = AvatarFavorite::where('type', 'talking_photo')->pluck('avatar_id')->toArray();

        return view('user.avatar.image-avatars', compact('avatarList', 'favorites'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createImageAvatar(Request $request)
    {
        return view('user.avatar.create-avatar');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadImageAvatar(Request $request)
    {
        if (request()->has('file')) {

            try {
                request()->validate([
                    'file' => 'required|image|mimes:jpeg,png,jpg'
                ]);

            } catch (\Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }

            $photo_name = request()->file('file')->getClientOriginalName();
            $photo_extension = request()->file('file')->getClientOriginalExtension();
            Storage::disk('audio')->put('avatar_assets/' . request()->file('file')->getClientOriginalName(), request()->file('file')->get());
            $path = Storage::disk('audio')->path('avatar_assets/' . $photo_name);

            $content_type = ($photo_extension == 'png') ? 'image/png' : 'image/jpeg';

            $url = 'https://upload.heygen.com/v1/talking_photo';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: ' . $content_type,
                'x-api-key:' . $this->api->heygen_api,
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($path));

            $result = curl_exec($ch);
            curl_close($ch);

            $list = json_decode($result, true);

            if ($list['code'] == 100) {
                $input = new AvatarAsset([
                    'user_id' => auth()->user()->id,
                    'talking_photo_id' => $list['data']['talking_photo_id'],
                    'talking_photo_url' => 'avatar_assets/' . $photo_name,
                    'asset_type' => 'avatar',
                    'original_name' => $photo_name,
                    'file_type' => 'image'
                ]);

                $input->save();

                return response()->json(200);
            } else {
                return response()->json(['status' => 400, 'message' => $list['message']]);
            }


        } else {
            return response()->json(['status' => 400, 'message' => __('Avatar photo is required')]);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listVoices(Request $request)
    {
        $voices = $this->getVoices();

        $favorites = AvatarFavorite::where('type', 'voice')->pluck('avatar_id')->toArray();

        return view('user.avatar.voices', compact('voices', 'favorites'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listVideoAvatars(Request $request)
    {
        $avatars = Avatar::get()->groupBy('group');

        return view('user.avatar.video-avatars', compact('avatars'));
    }


    public function showVideoAvatar(Request $request)
    {
        $avatars = Avatar::where('group', $request->name)->get();
        $name = ucfirst($request->name);

        $favorites = AvatarFavorite::where('type', 'avatars')->pluck('avatar_id')->toArray();

        return view('user.avatar.video-avatars-view', compact('avatars', 'name', 'favorites'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function favoriteImageAvatars(Request $request)
    {
        $avatar = AvatarFavorite::where('avatar_id', $request->id)->first();

        if ($avatar) {
            $avatar->delete();
            return response()->json('removed');
        } else {
            $favorite = new AvatarFavorite([
                'avatar_id' => $request->id,
                'type' => 'talking_photo'
            ]);

            $favorite->save();

            return response()->json('added');
        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function favoriteVideoAvatars(Request $request)
    {
        $avatar = AvatarFavorite::where('avatar_id', $request->id)->first();

        if ($avatar) {
            $avatar->delete();
            return response()->json('removed');
        } else {
            $favorite = new AvatarFavorite([
                'avatar_id' => $request->id,
                'type' => 'avatars'
            ]);

            $favorite->save();

            return response()->json('added');
        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function favoriteVoices(Request $request)
    {
        $avatar = AvatarFavorite::where('avatar_id', $request->id)->first();

        if ($avatar) {
            $avatar->delete();
            return response()->json('removed');
        } else {
            $favorite = new AvatarFavorite([
                'avatar_id' => $request->id,
                'type' => 'voice'
            ]);

            $favorite->save();

            return response()->json('added');
        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploads(Request $request)
    {
        $total = AvatarAsset::where('user_id', auth()->user()->id)->where('asset_type', '<>', 'avatar')->get();
        $audios = AvatarAsset::where('user_id', auth()->user()->id)->where('asset_type', '<>', 'avatar')->where('file_type', 'audio')->get();
        $images = AvatarAsset::where('user_id', auth()->user()->id)->where('asset_type', '<>', 'avatar')->where('file_type', 'image')->get();
        $videos = AvatarAsset::where('user_id', auth()->user()->id)->where('asset_type', '<>', 'avatar')->where('file_type', 'video')->get();

        return view('user.avatar.uploads', compact('audios', 'images', 'videos', 'total'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function processUpload(Request $request)
    {
        if (request()->has('file')) {

            $allowedTypes = ['jpg', 'jpeg', 'png', 'webm', 'mp4', 'mp3'];
            if (!in_array(Str::lower(request()->file('file')->getClientOriginalExtension()), $allowedTypes)) {
                return response()->json(['status' => 400, 'message' => __('Uploaded assets must be in one of the following formats: jpg, png, mp4, webm, mp3')]);
            }


            $photo_name = request()->file('file')->getClientOriginalName();
            $extension = request()->file('file')->getClientOriginalExtension();
            Storage::disk('audio')->put('avatar_assets/' . request()->file('file')->getClientOriginalName(), request()->file('file')->get());
            $path = Storage::disk('audio')->path('avatar_assets/' . $photo_name);

            $content_type = '';
            switch ($extension) {
                case 'png':
                    $content_type = 'image/png';
                    break;
                case 'jpg':
                case 'jpeg':
                    $content_type = 'image/jpeg';
                    break;
                case 'mp4':
                    $content_type = 'video/mp4';
                    break;
                case 'webm':
                    $content_type = 'video/webm';
                    break;
                case 'mp3':
                    $content_type = 'audio/mpeg';
                    break;
                default:
                    $content_type = 'image/png';
                    break;
            }

            $url = 'https://upload.heygen.com/v1/asset';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: ' . $content_type,
                'x-api-key:' . $this->api->heygen_api,
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($path));

            $result = curl_exec($ch);
            curl_close($ch);

            $list = json_decode($result, true);

            if ($list['code'] == 100) {
                \Log::info($list);
                $input = new AvatarAsset([
                    'user_id' => auth()->user()->id,
                    'asset_type' => 'asset',
                    'original_name' => $photo_name,
                    'file_id' => $list['data']['id'],
                    'file_name' => $list['data']['name'],
                    'file_type' => $list['data']['file_type'],
                    'file_url' => $list['data']['url']
                ]);

                $input->save();

                return response()->json(200);
            } else {
                return response()->json(['status' => 400, 'message' => $list['message']]);
            }


        } else {
            return response()->json(['status' => 400, 'message' => __('Select your asset file first')]);
        }
    }


    public function deleteResult(Request $request)
    {
        $result = AvatarResult::where('id', $request->id)->where('user_id', auth()->user()->id)->first();

        if ($result) {
            $url = 'https://api.heygen.com/v1/video.delete?video_id=' . $result->video_id;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'accept: application/json',
                'x-api-key:' . $this->api->heygen_api,
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            $result->delete();

            return response()->json(200);
        } else {
            return response()->json(500);
        }
    }


    private function getAvatars()
    {
        $url = 'https://api.heygen.com/v2/avatars';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'accept: application/json',
            'x-api-key:' . $this->api->heygen_api,
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $list = json_decode($result, true);

        return $list;
    }


    private function getVoices()
    {
        $url = 'https://api.heygen.com/v2/voices';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'accept: application/json',
            'x-api-key:' . $this->api->heygen_api,
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $list = json_decode($result, true);

        return $list;
    }


    public function createAvatarVideo(Request $request, $type = 'image')
    {

        $url = 'https://api.heygen.com/v2/video/generate';

        $title = ($request->title) ? $request->title : 'Untitled Video';

        if ($request->dimension == 'landscape') {
            $width = 1280;
            $height = 720;
        } else {
            $width = 720;
            $height = 1280;
        }

        if ($request->background_image == 'none' && is_null($request->background_image_url)) {
            $background = ["type" => "color", "value" => $request->background_color];
        } elseif ($request->background_image != 'none') {
            $background = ["type" => "image", "image_asset_id" => $request->background_image];
        } elseif (!is_null($request->background_image_url)) {
            $background = ["type" => "image", "url" => $request->background_image_url];
        }


        if ($type == 'image') {

            if ($request->talking_photo_style != 'none') {
                $data = [
                    "video_inputs" => [
                        [
                            "character" => [
                                "type" => "talking_photo",
                                "talking_photo_id" => $request->avatar_id,
                                "talking_photo_style" => $request->talking_photo_style,
                                "talking_style" => $request->talking_style,
                                "expression" => $request->expression
                            ],
                            "voice" => [
                                "type" => "text",
                                "input_text" => $request->text,
                                "voice_id" => $request->voice
                            ],
                            "background" => $background,
                        ]
                    ],
                    "title" => $title,
                    "caption" => false,
                    "dimension" => ["width" => $width, "height" => $height]
                ];

            } else {

                $data = [
                    "video_inputs" => [
                        [
                            "character" => [
                                "type" => "talking_photo",
                                "talking_photo_id" => $request->avatar_id,
                                "talking_style" => $request->talking_style,
                                "expression" => $request->expression
                            ],
                            "voice" => [
                                "type" => "text",
                                "input_text" => $request->text,
                                "voice_id" => $request->voice
                            ],
                            "background" => $background,
                        ]
                    ],
                    "title" => $title,
                    "caption" => false,
                    "dimension" => ["width" => $width, "height" => $height]
                ];
            }


        } else {

            $data = [
                "video_inputs" => [
                    [
                        "character" => [
                            "type" => "avatar",
                            "avatar_id" => $request->avatar_id,
                            "avatar_style" => $request->avatar_style
                        ],
                        "voice" => [
                            "type" => "text",
                            "input_text" => $request->text,
                            "voice_id" => $request->voice
                        ],
                        "background" => $background,
                    ]
                ],
                "title" => $title,
                "caption" => false,
                "dimension" => ["width" => $width, "height" => $height]
            ];
        }


        $data_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-Api-Key:' . $this->api->heygen_api,
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $reponse = json_decode($result, true);

        return $reponse;

    }


    private function checkResultStatus()
    {
        $results = AvatarResult::where('status', '<>', 'completed')->where('status', '<>', 'failed')->get();

        if ($results) {
            foreach ($results as $result) {
                $url = 'https://api.heygen.com/v1/video_status.get?video_id=' . $result->video_id;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'accept: application/json',
                    'x-api-key:' . $this->api->heygen_api,
                ));

                $ch_response = curl_exec($ch);
                curl_close($ch);

                $response = json_decode($ch_response, true);

                if ($response['code'] == 100) {
                    if ($response['data']['status'] == 'completed') {
                        $video = AvatarResult::where('video_id', $result->video_id)->first();
                        $video->duration = $response['data']['duration'];
                        $video->gif_url = $response['data']['gif_url'];
                        $video->status = $response['data']['status'];

                        $video_url_temp = file_get_contents($response['data']['video_url']);
                        $thumbnail_url_temp = file_get_contents($response['data']['thumbnail_url']);

                        $thumb_name = 'avatar-thumb-' . $result->video_id . '.png';
                        $video_name = 'avatar-video-' . $result->video_id . '.mp4';
                        Storage::disk('public')->put('images/avatar/' . $thumb_name, $thumbnail_url_temp);
                        Storage::disk('public')->put('images/avatar/' . $video_name, $video_url_temp);
                        $thumbnail_url = 'images/avatar/' . $thumb_name;
                        $video_url = 'images/avatar/' . $video_name;

                        $video->video_url = $video_url;
                        $video->thumbnail_url = $thumbnail_url;

                        $video->save();

                    } elseif ($response['data']['status'] == 'failed') {
                        $video = AvatarResult::where('video_id', $result->video_id)->first();
                        $video->status = $response['data']['status'];
                        $video->save();
                    }
                }
            }
        }

    }



}
