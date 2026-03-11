<?php

namespace App\Http\Controllers\Admin\Extensions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtensionSetting;
use App\Models\ImageCredit;


class VideoImageSettingController extends Controller
{
    public function index()
    {
        $extension = ExtensionSetting::first();
        $cost = ImageCredit::first();

        return view('admin.davinci.configuration.extension.video-image.setting', compact('extension', 'cost'));
    }


    public function store(Request $request)
    {
        $this->storeValues(request('video_image_stability_api'), 'video_image_stability_api');
        $this->storeValues(request('video_image_falai_api'), 'video_image_falai_api');

        $this->storeCheckbox(request('video_image_feature'), 'video_image_feature');
        $this->storeCheckbox(request('video_image_free_tier'), 'video_image_free_tier');

        $credits = ImageCredit::first();
        $credits->update([
            "kling_21_master_video_image" => request("kling_21_master_video_image"),
            "kling_21_pro_video_image" => request("kling_21_pro_video_image"),
            "kling_21_standard_video_image" => request("kling_21_standard_video_image"),
            "kling_15_video_image" => request("kling_15_video_image"),
            "luma_dream_machine_video_image" => request("luma_dream_machine_video_image"),
            "haiper_2_video_image" => request("haiper_2_video_image"),
            "stable_diffusion_video_image" => request("stable_diffusion_video_image"),
            "google_veo2_video_image" => request("google_veo2_video_image"),
        ]);

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();         
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


}


