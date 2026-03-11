<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Package\InstallPackageService;
use Illuminate\Http\Request;
use App\Models\Extension;
use App\Models\Setting;
use DataTables;

class MarketplaceController extends Controller
{
    private $extensions;
    private $install;

    public function __construct()
    {
        $this->extensions = new ExtensionController();
        $this->install = new InstallPackageService();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $extensions = $this->extensions->extensions();
        $details = Extension::get();

        return view('admin.marketplace.index', compact('details', 'extensions'));
    }


    /**
     * Show Theme
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showExtension($slug)
    {   
        $theme = $this->extensions->search($slug);

        $extension = Extension::where('slug', $slug)->first();

        $tags = explode(',', $theme['tags']);

        $information_rows = ['license_type'];
        $information = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $information_rows)) {
                $information[$row['name']] = $row['value'];
            }
        }

        $type = $information['license_type'];


        $current_version = explode('v', config('app.version'));

        $approved = ((float)$theme['minimum_app_version'] > (float)$current_version[1]) ? false : true;
        $approved_version = $theme['minimum_app_version'];

        return view('admin.marketplace.checkout', compact('theme', 'extension', 'tags', 'type', 'approved', 'approved_version'));     
    }


    /**
     * Show Extension
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function purchaseExtension(Request $request, $slug)
    {   
        $theme = $this->extensions->search($slug);

        if ($theme['only_for_extended']) {
            toastr()->warning(__('This extension requires to have Extended License'));
            return redirect()->back();
        } 

        session()->put('name', $slug);
        session()->put('type', $request->type);
        session()->put('amount', $request->value); 
        session()->put('extension_name', $request->extension_name);

        return view('admin.marketplace.gateway');    
    }


    public function purchasePackage(Request $request, $slug)
    {   
        if ($slug == 'premier') {
            session()->put('name', $slug);
            session()->put('type', 'package');
            session()->put('amount', 999); 
            session()->put('extension_name', 'Premier Package Bundle');
        } elseif ($slug == 'support') {
            session()->put('name', $slug);
            session()->put('type', 'support');
            session()->put('amount', 299);
            session()->put('extension_name', 'Premium Support');
        }

        return view('admin.marketplace.gateway');    
    }


    /**
     * Install Extension
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function installExtension(Request $request, $slug)
    {   
        if ($request->ajax()) {

            $response = $this->install->install($slug);     
           
            return $response;  
        }
       
    }


    /**
     * Activate Extension
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activateExtension(Request $request, $slug)
    {   
        $theme = $this->extensions->checkPayment($slug);

        return redirect()->back();    
    }
    



}
