<?php

namespace App\Http\Controllers\Admin\Extensions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtensionSetting;


class SpeechifyCloneController extends Controller
{
    public function index()
    {
        $extension = ExtensionSetting::first();

        return view('admin.davinci.configuration.extension.speechify.clone.setting', compact('extension'));
    }


    public function store(Request $request)
    {
        $this->storeCheckbox(request('speechify_clone_feature'), 'speechify_clone_feature');
        $this->storeCheckbox(request('speechify_clone_free_tier'), 'speechify_clone_free_tier');
        $this->storeValues(request('speechify_clone_api'), 'speechify_clone_api');
        $this->storeValues(request('speechify_clone_limit'), 'speechify_clone_limit');

        toastr()->success(__('Settings have been saved successfully'));
        return redirect()->back();         
    }


    private function storeValues($value, $field_name)
    {
        $settings = ExtensionSetting::first();
        $settings->update([
            $field_name => $value
        ]);
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


}


