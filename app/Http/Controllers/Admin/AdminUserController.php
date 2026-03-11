<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Services\PaymentPlatformResolverService;
use App\Services\Statistics\UserRegistrationYearlyService;
use App\Services\Statistics\UserRegistrationMonthlyService;
use App\Services\Statistics\DavinciUsageService;
use App\Models\SubscriptionPlan;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\MainSetting;
use Carbon\Carbon;
use DataTables;
use Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\AddUser;
use App\Mail\AddCredits;
use Exception;

class AdminUserController extends Controller
{
    protected $paymentPlatformResolver;

    public function __construct(PaymentPlatformResolverService $paymentPlatformResolver)
    {
        $this->paymentPlatformResolver = $paymentPlatformResolver;
        $this->middleware('auth:web'); // Ensure admin is authenticated
    }

    /**
     * Display user management dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'month' => 'nullable|integer|min:1|max:12'
        ]);

        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $registration_yearly = new UserRegistrationYearlyService($year);
        $registration_monthly = new UserRegistrationMonthlyService($month);

        $user_data_year = [
            'total_free_tier' => $registration_yearly->getTotalFreeRegistrations(),
            'total_users' => $registration_yearly->getTotalUsers(),
            'top_countries' => $this->getTopCountries(),
        ];

        $chart_data['free_registration_yearly'] = json_encode($registration_yearly->getFreeRegistrations());
        $chart_data['current_registered_users'] = json_encode($registration_monthly->getRegisteredUsers());
        $chart_data['user_countries'] = json_encode($this->getAllCountries());

        $cachedUsers = json_decode(Cache::get('isOnline', '[]'), true);
        $users_online = count($cachedUsers);

        $users_today = User::whereNotNull('last_seen')->whereDate('last_seen', Carbon::today())->count();

        return view('admin.users.dashboard.index', compact('chart_data', 'user_data_year', 'users_online', 'users_today'));
    }

    /**
     * Display all users
     *
     * @return \Illuminate\Http\Response
     */
    public function listUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('id', 'profile_photo_path', 'name', 'email', 'created_at', 'status', 'group', 'country')->orderBy('created_at', 'DESC')->get();

            return Datatables::of($data)
                ->addColumn('actions', function($row){
                    $actionBtn = '<div>
                        <a href="'. route("admin.user.show", $row["id"] ). '"><i class="fa-solid fa-clipboard-user table-action-buttons view-action-button" title="'. e(__('View User')) .'"></i></a>
                        <a href="'. route("admin.user.edit", $row["id"] ). '"><i class="fa-solid fa-user-pen table-action-buttons edit-action-button" title="'. e(__('Edit User Group')) .'"></i></a>
                        <a class="deleteUserButton" id="'. e($row["id"]) .'" href="#"><i class="fa-solid fa-user-slash table-action-buttons delete-action-button" title="'. e(__('Delete User')) .'"></i></a>
                    </div>';
                    return $actionBtn;
                })
                ->addColumn('user', function($row){
                    $path = $row['profile_photo_path'] ? asset($row['profile_photo_path']) : theme_url('img/users/avatar.png');
                    $user = '<div class="d-flex">
                        <div class="widget-user-image-sm overflow-hidden mr-4"><img alt="Avatar" class="'. ($row['profile_photo_path'] ? '' : 'rounded-circle') .'" src="' . e($path) . '"></div>
                        <div class="widget-user-name"><span class="font-weight-bold">'. e($row['name']) .'</span> <br> <span class="text-muted">'. e($row["email"]) .'</span></div>
                    </div>';
                    return $user;
                })
                ->addColumn('created-on', function($row){
                    $created_on = '<span>'. e(date_format($row["created_at"], 'd/m/Y')) .'</span><br><span>'. e(date_format($row["created_at"], 'H:i A')) .'</span>';
                    return $created_on;
                })
                ->addColumn('custom-status', function($row){
                    $custom_status = $row["status"] ? '<span class="cell-box user-'. e($row["status"]) .'">'. e(ucfirst($row["status"])) .'</span>' : '';
                    return $custom_status;
                })
                ->addColumn('custom-group', function($row){
                    $custom_group = $row["group"] ? '<span class="cell-box user-group-'. e($row["group"]) .'">'. e(ucfirst($row["group"])) .'</span>' : '';
                    return $custom_group;
                })
                ->rawColumns(['actions', 'custom-status', 'custom-group', 'created-on', 'user'])
                ->make(true);
        }

        return view('admin.users.list.index');
    }

    /**
     * Display user activity
     *
     * @return \Illuminate\Http\Response
     */
    public function activity(Request $request)
    {
        $result = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id')
            ->select('sessions.ip_address', 'sessions.user_agent', 'sessions.last_activity', 'users.email', 'users.group')
            ->orderBy('sessions.last_activity', 'desc')
            ->paginate(50);

        foreach ($result as $session) {
            $session->ip_address = e($session->ip_address);
            $session->user_agent = e($session->user_agent);
            $session->email = e($session->email);
            $session->group = e($session->group);
        }

        return view('admin.users.activity.index', compact('result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.list.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'role' => 'required|string|in:admin,user,subscriber',
            'country' => 'nullable|string|max:255',
            'job_role' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'job_role' => $request->job_role,
            'phone_number' => $request->phone_number,
            'company' => $request->company,
            'website' => $request->website,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ]);

        $settings = MainSetting::firstOrFail();

        $user->syncRoles($request->role);
        $user->status = 'active';
        $user->group = $request->role;
        $user->email_verified_at = now();
        $user->referral_id = strtoupper(Str::random(15));
        $user->images = $settings->image_credits;
        $user->tokens = $settings->token_credits;
        $user->characters = config('settings.voiceover_welcome_chars');
        $user->minutes = config('settings.whisper_welcome_minutes');
        $user->default_voiceover_language = config('settings.voiceover_default_language');
        $user->default_voiceover_voice = config('settings.voiceover_default_voice');
        $user->save();

        try {
            Mail::to($user)->send(new AddUser($user->email, $request->password));
        } catch (Exception $e) {
            \Log::error('Failed to send user creation email: ' . $e->getMessage());
        }

        toastr()->success(__('Congratulation! New user has been created'));
        return redirect()->back();
    }

    /**
     * Display the details of selected user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {
        $request->validate([
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'month' => 'nullable|integer|min:1|max:12'
        ]);

        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $davinci = new DavinciUsageService($month, $year);

        $data = [
            'words' => $davinci->userTotalWordsGenerated($user->id),
            'images' => $davinci->userTotalImagesGenerated($user->id),
            'characters' => $davinci->userTotalCharactersSynthesized($user->id),
            'minutes' => $davinci->userTotalMinutesTranscribed($user->id),
        ];

        $chart_data['word_usage'] = json_encode($davinci->userMonthlyWordsChart($user->id));

        $subscription = Subscriber::where('status', 'Active')->where('user_id', $user->id)->first();
        if ($subscription && Carbon::parse($subscription->active_until)->isPast()) {
            $subscription = false;
        }

        $user_subscription = $subscription ? SubscriptionPlan::where('id', $user->plan_id)->first() ?: '' : '';

        $progress = [
            'words' => ($user->total_words > 0) ? (($user->available_words / $user->total_words) * 100) : 0,
        ];

        return view('admin.users.list.show', compact('user', 'data', 'chart_data', 'user_subscription', 'progress', 'subscription'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.list.edit', compact('user'));
    }

    /**
     * Show users credit capacity
     */
    public function credit(User $user)
    {
        return view('admin.users.list.increase', compact('user'));
    }

    /**
     * Show users subscription
     */
    public function subscription(User $user)
    {
        $plan = $user->plan_id ? SubscriptionPlan::where('id', $user->plan_id)->first()->plan_name ?? __('None') : __('None');
        $plans = SubscriptionPlan::orderBy('payment_frequency', 'DESC')->get();

        return view('admin.users.list.subscription', compact('user', 'plan', 'plans'));
    }

    /**
     * Change user credit capacity
     */
    public function increase(Request $request, User $user)
    {
        $request->validate([
            'tokens' => 'nullable|integer|min:0',
            'image-credits' => 'nullable|integer|min:0',
            'chars' => 'nullable|integer|min:0',
            'minutes' => 'nullable|numeric|min:0',
            'tokens-prepaid' => 'nullable|integer|min:0',
            'image-credits-prepaid' => 'nullable|integer|min:0',
            'chars_prepaid' => 'nullable|integer|min:0',
            'minutes_prepaid' => 'nullable|numeric|min:0'
        ]);

        $user->tokens = $request->input('tokens');
        $user->images = $request->input('image-credits');
        $user->characters = $request->input('chars');
        $user->minutes = $request->input('minutes');
        $user->tokens_prepaid = $request->input('tokens-prepaid');
        $user->images_prepaid = $request->input('image-credits-prepaid');
        $user->characters_prepaid = $request->input('chars_prepaid');
        $user->minutes_prepaid = $request->input('minutes_prepaid');
        $user->save();

        $words = $request->input('tokens') + $request->input('tokens-prepaid');
        $images = $request->input('image-credits') + $request->input('image-credits-prepaid');
        $minutes = $request->input('minutes') + $request->input('minutes_prepaid');
        $chars = $request->input('chars') + $request->input('chars_prepaid');

        try {
            Mail::to($user)->send(new AddCredits($words, $minutes, $chars, $images));
        } catch (Exception $e) {
            \Log::error('Failed to send credit update email: ' . $e->getMessage());
        }

        toastr()->success(__('Credits have been updated successfully'));
        return redirect()->back();
    }

    /**
     * Change user subscription
     */
    public function assignSubscription(Request $request, User $user)
    {
        $request->validate([
            'plan' => 'required|integer|exists:subscription_plans,id'
        ]);

        $plan = SubscriptionPlan::where('id', $request->plan)->firstOrFail();

        if ($user->plan_id == $request->plan) {
            toastr()->warning(__('User has already this plan assigned, select a different plan'));
            return redirect()->back();
        }

        $subscriber = Subscriber::where('status', 'Active')->where('user_id', $user->id)->first();
        if ($subscriber) {
            $this->stopSubscription($subscriber->id);
        }

        $subscription_id = strtoupper(Str::random(10));

        $days = match ($plan->payment_frequency) {
            'monthly' => 30,
            'yearly' => 365,
            'lifetime' => 18250,
            default => throw new \InvalidArgumentException('Invalid payment frequency: ' . $plan->payment_frequency),
        };

        Subscriber::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'Active',
            'created_at' => now(),
            'gateway' => 'Manual',
            'frequency' => $plan->payment_frequency,
            'plan_name' => $plan->plan_name,
            'tokens' => $plan->token_credits,
            'images' => $plan->image_credits,
            'characters' => $plan->characters,
            'minutes' => $plan->minutes,
            'subscription_id' => $subscription_id,
            'active_until' => Carbon::now()->addDays($days),
        ]);

        $group = $user->hasRole('admin') ? 'admin' : 'subscriber';
        $user->syncRoles($group);
        $user->group = $group;
        $user->plan_id = $plan->id;
        $user->images = $plan->image_credits;
        $user->tokens = $plan->token_credits;
        $user->characters = $plan->characters;
        $user->minutes = $plan->minutes;
        $user->member_limit = $plan->team_members;
        $user->save();

        toastr()->success(__('Subscription plan has been assigned successfully'));
        return redirect()->back();
    }

    /**
     * Update selected user data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'job_role' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
        ]);

        $user->update($validatedData);

        toastr()->success(__('User profile was successfully updated'));
        return redirect()->back();
    }

    /**
     * Change user group/status/password
     */
    public function change(Request $request, User $user)
    {
        $request->validate([
            'password' => ['nullable', 'confirmed', Rules\Password::min(8)],
            'status' => 'required|string|in:active,inactive,suspended',
            'group' => 'required|string|in:admin,user,subscriber',
            'twoFactor_status' => 'nullable|boolean'
        ]);

        if ($user->group) {
            $user->removeRole($user->group);
        }
        $user->assignRole($request->group);
        $user->status = $request->status;
        $user->group = $request->group;
        $user->google2fa_enabled = $request->boolean('twoFactor_status');
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        toastr()->success(__('User data was successfully updated'));
        return redirect()->back();
    }

    /**
     * Delete selected user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid request'], 400);
        }

        $request->validate([
            'id' => 'required|integer|exists:users,id'
        ]);

        try {
            $user = User::findOrFail($request->input('id'));
            $user->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete user'], 500);
        }
    }

    /**
     * Show list of all countries
     */
    public function getAllCountries()
    {
        $countries = User::select(DB::raw("count(id) as data, country"))
            ->groupBy('country')
            ->orderBy('data')
            ->pluck('data', 'country')
            ->mapWithKeys(function ($value, $key) {
                return [e($key) => $value];
            });

        return $countries;
    }

    /**
     * Show top 30 countries
     */
    public function getTopCountries()
    {
        $countries = User::select(DB::raw("count(id) as data, country"))
            ->groupBy('country')
            ->orderByDesc('data')
            ->pluck('data', 'country')
            ->take(30)
            ->mapWithKeys(function ($value, $key) {
                return [e($key) => $value];
            });

        return $countries;
    }

    /**
     * Cancel active subscription
     */
    public function stopSubscription($id)
    {
        $subscriber = Subscriber::findOrFail($id);

        if ($subscriber->status == 'Cancelled') {
            return ['status' => 200, 'message' => __('This subscription was already cancelled before')];
        } elseif ($subscriber->status == 'Suspended') {
            return ['status' => 400, 'message' => __('Subscription has been suspended due to failed renewal payment')];
        } elseif ($subscriber->status == 'Expired') {
            return ['status' => 400, 'message' => __('Subscription has been expired, please create a new one')];
        }

        $user = User::where('id', $subscriber->user_id)->firstOrFail();

        if ($subscriber->gateway == 'Manual' || $subscriber->gateway == 'FREE') {
            $subscriber->update(['status' => 'Cancelled', 'active_until' => now()]);
            $user->plan_id = null;
            $user->group = $user->hasRole('admin') ? 'admin' : 'user';
            $user->member_limit = null;
            $user->syncRoles($user->group);
            $user->save();
            return ['status' => 200, 'message' => __('Subscription has been successfully cancelled')];
        }

        $platformID = match ($subscriber->gateway) {
            'PayPal' => 1,
            'Stripe' => 2,
            'BankTransfer' => 3,
            'Paystack' => 4,
            'Razorpay' => 5,
            'Mollie' => 7,
            'Flutterwave' => 10,
            'Yookassa' => 11,
            'Paddle' => 12,
            'Manual', 'FREE' => 99,
            default => throw new \InvalidArgumentException('Invalid payment gateway: ' . $subscriber->gateway),
        };

        try {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService($platformID);
            $status = $paymentPlatform->stopSubscription($subscriber->subscription_id);

            $cancelled = match ($platformID) {
                2 => $status, // Stripe
                4 => $status->status ?? false, // Paystack
                5, 7, 10, 11, 12 => $status == 'cancelled' || $status->status == 'cancelled' || $status->status == 'Cancelled', // Razorpay, Mollie, Flutterwave, Yookassa, Paddle
                99 => true, // Manual or FREE
                default => !is_null($status),
            };

            if ($cancelled) {
                $subscriber->update(['status' => 'Cancelled', 'active_until' => now()]);
                $user->plan_id = null;
                $user->group = $user->hasRole('admin') ? 'admin' : 'user';
                $user->member_limit = null;
                $user->syncRoles($user->group);
                $user->save();
                return ['status' => 200, 'message' => __('Subscription has been successfully cancelled')];
            }

            return ['status' => 400, 'message' => __('Failed to cancel subscription with payment gateway')];
        } catch (\Exception $e) {
            \Log::error('Failed to cancel subscription: ' . $e->getMessage());
            return ['status' => 500, 'message' => __('Failed to cancel subscription due to an error')];
        }
    }

    public function hiddenPlans(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => 400, 'message' => 'Invalid request']);
        }

        $request->validate([
            'status' => 'required|string|in:true,false',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $user->hidden_plan = $request->status === 'true' ? 1 : 0;
            $user->save();
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to update hidden plan status']);
        }
    }
}