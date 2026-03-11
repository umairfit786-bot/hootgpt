<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\ExtensionController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Exception\Report;
use App\Models\Setting;
use GuzzleHttp\Client;


class ActivationController extends Controller
{   
    protected $api;
    private $extensions;

    public function __construct()
    {
        $this->api = new LicenseController();
        $this->extensions = new ExtensionController();
    }

    /**
     * Dispaly activation index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notification = false;

        if (file_exists(base_path() . '/.lic') && filesize(base_path() . '/.lic') > 0) {
            $notification = true;
        }

        $information_rows = ['license', 'username', 'license_type'];
        $information = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $information_rows)) {
                $information[$row['name']] = $row['value'];
            }
        }

        if (isset($information['license_type']) && !is_null($information['license_type']) && !empty($information['license_type'])) {
            $type = $information['license_type'];
        } else {
            $type = 'No Valid License';            
        }

        return view('admin.settings.activation.index', compact('notification', 'information', 'type'));
    }


    /**
     * Store activation key
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'license' => 'required',
            'username' => 'required',
        ]);

        $status = $this->api->activate_license(request('license'), request('username'));

        if ($status['status'] == true) {

            $rows = ['license', 'username'];        
            foreach ($rows as $row) {
                Setting::where('name', $row)->update(['value' => $request->input($row)]);
            }

            Setting::where('name', 'license_type')->update(['value' => $status['data']]);

            toastr()->success(__('Application license was successfully activated'));
            return redirect()->back();
        } else {
            toastr()->error(__('There was an error while activating your application, please contact support team'));
            return redirect()->back();
        }
        
    }


    /**
     * Remove activation key and deactivate it
     *
     */
    public function destroy(Request $request)
    {
        if ($request->ajax()) {
            $verify = $this->api->deactivate_license(request('license'), request('username'));


            if ($verify['status']) {

                $rows = ['license', 'username'];        
                foreach ($rows as $row) {
                    Setting::where('name', $row)->update(['value' => '']);
                }

                Setting::where('name', 'license_type')->update(['value' => '']);

                return response()->json('success'); 
            } else {
                return response()->json('error'); 
            }
        }
    }


    /**
     * Hidden manual activation that is accessible only for admin group
     *
     */
    public function showManualActivation()
    {
        $information_rows = ['css', 'js'];
        $information = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $information_rows)) {
                $information[$row['name']] = $row['value'];
            }
        }

        return view('admin.settings.activation.manual', compact('information'));
    }


    /**
     * Store and activate via manual activation feature
     *
     */
    public function storeManualActivation(Request $request)
    {
        request()->validate([
            'license' => 'required',
            'username' => 'required',
        ]);

        $status = $this->api->activate_license(request('license'), request('username'));

        if ($status['status'] == true) {

            $rows = ['license', 'username'];        
            foreach ($rows as $row) {
                Setting::where('name', $row)->update(['value' => $request->input($row)]);
            }

            toastr()->success(__('Application license was successfully activated'));
            return redirect()->back();
        } else {
            toastr()->error(__('There was an error while activating your application, please contact support team'));
            return redirect()->back();
        }
    }


    /**
     * Record activation in .env
     */
    private function storeSettings($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $value, file_get_contents($path)
            ));

        }
    }

    public static function checkStatus() {
        $report = new Report();
        $status = $report->upload();
        if (isset($status['type'])) {
            if ($status['type'] == 'Extended License') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }  
    }


    public function showDownload()
    {

        return view('admin.settings.activation.download');
    }


    public function storeKey(Request $request)
    {
        request()->validate([
            'license' => 'required|string',
        ]);

        $status = $this->extensions->checkDownloadLicense($request->license);
        
        \Log::info($status);
        if ($status) {

            return response()->download(storage_path('app/update/update-6.1-main.zip'));

        } else {
            toastr()->error(__('Invalid activation code, make sure to provide correct one'));
            return redirect()->back();
        }
    }

}
