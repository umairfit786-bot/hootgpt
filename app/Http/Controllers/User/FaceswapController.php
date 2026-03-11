<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\HelperService;
use App\Models\SubscriptionPlan;
use App\Models\FaceswapResult;
use App\Models\ImageCredit;
use App\Models\User;
use App\Models\ExtensionSetting;
use DataTables;

class FaceswapController extends Controller
{

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {    

        $check = ExtensionSetting::first();
        $credits = ImageCredit::first();

        $results = FaceswapResult::where('user_id', auth()->user()->id)->orderBy('created_at','desc')->get();

        if (is_null(auth()->user()->plan_id)) {
            if (is_null($check->faceswap_free_tier) || !$check->faceswap_free_tier) {
                toastr()->warning(__('Faceswap feature is not available for free tier users, subscribe to get a proper access'));
                return redirect()->route('user.plans');
            } else {
                return view('user.faceswap.index', compact('credits', 'results'));
            }
        } else {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->faceswap_feature == false) {     
                toastr()->warning(__('Your current subscription plan does not include support for Faceswap feature'));
                return redirect()->back();                   
            } else {
                return view('user.faceswap.index', compact('credits', 'results'));
            }
        } 

    }


    /**
	*
	* Process Faceswap
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function create(Request $request) 
    {
        if ($request->ajax()) {

            $check = ExtensionSetting::first();

            if (is_null($check->faceswap_piapi_api) || $check->faceswap_piapi_api == '') {
                $data['status'] = 'error';
                $data['message'] = __('You must include your PiAPI API key first');
                return $data; 
            } 

            # Verify if user has enough credits
            $credit_status = $this->checkCredits();
            if (!$credit_status) {
                $data['status'] = 'error';
                $data['message'] = __('Not enough media credits to proceed, subscribe or top up your credit balance and try again');
                return $data;
            }

            $target_image_path = request()->file('target_image')->getRealPath();
            $target_image_extension = request()->file('target_image')->getClientOriginalExtension();

            $target_name = 'target-' . Str::random(10) . '.' . $target_image_extension;
            Storage::disk('audio')->put('faceswap/' . $target_name, file_get_contents($target_image_path));
            $target_url = asset('storage/faceswap/' . $target_name);

            $swap_image_path = request()->file('swap_image')->getRealPath();
            $swap_image_extension = request()->file('swap_image')->getClientOriginalExtension();

            $swap_name = 'swap-' . Str::random(10) . '.' . $swap_image_extension;
            Storage::disk('audio')->put('faceswap/' . $swap_name, file_get_contents($swap_image_path));
            $swap_url = asset('storage/faceswap/' . $swap_name);

            $headers = [
                'x-api-key' => $check->faceswap_piapi_api,
                'Content-Type' => 'application/json'
            ];
            
            $body = [
                "model" => "Qubico/image-toolkit",
                "task_type" => "face-swap",
                "input" => [
                    "target_image" => $target_url,
                    "swap_image" => $swap_url
                     
                ],
            ];
  
            
            $response = Http::withHeaders($headers)
                            ->post('https://api.piapi.ai/api/v1/task', $body);
    
            $response = json_decode($response->body() , true);

    
            if ($response['code'] == 200) {

                $result = '';

                do {
                    $result = $this->status($response['data']['task_id']);

                    sleep(2);

                } while ($result['data']['status'] == 'pending' || $result['data']['status'] == 'processing');
                    

                if ($result['data']['status'] == 'completed') {

                    $url = $result['data']['output']['image_url'];

                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    $contents = curl_exec($curl);
                    curl_close($curl);


                    $result_name = 'faceswap-' . Str::random(10) . '.png';
                    
                    
                    Storage::disk('audio')->put('faceswap/' . $result_name, $contents);
                    $result_url = Storage::disk('audio')->url('faceswap/' . $result_name);

                    # Update credit balance
                    $this->updateBalance();


                    $image = new FaceswapResult([
                        'user_id' => Auth::user()->id,
                        'title' => $request->title,
                        'target_image' => asset($target_url),
                        'swap_image' => asset($swap_url),
                        'result_image' => asset($result_url)
                    ]);

                    $image->save();

                    $image_box = ' <div class="col-md-6 col-sm-12">
                                                    <div class="card p-4 border-0">
                                                        <img src="'. asset($result_url).'" type="video/mp4">
                                                        <div class="text-center mt-3 relative">
                                                            <h6 class="mb-1 font-weight-semibold">'. $request->title .'</h6>
                                                            <p class="text-muted fs-12 mb-1">' . date('M d, Y') . '</p> 
                                                            <p class="text-muted fs-12 mb-0">('.__('Processing') .')</p> 
                                                            <a href="" class="avatar-result-delete" data-id="'. $image->id . '" data-tippy-content="'. __('Delete Swap Result') .'"><i class="fa-solid fa-trash-xmark"></i></a>                                                           
                                                        </div>
                                                    </div>
                                                </div>';

                    $data['status'] = 'success';
                    $data['result'] = $image_box;
                    $data['message'] = __('Faceswap task has been successfully created');                  
                    return $data; 
                } elseif ($result['data']['status'] == 'failed') {

                    $data['status'] = 'error';
                    $data['message'] = $result['data']['error']['message'];
                    return $data;
                }

            } else {
                $data['status'] = 'error';
                $data['message'] = $response['data']['error']['raw_message'];
                return $data;
            }

        }
	}


    public function status($task_id)
    {
        $setting = ExtensionSetting::first();

        $url = "https://api.piapi.ai/api/v1/task/" . $task_id;

        $response = Http::withHeaders([
            'x-api-key' => $setting->faceswap_piapi_api,
            'Content-Type' => 'application/json'
        ])->get($url);

        return json_decode($response->body() , true);
    }


    public function checkCredits()
    {
        $status = true;
        
        $status = HelperService::checkMediaCredits('faceswap');

        return $status;

    }


    /**
	*
	* Update user image balance
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function updateBalance() 
    {
        HelperService::updateMediaBalance('faceswap');
    }


    /**
	*
	* Delete File
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function delete(Request $request) 
    {
        if ($request->ajax()) {

            $image = FaceswapResult::where('id', request('id'))->first(); 

            if ($image->user_id == auth()->user()->id){

                $image->delete();

                return response()->json(200);
    
            } else{
                return response()->json(400);
            }  
        }
	}


    public function build_post_fields( $data,$existingKeys='',&$returnArray=[])
    {
        if(($data instanceof \CURLFile) or !(is_array($data) or is_object($data))){
            $returnArray[$existingKeys]=$data;
            return $returnArray;
        }
        else{
            foreach ($data as $key => $item) {
                $this->build_post_fields($item,$existingKeys?$existingKeys."[$key]":$key,$returnArray);
            }
            return $returnArray;
        }
    }


}
