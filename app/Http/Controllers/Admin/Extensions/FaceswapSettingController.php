<?php

namespace App\Http\Controllers\Admin\Extensions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtensionSetting;
use App\Models\ImageCredit;


class FaceswapSettingController extends Controller
{
    public function index()
    {
        $extension = ExtensionSetting::first();
        $cost = ImageCredit::first();

        return view('admin.davinci.configuration.extension.faceswap.setting', compact('extension', 'cost'));
    }


    public function store(Request $request)
    {
        $this->storeValues(request('faceswap_piapi_api'), 'faceswap_piapi_api');

        $this->storeCheckbox(request('faceswap_feature'), 'faceswap_feature');
        $this->storeCheckbox(request('faceswap_free_tier'), 'faceswap_free_tier');

        $credits = ImageCredit::first();
        $credits->update([
            "faceswap" => request("faceswap"),
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


