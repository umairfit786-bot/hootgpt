<?php

namespace App\Http\Controllers\Admin\Extensions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtensionSetting;
use App\Models\ImageCredit;


class ChatShareController extends Controller
{
    public function index()
    {
        $extension = ExtensionSetting::first();

        return view('admin.davinci.configuration.extension.chat-share.setting', compact('extension'));
    }


    public function store(Request $request)
    {
        $this->storeCheckbox(request('chat_share_feature'), 'chat_share_feature');
        $this->storeCheckbox(request('chat_share_free_tier'), 'chat_share_free_tier');

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

}


