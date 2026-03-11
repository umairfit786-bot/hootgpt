<?php

namespace App\Http\Controllers\Admin\Extensions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtensionSetting;
use App\Models\ImageCredit;


class VideoTextSettingController extends Controller
{
    public function index()
    {
        $extension = ExtensionSetting::first();
        $cost = ImageCredit::first();

        return view('admin.davinci.configuration.extension.video-text.setting', compact('extension', 'cost'));
    }


    public function store(Request $request)
    {
        $this->storeValues(request('video_text_falai_api'), 'video_text_falai_api');
        $this->storeValues(request('video_text_openai_api'), 'video_text_openai_api');

        $this->storeCheckbox(request('video_text_feature'), 'video_text_feature');
        $this->storeCheckbox(request('video_text_free_tier'), 'video_text_free_tier');

        $credits = ImageCredit::first();
        $credits->update([
            "openai_sora_2_video" => request("openai_sora_2_video"),
            "openai_sora_2_pro_video" => request("openai_sora_2_pro_video"),
            "google_veo3_video" => request("google_veo3_video"),
            "kling_21_master_video" => request("kling_21_master_video"),
            "kling_15_video" => request("kling_15_video"),
            "haiper_2_video" => request("haiper_2_video"),
            "minimax_video" => request("minimax_video"),
            "mochi_1_video" => request("mochi_1_video"),
            "luma_dream_machine_video" => request("luma_dream_machine_video"),
            "hunyuan_video" => request("hunyuan_video"),
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


