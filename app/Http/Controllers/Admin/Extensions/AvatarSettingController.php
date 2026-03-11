<?php

namespace App\Http\Controllers\Admin\Extensions;

use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtensionSetting;
use App\Models\Avatar;


class AvatarSettingController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = ExtensionSetting::first();
    }

    public function index()
    {
        $extension = $this->api;

        return view('admin.davinci.configuration.extension.avatar.setting', compact('extension'));
    }


    public function store(Request $request)
    {
        request()->validate([
            'heygen_api' => 'required|string',
        ]);

        $this->storeCheckbox(request('heygen_avatar_feature'), 'heygen_avatar_feature');
        $this->storeCheckbox(request('heygen_avatar_free_tier'), 'heygen_avatar_free_tier');
        $this->storeCheckbox(request('heygen_avatar_video'), 'heygen_avatar_video');
        $this->storeCheckbox(request('heygen_avatar_image'), 'heygen_avatar_image');

        $this->storeValues(request('heygen_api'), 'heygen_api');
        $this->storeValues(request('heygen_avatar_video_numbers'), 'heygen_avatar_video_numbers');
        $this->storeValues(request('heygen_avatar_image_numbers'), 'heygen_avatar_image_numbers');
        $this->storeValues(request('heygen_image_credit_cost'), 'heygen_image_credit_cost');
        $this->storeValues(request('heygen_video_credit_cost'), 'heygen_video_credit_cost');

        Avatar::where('type', 'avatars')->delete();
        $status = $this->populateAvatars();

        if ($status) {
            toastr()->success(__('Avatar and Voices list were populated successfully'));
        } else {
            toastr()->error(__('Avatar and Voices list were not populated, make sure your Heygen API key is valid'));
        }

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();
    }


    private function populateAvatars()
    {
        $avatars = $this->listAvatars();

        if ($avatars) {
            if (is_null($avatars['error'])) {
                foreach ($avatars['data']['avatars'] as $avatar) {

                    $temp = explode('_', $avatar['avatar_id']);

                    if (count($temp) == 1) {
                        $temp = explode('-', $avatar['avatar_id']);
                    }

                    $name = Arr::first($temp);

                    $character = new Avatar([
                        'avatar_id' => $avatar['avatar_id'],
                        'avatar_name' => $avatar['avatar_name'],
                        'gender' => $avatar['gender'],
                        'preview_image_url' => $avatar['preview_image_url'],
                        'preview_video_url' => $avatar['preview_video_url'],
                        'type' => 'avatars',
                        'group' => $name,
                    ]);

                    $character->save();
                }

                return true;

            } else {
                \Log::info($avatars['error']);
                return false;
            }
        } else {
            return false;
        }
    }


    private function storeCheckbox($checkbox, $field_name)
    {
        if ($checkbox == 'on') {
            $status = true;
        } else {
            $status = false;
        }

        $settings = ExtensionSetting::first();
        $settings->update([
            $field_name => $status
        ]);
    }


    private function storeValues($value, $field_name)
    {
        $settings = ExtensionSetting::first();
        $settings->update([
            $field_name => $value
        ]);
    }


    private function listAvatars()
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


    private function listTemplates()
    {
        $url = 'https://api.heygen.com/v2/templates';

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


}


