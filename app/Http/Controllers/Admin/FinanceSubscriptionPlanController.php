<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscriber;
use App\Models\FineTuneModel;
use App\Models\VendorPrice;
use App\Models\ChatCategory;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use DataTables;
use Exception;
use DB;

class FinanceSubscriptionPlanController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SubscriptionPlan::all()->sortByDesc("created_at");          
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>                                            
                                            <a href="'. route("admin.finance.plan.show", $row["id"] ). '"><i class="fa-solid fa-file-invoice-dollar table-action-buttons edit-action-button" title="'. __('View Plan') .'"></i></a>
                                            <a href="'. route("admin.finance.plan.edit", $row["id"] ). '"><i class="fa-solid fa-file-pen table-action-buttons view-action-button" title="'. __('Update Plan') .'"></i></a>
                                            <a href="'. route("admin.finance.plan.renew", $row["id"] ). '"><i class="fa-solid fa-box-check table-action-buttons view-action-button" title="'. __('Renew Credits') .'"></i></a>
                                            <a class="deletePlanButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark table-action-buttons delete-action-button" title="'. __('Delete Plan') .'"></i></a>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span class="text-muted">'.date_format($row["created_at"], 'M d, Y').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_priority = '<span class="cell-box plan-'.strtolower($row["status"]).'">'.ucfirst($row["status"]).'</span>';
                        return $custom_priority;
                    })
                    ->addColumn('frequency', function($row){
                        $custom_status = '<span class="cell-box payment-'.strtolower($row["payment_frequency"]).'">'.ucfirst($row["payment_frequency"]).'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-subscribers', function($row){
                        $value = $this->countSubscribers($row['id']);
                        $custom_storage = '<span class="text-muted">'.$value.'</span>';
                        return $custom_storage;
                    })
                    ->addColumn('custom-name', function($row){
                        $custom_name = '<span class="font-weight-bold">'.$row["plan_name"].'</span>';
                        return $custom_name;
                    })
                    ->addColumn('custom-price', function($row){
                        $custom_name = '<span class="text-muted">'.$row["price"] . ' ' . $row["currency"].'</span>';
                        return $custom_name;
                    })
                    ->addColumn('custom-featured', function($row){
                        $icon = ($row['featured'] == true) ? '<i class="fa-solid fa-circle-check text-success fs-16"></i>' : '<i class="fa-solid fa-circle-xmark fs-16"></i>';
                        $custom_featured = '<span class="font-weight-bold">'.$icon.'</span>';
                        return $custom_featured;
                    })
                    ->addColumn('custom-free', function($row){
                        $icon = ($row['free'] == true) ? '<i class="fa-solid fa-circle-check text-success fs-16"></i>' : '<i class="fa-solid fa-circle-xmark fs-16"></i>';
                        $custom_featured = '<span class="font-weight-bold">'.$icon.'</span>';
                        return $custom_featured;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-subscribers', 'frequency', 'custom-name', 'custom-featured', 'custom-free', 'custom-price'])
                    ->make(true);
                    
        }

        return view('admin.finance.plans.finance_subscription_index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $models = FineTuneModel::all();
        $prices = VendorPrice::first();
        $categories = Category::orderBy('name', 'asc')->get();
        $chat_categories = ChatCategory::orderBy('name', 'asc')->get();

        return view('admin.finance.plans.finance_subscription_create', compact('models', 'prices', 'categories', 'chat_categories'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'plan-status' => 'required',
            'plan-name' => 'required',
            'cost' => 'required|numeric',
            'currency' => 'required',
            'frequency' => 'required',
        ]);

        if (request('writer-feature') == 'on') {
            $writer = true; 
        } else {
            $writer = false;
        }

        if (request('voiceover-feature') == 'on') {
            $voiceover = true; 
        } else {
            $voiceover = false;
        }

        if (request('image-feature') == 'on') {
            $image = true; 
        } else {
            $image = false;
        }

        if (request('whisper-feature') == 'on') {
            $whisper = true; 
        } else {
            $whisper = false;
        }

        if (request('chat-feature') == 'on') {
            $chat = true; 
        } else {
            $chat = false;
        }

        if (request('code-feature') == 'on') {
            $code = true; 
        } else {
            $code = false;
        }

        if (request('personal-openai-api') == 'on') {
            $openai_personal = true; 
        } else {
            $openai_personal = false;
        }

        if (request('personal-claude-api') == 'on') {
            $claude_personal = true; 
        } else {
            $claude_personal = false;
        }

        if (request('personal-gemini-api') == 'on') {
            $gemini_personal = true; 
        } else {
            $gemini_personal = false;
        }

        if (request('personal-sd-api') == 'on') {
            $sd_personal = true; 
        } else {
            $sd_personal = false;
        }

        if (request('wizard-feature') == 'on') {
            $wizard = true; 
        } else {
            $wizard = false;
        }

        if (request('vision-feature') == 'on') {
            $vision = true; 
        } else {
            $vision = false;
        }

        if (request('chat-image-feature') == 'on') {
            $chat_image = true; 
        } else {
            $chat_image = false;
        }

        if (request('file-chat-feature') == 'on') {
            $file = true; 
        } else {
            $file = false;
        }

        if (request('internet-feature') == 'on') {
            $internet = true; 
        } else {
            $internet = false;
        }

        if (request('chat-web-feature') == 'on') {
            $web = true; 
        } else {
            $web = false;
        }

        if (request('smart-editor-feature') == 'on') {
            $smart = true; 
        } else {
            $smart = false;
        }

        if (request('rewriter-feature') == 'on') {
            $rewriter = true; 
        } else {
            $rewriter = false;
        }

        if (request('video-image-feature') == 'on') {
            $video_image = true; 
        } else {
            $video_image = false;
        }

        if (request('photo-studio-feature') == 'on') {
            $photo_studio = true; 
        } else {
            $photo_studio = false;
        }

        if (request('voice-clone-feature') == 'on') {
            $clone = true; 
        } else {
            $clone = false;
        }

        if (request('sound-studio-feature') == 'on') {
            $studio = true; 
        } else {
            $studio = false;
        }

        if (request('plagiarism-feature') == 'on') {
            $plagiarism = true; 
        } else {
            $plagiarism = false;
        }

        if (request('detector-feature') == 'on') {
            $detector = true; 
        } else {
            $detector = false;
        }

        if (request('personal-chat-feature') == 'on') {
            $personal_chat = true; 
        } else {
            $personal_chat = false;
        }

        if (request('personal-template-feature') == 'on') {
            $personal_template = true; 
        } else {
            $personal_template = false;
        }

        if (request('brand-voice-feature') == 'on') {
            $brand_voice = true; 
        } else {
            $brand_voice = false;
        }

        if (request('integration-feature') == 'on') {
            $integration = true; 
        } else {
            $integration = false;
        }

        if (request('youtube-feature') == 'on') {
            $youtube = true; 
        } else {
            $youtube = false;
        }

        if (request('rss-feature') == 'on') {
            $rss = true; 
        } else {
            $rss = false;
        }

        if (request('product-photo-feature') == 'on') {
            $product_photo = true; 
        } else {
            $product_photo = false;
        }

        if (request('wordpress-feature') == 'on') {
            $wordpress = true; 
        } else {
            $wordpress = false;
        }

        if (request('avatar_feature') == 'on') {
            $avatar = true; 
        } else {
            $avatar = false;
        }

        if (request('avatar_video_feature') == 'on') {
            $avatar_video = true; 
        } else {
            $avatar_video = false;
        }

        if (request('avatar_image_feature') == 'on') {
            $avatar_image = true; 
        } else {
            $avatar_image = false;
        }

        if (request('video-text-feature') == 'on') {
            $video_text = true; 
        } else {
            $video_text = false;
        }

        if (request('voice-isolator-feature') == 'on') {
            $voice_isolator = true; 
        } else {
            $voice_isolator = false;
        }

        if (request('video-video-feature') == 'on') {
            $video_video = true; 
        } else {
            $video_video = false;
        }

        if (request('faceswap-feature') == 'on') {
            $faceswap = true; 
        } else {
            $faceswap = false;
        }

        if (request('music-feature') == 'on') {
            $music = true; 
        } else {
            $music = false;
        }

        if (request('seo-feature') == 'on') {
            $seo = true; 
        } else {
            $seo = false;
        }

        if (request('social-media-feature') == 'on') {
            $social_media = true; 
        } else {
            $social_media = false;
        }

        if (request('chat-share-feature') == 'on') {
            $chat_share = true; 
        } else {
            $chat_share = false;
        }

        if (request('textract-feature') == 'on') {
            $textract = true; 
        } else {
            $textract = false;
        }

        if (request('chat_realtime_feature') == 'on') {
            $chat_realtime = true; 
        } else {
            $chat_realtime = false;
        }

        if (request('chatbot_external_feature') == 'on') {
            $chatbot_external = true; 
        } else {
            $chatbot_external = false;
        }

        if (request('team_member_feature') == 'on') {
            $team_member = true; 
        } else {
            $team_member = false;
        }

        if (request('speech_text_pro_feature') == 'on') {
            $speech_pro = true; 
        } else {
            $speech_pro = false;
        }

        if (request('telegram_feature') == 'on') {
            $telegram = true; 
        } else {
            $telegram = false;
        }

        if (request('whatsapp_feature') == 'on') {
            $whatsapp = true; 
        } else {
            $whatsapp = false;
        }

        if (request('speechify_voice_clone_feature') == 'on') {
            $speechify_clone = true; 
        } else {
            $speechify_clone = false;
        }

        if (request('chatbot_external_analytics_feature') == 'on') {
            $chatbot_external_analytics = true; 
        } else {
            $chatbot_external_analytics = false;
        }

        $voiceover_vendors = '';
        if (!is_null(request('voiceover_vendors'))) {
            foreach (request('voiceover_vendors') as $key => $value) {
                if ($key === array_key_last(request('voiceover_vendors'))) {
                    $voiceover_vendors .= $value; 
                } else {
                    $voiceover_vendors .= $value . ', '; 
                }                
            }
        }

        $template_models = '';
        if (!is_null(request('templates_models_list'))) {
            foreach (request('templates_models_list') as $key => $value) {
                if ($key === array_key_last(request('templates_models_list'))) {
                    $template_models .= $value; 
                } else {
                    $template_models .= $value . ', '; 
                }   
            }
        }

        $chat_models = '';
        if (!is_null(request('chats_models_list'))) {
            foreach (request('chats_models_list') as $key => $value) {
                if ($key === array_key_last(request('chats_models_list'))) {
                    $chat_models .= $value; 
                } else {
                    $chat_models .= $value . ', '; 
                }   
            }
        }

        $image_vendors = '';
        if (!is_null(request('image_vendors'))) {
            foreach (request('image_vendors') as $key => $value) {
                if ($key === array_key_last(request('image_vendors'))) {
                    $image_vendors .= $value; 
                } else {
                    $image_vendors .= $value . ', '; 
                }   
            }
        }

        $template_categories = '';
        if (!is_null(request('template_categories'))) {
            foreach (request('template_categories') as $key => $value) {
                if ($key === array_key_last(request('template_categories'))) {
                    $template_categories .= $value; 
                } else {
                    $template_categories .= $value . ', '; 
                }                
            }
        }

        $chat_categories = '';
        if (!is_null(request('chat_categories'))) {
            foreach (request('chat_categories') as $key => $value) {
                if ($key === array_key_last(request('chat_categories'))) {
                    $chat_categories .= $value; 
                } else {
                    $chat_categories .= $value . ', '; 
                }                
            }
        }


        try {
            $plan = new SubscriptionPlan([
                'paypal_gateway_plan_id' => request('paypal_gateway_plan_id'),
                'stripe_gateway_plan_id' => request('stripe_gateway_plan_id'),
                'paystack_gateway_plan_id' => request('paystack_gateway_plan_id'),
                'razorpay_gateway_plan_id' => request('razorpay_gateway_plan_id'),
                'flutterwave_gateway_plan_id' => request('flutterwave_gateway_plan_id'),
                'paddle_gateway_plan_id' => request('paddle_gateway_plan_id'),
                'status' => request('plan-status'),
                'plan_name' => request('plan-name'),
                'price' => request('cost'),
                'currency' => request('currency'),
                'free' => request('free-plan'),
                'image_feature' => $image,
                'voiceover_feature' => $voiceover,
                'transcribe_feature' => $whisper,
                'chat_feature' => $chat,
                'code_feature' => $code,
                'templates' => request('templates'),
                'chats' => request('chats'),
                'characters' => request('characters'),
                'minutes' => request('minutes'),
                'payment_frequency' => request('frequency'),
                'primary_heading' => request('primary-heading'),
                'featured' => request('featured'),
                'plan_features' => request('features'),
                'max_tokens' => request('tokens'),
                'model' => $template_models,
                'model_chat' => $chat_models,
                'team_members' => request('team-members'),
                'personal_openai_api' => $openai_personal,
                'personal_claude_api' => $claude_personal,
                'personal_gemini_api' => $gemini_personal,
                'personal_sd_api' => $sd_personal,
                'days' => request('days'),
                'wizard_feature' => $wizard,
                'writer_feature' => $writer,
                'vision_feature' => $vision,
                'internet_feature' => $internet,
                'chat_image_feature' => $chat_image,
                'file_chat_feature' => $file,
                'chat_web_feature' => $web,
                'chat_csv_file_size' => request('chat-csv-file-size'),
                'chat_pdf_file_size' => request('chat-pdf-file-size'),
                'chat_word_file_size' => request('chat-word-file-size'),                
                'rewriter_feature' => $rewriter,
                'smart_editor_feature' => $smart,            
                'personal_chats_feature' => $personal_chat,
                'personal_templates_feature' => $personal_template,
                'voiceover_vendors' => $voiceover_vendors,
                'brand_voice_feature' => $brand_voice,
                'file_result_duration' => request('file-result-duration'),
                'document_result_duration' => request('document-result-duration'),
                'image_credits' => request('image-credits'),
                'image_vendors' => $image_vendors,                   
                'youtube_feature' => $youtube,
                'rss_feature' => $rss,
                'integration_feature' => $integration,                
                'plagiarism_feature' => $plagiarism,
                'ai_detector_feature' => $detector,
                'plagiarism_pages' => request('plagiarism-pages'),
                'ai_detector_pages' => request('detector-pages'),
                'video_image_feature' => $video_image,
                'photo_studio_feature' => $photo_studio,
                'voice_clone_feature' => $clone,
                'voice_clone_number' => request('voice_clone_number'),
                'sound_studio_feature' => $studio,
                'product_photo_feature' => $product_photo,
                'wordpress_feature' => $wordpress,
                'wordpress_website_number' => request('wordpress-website-number'),
                'wordpress_post_number' => request('wordpress-post-number'),
                'avatar_feature' => $avatar,
                'avatar_video_feature' => $avatar_video,
                'avatar_image_feature' => $avatar_image,
                'avatar_video_numbers' => request('avatar_video_numbers'),
                'avatar_image_numbers' => request('avatar_image_numbers'),
                'video_text_feature' => $video_text,
                'voice_isolator_feature' => $voice_isolator,
                'video_video_feature' => $video_video,
                'faceswap_feature' => $faceswap,
                'music_feature' => $music,
                'seo_feature' => $seo,
                'social_media_feature' => $social_media,
                'token_credits' => request('token-credits'),
                'chat_share_feature' => $chat_share,
                'textract_feature' => $textract,
                'chat_realtime_feature' => $chat_realtime,
                'chatbot_external_feature' => $chatbot_external,
                'chatbot_external_quantity' => request('chatbot_external_quantity'),
                'chatbot_external_domains' => request('chatbot_external_domains'),
                'team_member_feature' => $team_member,
                'speech_text_pro_feature' => $speech_pro,
                'telegram_feature' => $telegram,
                'whatsapp_feature' => $whatsapp,
                'whatsapp_total_bots' => request('whatsapp_total_bots'),
                'telegram_total_bots' => request('telegram_total_bots'),
                'speechify_voice_clone_feature' => $speechify_clone,
                'speechify_voice_clone_number' => request('speechify_voice_clone_number'),
                'chatbot_external_analytics_feature' => $chatbot_external_analytics,
                'template_categories' => $template_categories,
                'chat_categories' => $chat_categories,
            ]); 
                   
            $plan->save();            
    
            toastr()->success(__('New subscription plan has been created successfully'));
            return redirect()->route('admin.finance.plans');

        } catch (Exception $e) {
            toastr()->error($e->getMessage());
            return redirect()->back();
        }

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SubscriptionPlan $id)
    {
        return view('admin.finance.plans.finance_subscription_show', compact('id'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SubscriptionPlan $id)
    {
        $models = FineTuneModel::all();
        $vendors = array_map('trim', explode(',', $id->voiceover_vendors));
        $model_templates = array_map('trim', explode(',', $id->model));
        $model_chats = array_map('trim', explode(',', $id->model_chat));
        $images = array_map('trim', explode(',', $id->image_vendors));
        $included_template_cat = array_map('trim', explode(',', $id->template_categories));
        $included_chat_cat = array_map('trim', explode(',', $id->chat_categories));
        \Log::info($included_template_cat);

        $categories = Category::orderBy('name', 'asc')->get();
        $chat_categories = ChatCategory::orderBy('name', 'asc')->get();
        $prices = VendorPrice::first();

        return view('admin.finance.plans.finance_subscription_edit', compact('id', 'included_template_cat', 'included_chat_cat', 'models', 'vendors', 'model_templates', 'model_chats', 'prices', 'images', 'categories', 'chat_categories'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriptionPlan $id)
    {
        request()->validate([
            'plan-status' => 'required',
            'plan-name' => 'required',
            'cost' => 'required|numeric',
            'currency' => 'required',
            'frequency' => 'required',
        ]);

        if (request('writer-feature') == 'on') {
            $writer = true; 
        } else {
            $writer = false;
        }

        if (request('voiceover-feature') == 'on') {
            $voiceover = true; 
        } else {
            $voiceover = false;
        }

        if (request('image-feature') == 'on') {
            $image = true; 
        } else {
            $image = false;
        }

        if (request('whisper-feature') == 'on') {
            $whisper = true; 
        } else {
            $whisper = false;
        }

        if (request('chat-feature') == 'on') {
            $chat = true; 
        } else {
            $chat = false;
        }

        if (request('code-feature') == 'on') {
            $code = true; 
        } else {
            $code = false;
        }

        if (request('personal-openai-api') == 'on') {
            $openai_personal = true; 
        } else {
            $openai_personal = false;
        }

        if (request('personal-claude-api') == 'on') {
            $claude_personal = true; 
        } else {
            $claude_personal = false;
        }

        if (request('personal-gemini-api') == 'on') {
            $gemini_personal = true; 
        } else {
            $gemini_personal = false;
        }

        if (request('personal-sd-api') == 'on') {
            $sd_personal = true; 
        } else {
            $sd_personal = false;
        }

        if (request('wizard-feature') == 'on') {
            $wizard = true; 
        } else {
            $wizard = false;
        }

        if (request('vision-feature') == 'on') {
            $vision = true; 
        } else {
            $vision = false;
        }

        if (request('chat-image-feature') == 'on') {
            $chat_image = true; 
        } else {
            $chat_image = false;
        }

        if (request('file-chat-feature') == 'on') {
            $file = true; 
        } else {
            $file = false;
        }

        if (request('internet-feature') == 'on') {
            $internet = true; 
        } else {
            $internet = false;
        }

        if (request('chat-web-feature') == 'on') {
            $web = true; 
        } else {
            $web = false;
        }

        if (request('smart-editor-feature') == 'on') {
            $smart = true; 
        } else {
            $smart = false;
        }

        if (request('rewriter-feature') == 'on') {
            $rewriter = true; 
        } else {
            $rewriter = false;
        }

        if (request('video-image-feature') == 'on') {
            $video_image = true; 
        } else {
            $video_image = false;
        }

        if (request('photo-studio-feature') == 'on') {
            $photo_studio = true; 
        } else {
            $photo_studio = false;
        }

        if (request('voice-clone-feature') == 'on') {
            $clone = true; 
        } else {
            $clone = false;
        }

        if (request('sound-studio-feature') == 'on') {
            $studio = true; 
        } else {
            $studio = false;
        }

        if (request('plagiarism-feature') == 'on') {
            $plagiarism = true; 
        } else {
            $plagiarism = false;
        }

        if (request('detector-feature') == 'on') {
            $detector = true; 
        } else {
            $detector = false;
        }

        if (request('personal-chat-feature') == 'on') {
            $personal_chat = true; 
        } else {
            $personal_chat = false;
        }

        if (request('personal-template-feature') == 'on') {
            $personal_template = true; 
        } else {
            $personal_template = false;
        }

        if (request('brand-voice-feature') == 'on') {
            $brand_voice = true; 
        } else {
            $brand_voice = false;
        }

        if (request('integration-feature') == 'on') {
            $integration = true; 
        } else {
            $integration = false;
        }

        if (request('youtube-feature') == 'on') {
            $youtube = true; 
        } else {
            $youtube = false;
        }

        if (request('rss-feature') == 'on') {
            $rss = true; 
        } else {
            $rss = false;
        }

        if (request('product-photo-feature') == 'on') {
            $product_photo = true; 
        } else {
            $product_photo = false;
        }

        if (request('wordpress-feature') == 'on') {
            $wordpress = true; 
        } else {
            $wordpress = false;
        }

        if (request('avatar_feature') == 'on') {
            $avatar = true; 
        } else {
            $avatar = false;
        }

        if (request('avatar_video_feature') == 'on') {
            $avatar_video = true; 
        } else {
            $avatar_video = false;
        }

        if (request('avatar_image_feature') == 'on') {
            $avatar_image = true; 
        } else {
            $avatar_image = false;
        }

        if (request('video-text-feature') == 'on') {
            $video_text = true; 
        } else {
            $video_text = false;
        }

        if (request('voice-isolator-feature') == 'on') {
            $voice_isolator = true; 
        } else {
            $voice_isolator = false;
        }

        if (request('video-video-feature') == 'on') {
            $video_video = true; 
        } else {
            $video_video = false;
        }

        if (request('faceswap-feature') == 'on') {
            $faceswap = true; 
        } else {
            $faceswap = false;
        }

        if (request('music-feature') == 'on') {
            $music = true; 
        } else {
            $music = false;
        }

        if (request('seo-feature') == 'on') {
            $seo = true; 
        } else {
            $seo = false;
        }

        if (request('social-media-feature') == 'on') {
            $social_media = true; 
        } else {
            $social_media = false;
        }

        if (request('chat-share-feature') == 'on') {
            $chat_share = true; 
        } else {
            $chat_share = false;
        }

        if (request('textract-feature') == 'on') {
            $textract = true; 
        } else {
            $textract = false;
        }

        if (request('chat_realtime_feature') == 'on') {
            $chat_realtime = true; 
        } else {
            $chat_realtime = false;
        }

        if (request('chatbot_external_feature') == 'on') {
            $chatbot_external = true; 
        } else {
            $chatbot_external = false;
        }

        if (request('team_member_feature') == 'on') {
            $team_member = true; 
        } else {
            $team_member = false;
        }

        if (request('speech_text_pro_feature') == 'on') {
            $speech_pro = true; 
        } else {
            $speech_pro = false;
        }

        if (request('telegram_feature') == 'on') {
            $telegram = true; 
        } else {
            $telegram = false;
        }

        if (request('whatsapp_feature') == 'on') {
            $whatsapp = true; 
        } else {
            $whatsapp = false;
        }

        if (request('speechify_voice_clone_feature') == 'on') {
            $speechify_clone = true; 
        } else {
            $speechify_clone = false;
        }

        if (request('chatbot_external_analytics_feature') == 'on') {
            $chatbot_external_analytics = true; 
        } else {
            $chatbot_external_analytics = false;
        }

        $voiceover_vendors = '';
        if (!is_null(request('voiceover_vendors'))) {
            foreach (request('voiceover_vendors') as $key => $value) {
                if ($key === array_key_last(request('voiceover_vendors'))) {
                    $voiceover_vendors .= $value; 
                } else {
                    $voiceover_vendors .= $value . ', '; 
                }                
            }
        }

        $template_models = '';
        if (!is_null(request('templates_models_list'))) {
            foreach (request('templates_models_list') as $key => $value) {
                if ($key === array_key_last(request('templates_models_list'))) {
                    $template_models .= $value; 
                } else {
                    $template_models .= $value . ', '; 
                }   
            }
        }

        $chat_models = '';
        if (!is_null(request('chats_models_list'))) {
            foreach (request('chats_models_list') as $key => $value) {
                if ($key === array_key_last(request('chats_models_list'))) {
                    $chat_models .= $value; 
                } else {
                    $chat_models .= $value . ', '; 
                }   
            }
        }

        $image_vendors = '';
        if (!is_null(request('image_vendors'))) {
            foreach (request('image_vendors') as $key => $value) {
                if ($key === array_key_last(request('image_vendors'))) {
                    $image_vendors .= $value; 
                } else {
                    $image_vendors .= $value . ', '; 
                }   
            }
        }

        $template_categories = '';
        if (!is_null(request('template_categories'))) {
            foreach (request('template_categories') as $key => $value) {
                if ($key === array_key_last(request('template_categories'))) {
                    $template_categories .= $value; 
                } else {
                    $template_categories .= $value . ', '; 
                }                
            }
        }

        $chat_categories = '';
        if (!is_null(request('chat_categories'))) {
            foreach (request('chat_categories') as $key => $value) {
                if ($key === array_key_last(request('chat_categories'))) {
                    $chat_categories .= $value; 
                } else {
                    $chat_categories .= $value . ', '; 
                }                
            }
        }

        try {

            $id->update([
                'paypal_gateway_plan_id' => request('paypal_gateway_plan_id'),
                'stripe_gateway_plan_id' => request('stripe_gateway_plan_id'),
                'paystack_gateway_plan_id' => request('paystack_gateway_plan_id'),
                'razorpay_gateway_plan_id' => request('razorpay_gateway_plan_id'),
                'flutterwave_gateway_plan_id' => request('flutterwave_gateway_plan_id'),
                'paddle_gateway_plan_id' => request('paddle_gateway_plan_id'),
                'status' => request('plan-status'),
                'plan_name' => request('plan-name'),
                'price' => request('cost'),
                'currency' => request('currency'),
                'free' => request('free-plan'),
                'characters' => request('characters'),
                'minutes' => request('minutes'),
                'payment_frequency' => request('frequency'),
                'primary_heading' => request('primary-heading'),
                'featured' => request('featured'),
                'plan_features' => request('features'),
                'image_feature' => $image,
                'voiceover_feature' => $voiceover,
                'transcribe_feature' => $whisper,
                'chat_feature' => $chat,
                'code_feature' => $code,
                'templates' => request('templates'),
                'chats' => request('chats'),
                'max_tokens' => request('tokens'),
                'model' => $template_models,
                'model_chat' => $chat_models,
                'team_members' => request('team-members'),
                'personal_openai_api' => $openai_personal,
                'personal_claude_api' => $claude_personal,
                'personal_gemini_api' => $gemini_personal,
                'personal_sd_api' => $sd_personal,
                'days' => request('days'),
                'wizard_feature' => $wizard,
                'writer_feature' => $writer,
                'vision_feature' => $vision,
                'internet_feature' => $internet,
                'chat_image_feature' => $chat_image,
                'file_chat_feature' => $file,
                'chat_web_feature' => $web,
                'chat_csv_file_size' => request('chat-csv-file-size'),
                'chat_pdf_file_size' => request('chat-pdf-file-size'),
                'chat_word_file_size' => request('chat-word-file-size'),
                'voice_clone_number' => request('voice_clone_number'),
                'rewriter_feature' => $rewriter,
                'smart_editor_feature' => $smart,
                'video_image_feature' => $video_image,
                'photo_studio_feature' => $photo_studio,
                'voice_clone_feature' => $clone,
                'sound_studio_feature' => $studio,
                'plagiarism_feature' => $plagiarism,
                'ai_detector_feature' => $detector,
                'plagiarism_pages' => request('plagiarism-pages'),
                'ai_detector_pages' => request('detector-pages'),
                'personal_chats_feature' => $personal_chat,
                'personal_templates_feature' => $personal_template,
                'voiceover_vendors' => $voiceover_vendors,
                'brand_voice_feature' => $brand_voice,
                'file_result_duration' => request('file-result-duration'),
                'document_result_duration' => request('document-result-duration'),
                'image_credits' => request('image-credits'),
                'image_vendors' => $image_vendors,               
                'integration_feature' => $integration,
                'youtube_feature' => $youtube,
                'rss_feature' => $rss,
                'product_photo_feature' => $product_photo,
                'wordpress_feature' => $wordpress,
                'wordpress_website_number' => request('wordpress-website-number'),
                'wordpress_post_number' => request('wordpress-post-number'),
                'avatar_feature' => $avatar,
                'avatar_video_feature' => $avatar_video,
                'avatar_image_feature' => $avatar_image,
                'avatar_video_numbers' => request('avatar_video_numbers'),
                'avatar_image_numbers' => request('avatar_image_numbers'),
                'video_text_feature' => $video_text,
                'voice_isolator_feature' => $voice_isolator,
                'video_video_feature' => $video_video,
                'faceswap_feature' => $faceswap,
                'music_feature' => $music,
                'seo_feature' => $seo,
                'social_media_feature' => $social_media,
                'token_credits' => request('token-credits'),
                'chat_share_feature' => $chat_share,
                'textract_feature' => $textract,
                'chat_realtime_feature' => $chat_realtime,
                'chatbot_external_feature' => $chatbot_external,
                'chatbot_external_quantity' => request('chatbot_external_quantity'),
                'chatbot_external_domains' => request('chatbot_external_domains'),
                'team_member_feature' => $team_member,
                'speech_text_pro_feature' => $speech_pro,
                'telegram_feature' => $telegram,
                'whatsapp_feature' => $whatsapp,
                'whatsapp_total_bots' => request('whatsapp_total_bots'),
                'telegram_total_bots' => request('telegram_total_bots'),
                'speechify_voice_clone_feature' => $speechify_clone,
                'speechify_voice_clone_number' => request('speechify_voice_clone_number'),
                'chatbot_external_analytics_feature' => $chatbot_external_analytics,
                'template_categories' => $template_categories,
                'chat_categories' => $chat_categories,
            ]); 
            
            toastr()->success(__('Selected plan has been updated successfully'));
            return redirect()->route('admin.finance.plans');
            
        } catch (Exception $e) {
            toastr()->error($e->getMessage());
            return redirect()->back();
        }
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if ($request->ajax()) {

            $plan = SubscriptionPlan::find(request('id'));

            if($plan) {

                $subscribers = $this->countSubscribers($plan->id);

                if ($subscribers != 0) {

                    return response()->json(['status' => 'error', 'message' => __('Plan with active subscribers cannot be deleted, you can either deactive or hide the plan, or first unsubscribe all users from this plan before deleting it. This subscription plan has ') . $subscribers . __(' active subscribers')]);

                } else {

                    $plan->delete();
                    return response()->json(['status' => 'success']);
                }

            } else{
                return response()->json('error');
            } 
        }
    }


    public function countSubscribers($id)
    {
        $total_voiceover = Subscriber::select(DB::raw("count(id) as data"))
                ->where('plan_id', $id)
                ->where('status', 'Active')
                ->get();  
        
        return $total_voiceover[0]['data'];
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function renew(SubscriptionPlan $id)
    {
        $subscribers = $this->countSubscribers($id->id);

        return view('admin.finance.plans.finance_subscription_renew', compact('id', 'subscribers'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function push(Request $request, SubscriptionPlan $id)
    {
        $subscribers = Subscriber::where('plan_id', $id->id)->where('status', 'Active')->get(); 

        foreach ($subscribers as $subscriber) {
            $user = User::where('id', $subscriber->user_id)->first();
            if ($user) {
                if (request('tokens_check') == 'on') {
                    $user->tokens = request('tokens');
                }
    
                if (request('images_check') == 'on') {
                    $user->images = request('images');
                }
    
                if (request('minutes_check') == 'on') {
                    $user->minutes = request('minutes');
                }
    
                if (request('characters_check') == 'on') {
                    $user->characters = request('characters');
                }
    
                $user->save();
            }
            
        }
        
       
        toastr()->success(__('Credits were applied to all active subscribers'));
        return redirect()->back();
    }
}
