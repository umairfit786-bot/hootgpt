<?php

namespace App\Http\Controllers\Admin\Extensions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtensionSetting;
use App\Models\ImageCredit;


class AIPhotoStudioController extends Controller
{
    public function index()
    {
        $extension = ExtensionSetting::first();

        return view('admin.davinci.configuration.extension.photo-studio.setting', compact('extension'));
    }


    public function store(Request $request)
    {
        $this->storeValues(request('photo_studio_stability_api'), 'photo_studio_stability_api');

        $this->storeCheckbox(request('photo_studio_feature'), 'photo_studio_feature');
        $this->storeCheckbox(request('photo_studio_free_tier'), 'photo_studio_free_tier');

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();         
    }


    public function showCredits(Request $request)
    {
        $studio = ImageCredit::first();
        return view('admin.davinci.configuration.extension.photo-studio.sd', compact('studio'));
    }


    /**
     * Store photo studio costs in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCredits(Request $request)
    {
        $studio = ImageCredit::first();
        $studio->update([
            'sd_photo_studio_reimagine' => request('reimagine'),
            'sd_photo_studio_style' => request('style'),
            'sd_photo_studio_inpaint' => request('inpaint'), 
            'sd_photo_studio_search_replace' => request('search'), 
            'sd_photo_studio_outpaint' => request('outpaint'),
            'sd_photo_studio_erase_object' => request('erase'), 
            'sd_photo_studio_remove_background' => request('background'), 
            'sd_photo_studio_structure' => request('structure'),
            'sd_photo_studio_sketch' => request('sketch'), 
            'sd_photo_studio_creative_upscaler' => request('creative'), 
            'sd_photo_studio_conservative_upscaler' => request('conservative'), 
            'sd_photo_studio_text' => request('text'), 
        ]);

        toastr()->success(__('Photo Studio feature costs updated'));
        return redirect()->route('admin.davinci.configs.photo.studio.credits');
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


