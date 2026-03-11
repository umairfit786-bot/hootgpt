<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\Statistics\DavinciUsageService;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Models\SubscriptionPlan;
use App\Models\Subscriber;
use App\Models\Language;
use App\Models\MainSetting;
use App\Models\User;
use App\Models\GiftCard;
use App\Models\GiftCardUsage;
use App\Models\GiftCardTransfer;
use App\Mail\WalletSender;
use App\Mail\WalletReceiver;
use Carbon\Carbon;
use DataTables;
use Exception;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                         
        $request->validate([
            'year' => 'nullable|integer|min:2020|max:2030',
            'month' => 'nullable|integer|min:1|max:12'
        ]);
        
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        $user = auth()->user();

        $davinci = new DavinciUsageService($month, $year);

        $data = [
            'words' => $davinci->userTotalWordsGenerated(),
            'images' => $davinci->userTotalImagesGenerated(),
            'contents' => $davinci->userTotalContentsGenerated(),
            'synthesized' => $davinci->userTotalSynthesizedText(),
            'transcribed' => $davinci->userTotalTranscribedAudio(),
            'codes' => $davinci->userTotalCodesCreated(),
        ];
        
        $chart_data['word_usage'] = json_encode($davinci->userMonthlyWordsChart());
        $chart_data['image_usage'] = json_encode($davinci->userMonthlyImagesChart());
        
        $subscription = $this->getActiveSubscription($user->id);
        $user_subscription = ($subscription && $user->plan_id && SubscriptionPlan::find($user->plan_id)) ? SubscriptionPlan::find($user->plan_id) : null;
        $check_api_feature = ($user->plan_id && SubscriptionPlan::find($user->plan_id)) ? SubscriptionPlan::find($user->plan_id) : null;

        $progress = [
            'words' => ($user->total_words > 0) ? (($user->available_words / $user->total_words) * 100) : 0,
        ];

        return view('user.profile.index', compact('chart_data', 'data', 'subscription', 'user_subscription', 'progress', 'check_api_feature'));           
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {   
        $user = auth()->user();
        $check_api_feature = $user->plan_id ? SubscriptionPlan::find($user->plan_id) : null;
        $storage['available'] = $this->formatSize($user->storage_total * 1000000);

        return view('user.profile.edit', compact('storage', 'check_api_feature'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editDefaults($id = null)
    {   
        $user = auth()->user();
        $voiceoverVendors = $this->getVoiceoverVendors($user);
        $models = $this->getModels($user);
        $imageVendors = $this->getImageVendors($user);
        
        $languages = DB::table('voices')
            ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
            ->join('voiceover_languages', 'voices.language_code', '=', 'voiceover_languages.language_code')
            ->where('vendors.enabled', '1')
            ->where('voices.status', 'active')
            ->whereIn('voices.vendor', $voiceoverVendors)
            ->select('voiceover_languages.id', 'voiceover_languages.language', 'voices.language_code', 'voiceover_languages.language_flag')                
            ->distinct()
            ->orderBy('voiceover_languages.language', 'asc')
            ->get();

        $voices = DB::table('voices')
            ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
            ->where('vendors.enabled', '1')
            ->where('voices.status', 'active')
            ->whereIn('voices.vendor', $voiceoverVendors)
            ->orderBy('voices.voice_type', 'desc')
            ->orderBy('voices.voice', 'asc')
            ->get();

        $template_languages = Language::orderBy('languages.language', 'asc')->get();
        $check_api_feature = $user->plan_id ? SubscriptionPlan::find($user->plan_id) : null;
        $vendors = $imageVendors;

        return view('user.profile.default', compact('languages', 'voices', 'template_languages', 'check_api_feature', 'models', 'vendors'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {           
        $user = auth()->user();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user)],
            'job_role' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',            
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5048'
        ]);
        
        if ($request->hasFile('profile_photo')) {
            $image = $request->file('profile_photo');
            $name = Str::random(20);
            $folder = '/uploads/img/users/';
            
            // Use MIME type for security
            $mimeType = $image->getClientMimeType();
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (!in_array($mimeType, $allowedMimes)) {
                toastr()->error(__('Avatar image must be in png, jpeg or webp formats'));
                return redirect()->back();
            }
            
            $extension = $image->getClientOriginalExtension();
            $filePath = $folder . $name . '.' . $extension;
            
            $this->uploadImage($image, $folder, 'public', $name);
            $validatedData['profile_photo_path'] = $filePath;
        }
        
        $user->update($validatedData);

        toastr()->success(__('Profile Successfully Updated'));
        return redirect()->route('user.profile.edit', compact('user'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDefaults(Request $request)
    {           
        $user = auth()->user();
        $validatedData = $request->validate([
            'default_voiceover_voice' => 'nullable|string|max:255',
            'default_voiceover_language' => 'nullable|string|max:255',
            'default_template_language' => 'nullable|string|max:255',
            'default_model_template' => 'nullable|string|max:255',
            'default_model_chat' => 'nullable|string|max:255',
            'default_image_model' => 'nullable|string|max:255',
        ]);

        $user->update($validatedData);

        toastr()->success(__('Default settings successfully updated'));
        return redirect()->route('user.profile.defaults');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDelete($id = null)
    {   
        $user = auth()->user();
        $check_api_feature = $user->plan_id ? SubscriptionPlan::find($user->plan_id) : null;

        return view('user.profile.delete', compact('check_api_feature'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAPI()
    {   
        $user = auth()->user();
        $check_api_feature = $user->plan_id ? SubscriptionPlan::find($user->plan_id) : null;

        return view('user.profile.api', compact('check_api_feature'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAPI(Request $request)
    {           
        $validatedData = $request->validate([
            'openai-key' => 'nullable|string|max:255',
            'claude-key' => 'nullable|string|max:255',
            'gemini-key' => 'nullable|string|max:255',
            'sd-key' => 'nullable|string|max:255',
        ]);
        
        $user = auth()->user();
        $user->update([
            'personal_openai_key' => $validatedData['openai-key'] ?? null,
            'personal_claude_key' => $validatedData['claude-key'] ?? null,
            'personal_gemini_key' => $validatedData['gemini-key'] ?? null,
            'personal_sd_key' => $validatedData['sd-key'] ?? null,
        ]);

        toastr()->success(__('Your personal api keys have been saved successfully'));
        return redirect()->route('user.profile.api');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accountDelete(Request $request)
    {   
        $request->validate([
            'consent' => 'required|accepted'
        ]);
        
        if ($request->consent) {
            $user = auth()->user();
            $user->delete();

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            toastr()->success(__('Your account has been successfully deleted'));
            return redirect('/');
        } else {
            toastr()->warning(__('Please activate the checkbox to confirm account deletion'));
            return redirect()->back();
        }
    }


    /**
     * Upload user profile image
     */
    public function uploadImage(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);
        
        // Use MIME type for security
        $mimeType = $file->getClientMimeType();
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        
        if (!in_array($mimeType, $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid file type');
        }
        
        $extension = $file->getClientOriginalExtension();
        $image = $file->storeAs($folder, $name .'.'. $extension, $disk);

        return $image;
    }


    /**
     * Format storage space to readable format
     */
    private function formatSize($size, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $size = max($size, 0); 
        $pow = floor(($size ? log($size) : 0) / log(1000)); 
        $pow = min($pow, count($units) - 1); 
        
        $size /= pow(1000, $pow);

        return round($size, $precision) . $units[$pow]; 
    }


    public function updateReferral(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'value' => 'required|string|max:50|alpha_num'
            ]);

            $check = User::where('referral_id', $request->value)->first();

            if ($check) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('This Referral ID is already in use by another user, please enter another one')
                ]);
            } else {
                $user = auth()->user();
                $user->referral_id = $request->value;
                $user->save();
            }

            return response()->json([
                'status' => 'success'
            ]);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }


     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function themeSetting(Request $request)
    {           
        $request->validate([
            'theme' => 'required|string|in:light,dark'
        ]);
        
        $user = auth()->user();
        $user->update(['theme' => $request->theme]);
    }


    public function emailNewsletter(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'status' => 'required|string|in:true,false'
            ]);
   
            $status = ($request->status == 'true') ? 1 : 0;
            $user = auth()->user();
            $user->email_opt_in = $status;
            $user->save();

            return response()->json(['status' => 200]);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }


    public function showWallet(Request $request)
    {           
        if ($request->ajax()) {
            $userId = auth()->id();
            $data = GiftCardUsage::where('user_id', $userId)->orderBy('created_at', 'DESC')->get();        
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.e(date_format($row["created_at"], 'M d Y')).'</span><br><span class="text-muted">'.e(date_format($row["created_at"], 'H:i A')).'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-code', function($row){
                        $name = '<span class="font-weight-bold text-info">'.e($row['code']).'</span>';
                        return $name;
                    })
                    ->addColumn('custom-value', function($row){
                        $name = '<span class="font-weight-bold">'.e($row['amount']). e(config('payment.default_system_currency')) . '</span>';
                        return $name;
                    })
                    ->addColumn('custom-status', function($row){
                        $status = ($row['status']) ? 'redeemed' : 'failed';
                        $custom_priority = '<span class="cell-box gift-'.e(strtolower($status)).'">'.e(ucfirst($status)).'</span>';
                        return $custom_priority;
                    })
                    ->rawColumns(['custom-status', 'custom-code', 'created-on', 'custom-value'])
                    ->make(true);
        }

        $userId = auth()->id();
        $total = GiftCardUsage::where('user_id', $userId)->count();
        $totalAmount = GiftCardUsage::where('user_id', $userId)->sum('amount');
        $data = [
            'total' => $total,
            'amount' => $totalAmount
        ];

        return view('user.profile.wallet', compact('data'));  
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeWallet(Request $request)
    {           
        $request->validate([
            'code' => 'required|string|max:50'
        ]);

        
        $gift_code = GiftCard::where('code', $request->code)->first();

        if($gift_code) {
            if (!$gift_code->status) {
                toastr()->warning(__('This gift code is currently disabled, please use another one'));
                return redirect()->back();
            }

            if ($gift_code->valid_until->isPast()) {
                toastr()->warning(__('This gift code is already expired and cannot be used, please provide a valid one'));
                return redirect()->back();
            }

            if ($gift_code->usages_left == 0) {
                toastr()->warning(__('This gift code is already depleted, please provide a valid one'));
                return redirect()->back();
            }

            $user = auth()->user();
            if (!$gift_code->reusable) {
                $usage = GiftCardUsage::where('user_id', $user->id)->where('code', $request->code)->first();
                if($usage) {
                    toastr()->warning(__('You have already used this gift code, please provide a new one'));
                    return redirect()->back();
                }
            }

            DB::transaction(function () use ($gift_code, $user) {
                GiftCardUsage::create([
                    'user_id' => $user->id,
                    'username' => $user->name,
                    'email' => $user->email,
                    'code' => $gift_code->code,
                    'amount' => $gift_code->amount,
                    'status' => true
                ]);

                $user->increment('wallet', $gift_code->amount);
                $gift_code->decrement('usages_left');
            });

            toastr()->success(__('Gift code has been successfully redeemed!'));
            return redirect()->back();

        } else {
            toastr()->error(__('Invalid gift code provided, please provide a valid one'));
            return redirect()->back();
        }
    }


    public function transferWallet(Request $request)
    {        
        if ($request->ajax()) {   
            $request->validate([
                'email' => 'required|string|email|max:255',
                'amount' => 'required|numeric|min:1|max:999999'
            ]);

            $target_user = User::where('email', $request->email)->first();
            $user = auth()->user();

            if($target_user) {
                if ($target_user->id == $user->id) {
                    return response()->json([
                        'status' => 400,
                        'message' => __('You cannot transfer to yourself')
                    ]); 
                }

                if ($request->amount > $user->wallet) {
                    return response()->json([
                        'status' => 400,
                        'message' => __('You are trying to transfer more than what you have, make sure to set a lower value')
                    ]); 
                }

                DB::transaction(function () use ($target_user, $user, $request) {
                    $target_user->increment('wallet', $request->amount);
                    $user->decrement('wallet', $request->amount);

                    $transfer_id = strtoupper(Str::random(10));

                    GiftCardTransfer::create([
                        'sender_user_id' => $user->id,
                        'sender_username' => $user->name,
                        'sender_email' => $user->email,
                        'amount' => $request->amount,
                        'transfer_id' => $transfer_id,
                        'receiver_user_id' => $target_user->id,
                        'receiver_username' => $target_user->name,
                        'receiver_email' => $target_user->email,
                        'status' => true
                    ]);
                });

                try {
                    Mail::to($user)->send(new WalletSender($target_user, $request->amount, config('payment.default_system_currency')));
                    Mail::to($target_user)->send(new WalletReceiver($user, $request->amount, config('payment.default_system_currency')));
                } catch (Exception $e) {
                    Log::info('SMTP settings are not setup to send transfer statuses via email: '. $e->getMessage());
                }

                return response()->json([
                    'status' => 200,
                    'message' => __('You have successfully transferred funds to your friend!')
                ]); 
                
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => __('Looks like your friend did not yet register with us, let him know to sign up soon')
                ]); 
            }
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }


    public function transferList(Request $request)
    {
        if ($request->ajax()) {
            $userId = auth()->id();
            $data = GiftCardTransfer::where('sender_user_id', $userId)
                ->orWhere('receiver_user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->get();        
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.e(date_format($row["created_at"], 'M d Y')).'</span><br><span class="text-muted">'.e(date_format($row["created_at"], 'H:i A')).'</span>';
                        return $created_on;
                    })
                    ->addColumn('receiver', function($row){
                        $user = '<div class="d-flex">
                                <div class="widget-user-name"><span class="font-weight-bold">'. e($row['receiver_username']) .'</span> <br> <span class="text-muted">'.e($row["receiver_email"]).'</span></div>
                            </div>';                        
                        
                        return $user;
                    })
                    ->addColumn('sender', function($row){
                        $user = '<div class="d-flex">
                                <div class="widget-user-name"><span class="font-weight-bold">'. e($row['sender_username']) .'</span> <br> <span class="text-muted">'.e($row["sender_email"]).'</span></div>
                            </div>';                        
                        
                        return $user;
                    })
                    ->addColumn('custom-value', function($row){
                        $name = '<span class="font-weight-bold">'.e($row['amount']). e(config('payment.default_system_currency')) . '</span>';
                        return $name;
                    })
                    ->addColumn('custom-status', function($row){
                        $status = ($row['status']) ? 'transfered' : 'failed';
                        $custom_priority = '<span class="cell-box gift-'.e(strtolower($status)).'">'.e(ucfirst($status)).'</span>';
                        return $custom_priority;
                    })
                    ->rawColumns(['custom-status', 'created-on', 'custom-value', 'receiver', 'sender'])
                    ->make(true);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }
    
    /**
     * Get active subscription for user
     */
    private function getActiveSubscription($userId)
    {
        $subscription = Subscriber::where('status', 'Active')
            ->where('user_id', $userId)
            ->first();
            
        if ($subscription && Carbon::parse($subscription->active_until)->isPast()) {
            return false;
        }
        
        return $subscription ?: false;
    }
    
    /**
     * Get voiceover vendors for user
     */
    private function getVoiceoverVendors($user)
    {
        if (is_null($user->plan_id)) {
            return explode(', ', config('settings.voiceover_free_tier_vendors'));
        }
        
        $plan = SubscriptionPlan::find($user->plan_id);
        return $plan ? explode(', ', $plan->voiceover_vendors) : [];
    }
    
    /**
     * Get models for user
     */
    private function getModels($user)
    {
        if ($user->group == 'user') {
            return explode(',', config('settings.free_tier_models'));
        } elseif (!is_null($user->plan_id)) {
            $plan = SubscriptionPlan::find($user->plan_id);
            return $plan ? explode(',', $plan->model) : explode(',', config('settings.free_tier_models'));
        }
        
        return explode(',', config('settings.free_tier_models'));
    }
    
    /**
     * Get image vendors for user
     */
    private function getImageVendors($user)
    {
        if ($user->plan_id) {
            $plan = SubscriptionPlan::find($user->plan_id);
            if ($plan && !is_null($plan->image_vendors)) {
                return explode(',', $plan->image_vendors);
            }
        }
        
        $settings = MainSetting::first();
        if ($settings && !is_null($settings->image_vendors)) {
            return explode(',', $settings->image_vendors);
        }
        
        return ['openai'];
    }
}
