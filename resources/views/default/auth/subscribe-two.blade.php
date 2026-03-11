@extends('layouts.auth')

@section('metadata')
    <meta name="description" content="{{ __($metadata->register_description) }}">
    <meta name="keywords" content="{{ __($metadata->register_keywords) }}">
    <meta name="author" content="{{ __($metadata->register_author) }}">	    
    <link rel="canonical" href="{{ $metadata->register_url }}">
    <title>{{ __($metadata->register_title) }}</title>
@endsection

@section('content')
    @if ($extension->maintenance_feature)			
        <div class="container">
            <div class="row text-center h-100vh align-items-center">
                <div class="col-md-12">
                    <img src="{{ theme_url($extension->maintenance_banner) }}" alt="Maintenance Image">
                    <h2 class="mt-4 font-weight-bold">{{ __($extension->maintenance_header) }}</h2>
                    <h5>{{ __($extension->maintenance_message) }} </h5>						
                </div>					
            </div>
            <footer class="text-center  align-items-center">
                <p class="text-muted">{{ __($extension->maintenance_message) }} </p>
            </footer>
        </div>
    @else
        @if (config('settings.registration') == 'enabled')
            <div class="container-fluid h-100vh ">                
                <div class="row login-background justify-content-center">

                    <div class="col-sm-12" id="login-responsive"> 
                        <div class="row justify-content-center subscribe-registration-background">
                            <div class="col-lg-8 col-md-12 col-sm-12 mx-auto">
                                <div class="card-body pt-8">

                                    <a class="navbar-brand register-logo" href="{{ url('/') }}"><img id="brand-img"  src="{{ URL::asset($settings->logo_frontend) }}" alt=""></a>
                                    
                                    <div class="registration-nav mb-8 mt-8">
                                        <div class="registration-nav-inner">					
                                            <div class="row text-center justify-content-center">
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="d-flex wizard-nav-text">
                                                        <div class="wizard-step-number current-step mr-3 fs-14" id="step-one-number"><i class="fa-solid fa-check"></i></div>
                                                        <div class="wizard-step-title"><span class="font-weight-bold fs-14">{{ __('Create Account') }}</span> <br> <span class="text-muted wizard-step-title-number fs-11 float-left">{{ __('STEP 1') }}</span></div>
                                                    </div>
                                                    <div>
                                                        <i class="fa-solid fa-chevrons-right wizard-nav-chevron current-sign" id="step-one-icon"></i>
                                                    </div>									
                                                </div>	
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="d-flex wizard-nav-text">
                                                        <div class="wizard-step-number mr-3 fs-14 current-step" id="step-two-number">2</div>
                                                        <div class="wizard-step-title responsive"><span class="font-weight-bold fs-14">{{ __('Select Your Plan') }}</span> <br> <span class="text-muted wizard-step-title-number fs-11 float-left">{{ __('STEP 2') }}</span></div>
                                                    </div>	
                                                    <div>
                                                        <i class="fa-solid fa-chevrons-right wizard-nav-chevron" id="step-two-icon"></i>
                                                    </div>								
                                                </div>
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="d-flex wizard-nav-text">
                                                        <div class="wizard-step-number mr-3 fs-14" id="step-three-number">3</div>
                                                        <div class="wizard-step-title"><span class="font-weight-bold fs-14">{{ __('Payment') }}</span> <br> <span class="text-muted wizard-step-title-number fs-11 float-left">{{ __('STEP 3') }}</span></div>
                                                    </div>								
                                                </div>
                                            </div>					
                                        </div>
                                    </div>                         

                                    <div id="registration-prices" class="subscribe-second-step">

                                        <h3 class="text-center login-title mb-2">{{__('Select Your Plan')}} </h3>
                                        <p class="fs-12 text-muted text-center mb-8">{{ __('Choose your subscription plan and click continue') }}</p>

                                        @if ($monthly || $yearly || $lifetime)
                            
                                            <div class="tab-menu-heading text-center">
                                                <div class="tabs-menu">								
                                                    <ul class="nav">							
                                                        @if ($monthly)
                                                            <li><a href="#monthly_plans" class="@if (($monthly && $yearly) || ($monthly && !$yearly) || ($monthly && !$yearly) || ($monthly && $yearly)) active @else '' @endif" data-bs-toggle="tab"> {{ __('Monthly Plans') }}</a></li>
                                                        @endif	
                                                        @if ($yearly)
                                                            <li><a href="#yearly_plans" class="@if (!$monthly && $yearly) active @else '' @endif" data-bs-toggle="tab"> {{ __('Yearly Plans') }}</a></li>
                                                        @endif		
                                                        @if ($lifetime)
                                                            <li><a href="#lifetime" class="@if (!$monthly && !$yearly &&  $lifetime) active @else '' @endif" data-bs-toggle="tab"> {{ __('Lifetime Plans') }}</a></li>
                                                        @endif							
                                                    </ul>
                                                </div>
                                            </div>
                            
                                        
                            
                                            <div class="tabs-menu-body">
                                                <div class="tab-content">
                            
                                                    @if ($monthly)	
                                                        <div class="tab-pane @if (($monthly && !$lifetime) || ($monthly && !$yearly)) active @else '' @endif" id="monthly_plans">
                            
                                                            @if ($monthly_subscriptions->count())		
                            
                                                                <div class="row justify-content-md-center">
                            
                                                                    @foreach ( $monthly_subscriptions as $subscription )																			
                                                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                                                            <div class="pt-2 h-100 prices-responsive pb-6">
                                                                                <div class="card p-5 mb-4 pl-6 pr-6 h-100 price-card @if ($subscription->featured) price-card-border @endif">
                                                                                    @if ($subscription->featured)
                                                                                        <span class="plan-featured">{{ __('Most Popular') }}</span>
                                                                                    @endif
                                                                                    <div class="plan">			
                                                                                        <div class="plan-title">{{ $subscription->plan_name }}</div>	
                                                                                        <p class="plan-cost mb-5">																					
                                                                                            @if ($subscription->free)
                                                                                                {{ __('Free') }}
                                                                                            @else
                                                                                                {!! config('payment.default_system_currency_symbol') !!}@if(config('payment.decimal_points') == 'allow'){{ number_format((float)$subscription->price, 2) }} @else{{ number_format($subscription->price) }} @endif<span class="fs-12 text-muted"><span class="mr-1">/</span> {{ __('monthly') }}</span>
                                                                                            @endif   
                                                                                        </p>  																				
                                                                                        <div class="text-center action-button mt-2 mb-5">
                                                                                            @if (auth()->user()->plan_id == $subscription->id)
                                                                                                <a href="#" class="btn btn-primary-pricing"><i class="fa-solid fa-check fs-14 mr-2"></i>{{ __('Subscribed') }}</a> 
                                                                                            @else
                                                                                                <a href="{{ route('user.plan.subscribe', $subscription->id) }}" class="btn btn-primary-pricing">@if (!is_null(auth()->user()->plan_id)) {{ __('Upgrade to') }} {{ $subscription->plan_name }} @else {{ __('Subscribe Now') }} @endif</a>
                                                                                            @endif                                               														
                                                                                        </div>
                                                                                        <p class="fs-12 mb-3 text-muted">{{ __('Included Features') }}</p>																		
                                                                                        <ul class="fs-12 pl-3">	
                                                                                            @if ($subscription->token_credits == -1)
                                                                                                <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text"> @if ($settings->model_credit_name == 'words') {{ __('words') }} @else {{ __('tokens') }} @endif</span></li>
                                                                                            @else
                                                                                                @if($subscription->token_credits != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->token_credits) }}</span> <span class="plan-feature-text">@if ($settings->model_credit_name == 'words') {{ __('words') }} @else {{ __('tokens') }} @endif</span></li> @endif
                                                                                            @endif
                                                                                            @if ($subscription->image_credits == -1)
                                                                                                <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('media credits') }}</span></li>
                                                                                            @else
                                                                                                @if($subscription->image_credits != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->image_credits) }}</span> <span class="plan-feature-text">{{ __('media credits') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.whisper_feature_user') == 'allow')
                                                                                                @if ($subscription->minutes == -1)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('minutes') }}</span></li>
                                                                                                @else
                                                                                                    @if($subscription->minutes != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->minutes) }}</span> <span class="plan-feature-text">{{ __('minutes') }}</span></li> @endif
                                                                                                @endif																	
                                                                                            @endif
                                                                                            @if (config('settings.voiceover_feature_user') == 'allow')
                                                                                                @if ($subscription->characters == -1)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('characters') }}</span></li>
                                                                                                @else
                                                                                                    @if($subscription->characters != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->characters) }}</span> <span class="plan-feature-text">{{ __('characters') }}</span></li> @endif
                                                                                                @endif																	
                                                                                            @endif
                                                                                                @if($subscription->team_members != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->team_members) }}</span> <span class="plan-feature-text">{{ __('team members') }}</span></li> @endif
                                                                                            
                                                                                            @if (config('settings.writer_feature_user') == 'allow')
                                                                                                @if($subscription->writer_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Writer Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.wizard_feature_user') == 'allow')
                                                                                                @if($subscription->wizard_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Article Wizard Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.smart_editor_feature_user') == 'allow')
                                                                                                @if($subscription->smart_editor_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Smart Editor Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.rewriter_feature_user') == 'allow')
                                                                                                @if($subscription->rewriter_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI ReWriter Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_feature_user') == 'allow')
                                                                                                @if($subscription->chat_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Chats Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.image_feature_user') == 'allow')
                                                                                                @if($subscription->image_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Images Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.voiceover_feature_user') == 'allow')
                                                                                                @if($subscription->voiceover_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Voiceover Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.video_feature_user') == 'allow')
                                                                                                @if($subscription->video_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Video Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.voice_clone_feature_user') == 'allow')
                                                                                                @if($subscription->voice_clone_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Voice Clone Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.sound_studio_feature_user') == 'allow')
                                                                                                @if($subscription->sound_studio_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Sound Studio Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.whisper_feature_user') == 'allow')
                                                                                                @if($subscription->transcribe_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Speech to Text Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.plagiarism_checker_feature_user') == 'allow')
                                                                                                @if($subscription->plagiarism_checker_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Plagiarism Checker Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.vision_feature_user') == 'allow')
                                                                                                @if($subscription->vision_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Vision Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.ai_detector_feature_user') == 'allow')
                                                                                                @if($subscription->ai_detector_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Detector Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_file_feature_user') == 'allow')
                                                                                                @if($subscription->chat_file_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI File Chat Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_web_feature_user') == 'allow')
                                                                                                @if($subscription->chat_web_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Web Chat Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.code_feature_user') == 'allow')
                                                                                                @if($subscription->code_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Code Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if($subscription->team_members) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Team Members Option') }}</span></li> @endif
                                                                                            @foreach ( (explode(',', $subscription->plan_features)) as $feature )
                                                                                                @if ($feature)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> {{ __($feature) }}</li>
                                                                                                @endif																
                                                                                            @endforeach															
                                                                                        </ul>																
                                                                                    </div>					
                                                                                </div>	
                                                                            </div>							
                                                                        </div>										
                                                                    @endforeach
                            
                                                                </div>	
                                                            
                                                            @else
                                                                <div class="row text-center">
                                                                    <div class="col-sm-12 mt-6 mb-6">
                                                                        <h6 class="fs-12 font-weight-bold text-center">{{ __('No Subscriptions plans were set yet') }}</h6>
                                                                    </div>
                                                                </div>
                                                            @endif					
                                                        </div>	
                                                    @endif	
                                                    
                                                    @if ($yearly)	
                                                        <div class="tab-pane @if (($yearly && !$lifetime) && ($yearly && !$monthly)) active @else '' @endif" id="yearly_plans">
                            
                                                            @if ($yearly_subscriptions->count())		
                            
                                                                <div class="row justify-content-md-center">
                            
                                                                    @foreach ( $yearly_subscriptions as $subscription )																			
                                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                                            <div class="pt-2 h-100 prices-responsive pb-6">
                                                                                <div class="card p-5 mb-4 pl-6 pr-6 h-100 price-card @if ($subscription->featured) price-card-border @endif">
                                                                                    @if ($subscription->featured)
                                                                                        <span class="plan-featured">{{ __('Most Popular') }}</span>
                                                                                    @endif
                                                                                    <div class="plan">			
                                                                                        <div class="plan-title">{{ $subscription->plan_name }}</div>	
                                                                                        <p class="plan-cost mb-5">
                                                                                            @if ($subscription->free)
                                                                                                {{ __('Free') }}
                                                                                            @else
                                                                                                {!! config('payment.default_system_currency_symbol') !!}@if(config('payment.decimal_points') == 'allow'){{ number_format((float)$subscription->price, 2) }} @else{{ number_format($subscription->price) }} @endif<span class="fs-12 text-muted"><span class="mr-1">/</span> {{ __('yearly') }}</span>
                                                                                            @endif    
                                                                                        </p> 																				
                                                                                        <div class="text-center action-button mt-2 mb-5">
                                                                                            @if (auth()->user()->plan_id == $subscription->id)
                                                                                                <a href="#" class="btn btn-primary-pricing"><i class="fa-solid fa-check fs-14 mr-2"></i>{{ __('Subscribed') }}</a> 
                                                                                            @else
                                                                                                <a href="{{ route('user.plan.subscribe', $subscription->id) }}" class="btn btn-primary-pricing">@if (!is_null(auth()->user()->plan_id)) {{ __('Upgrade to') }} {{ $subscription->plan_name }} @else {{ __('Subscribe Now') }} @endif</a>
                                                                                            @endif                                                														
                                                                                        </div>
                                                                                        <p class="fs-12 mb-3 text-muted">{{ __('Included Features') }}</p>																	
                                                                                        <ul class="fs-12 pl-3">	
                                                                                            @if ($subscription->token_credits == -1)
                                                                                                <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text"> @if ($settings->model_credit_name == 'words') {{ __('words') }} @else {{ __('tokens') }} @endif</span></li>
                                                                                            @else
                                                                                                @if($subscription->token_credits != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->token_credits) }}</span> <span class="plan-feature-text">@if ($settings->model_credit_name == 'words') {{ __('words') }} @else {{ __('tokens') }} @endif</span></li> @endif
                                                                                            @endif
                                                                                            @if ($subscription->image_credits == -1)
                                                                                                <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('media credits') }}</span></li>
                                                                                            @else
                                                                                                @if($subscription->image_credits != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->image_credits) }}</span> <span class="plan-feature-text">{{ __('media credits') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.whisper_feature_user') == 'allow')
                                                                                                @if ($subscription->minutes == -1)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('minutes') }}</span></li>
                                                                                                @else
                                                                                                    @if($subscription->minutes != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->minutes) }}</span> <span class="plan-feature-text">{{ __('minutes') }}</span></li> @endif
                                                                                                @endif																	
                                                                                            @endif
                                                                                            @if (config('settings.voiceover_feature_user') == 'allow')
                                                                                                @if ($subscription->characters == -1)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('characters') }}</span></li>
                                                                                                @else
                                                                                                    @if($subscription->characters != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->characters) }}</span> <span class="plan-feature-text">{{ __('characters') }}</span></li> @endif
                                                                                                @endif																	
                                                                                            @endif
                                                                                                @if($subscription->team_members != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->team_members) }}</span> <span class="plan-feature-text">{{ __('team members') }}</span></li> @endif
                                                                                            
                                                                                            @if (config('settings.writer_feature_user') == 'allow')
                                                                                                @if($subscription->writer_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Writer Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.wizard_feature_user') == 'allow')
                                                                                                @if($subscription->wizard_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Article Wizard Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.smart_editor_feature_user') == 'allow')
                                                                                                @if($subscription->smart_editor_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Smart Editor Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.rewriter_feature_user') == 'allow')
                                                                                                @if($subscription->rewriter_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI ReWriter Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_feature_user') == 'allow')
                                                                                                @if($subscription->chat_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Chats Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.image_feature_user') == 'allow')
                                                                                                @if($subscription->image_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Images Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.voiceover_feature_user') == 'allow')
                                                                                                @if($subscription->voiceover_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Voiceover Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.video_feature_user') == 'allow')
                                                                                                @if($subscription->video_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Video Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.voice_clone_feature_user') == 'allow')
                                                                                                @if($subscription->voice_clone_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Voice Clone Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.sound_studio_feature_user') == 'allow')
                                                                                                @if($subscription->sound_studio_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Sound Studio Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.whisper_feature_user') == 'allow')
                                                                                                @if($subscription->transcribe_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Speech to Text Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.plagiarism_checker_feature_user') == 'allow')
                                                                                                @if($subscription->plagiarism_checker_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Plagiarism Checker Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.vision_feature_user') == 'allow')
                                                                                                @if($subscription->vision_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Vision Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.ai_detector_feature_user') == 'allow')
                                                                                                @if($subscription->ai_detector_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Detector Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_file_feature_user') == 'allow')
                                                                                                @if($subscription->chat_file_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI File Chat Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_web_feature_user') == 'allow')
                                                                                                @if($subscription->chat_web_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Web Chat Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.code_feature_user') == 'allow')
                                                                                                @if($subscription->code_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Code Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if($subscription->team_members) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Team Members Option') }}</span></li> @endif
                                                                                            @foreach ( (explode(',', $subscription->plan_features)) as $feature )
                                                                                                @if ($feature)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> {{ __($feature) }}</li>
                                                                                                @endif																
                                                                                            @endforeach															
                                                                                        </ul>																	
                                                                                    </div>					
                                                                                </div>	
                                                                            </div>							
                                                                        </div>											
                                                                    @endforeach
                            
                                                                </div>	
                                                            
                                                            @else
                                                                <div class="row text-center">
                                                                    <div class="col-sm-12 mt-6 mb-6">
                                                                        <h6 class="fs-12 font-weight-bold text-center">{{ __('No Subscriptions plans were set yet') }}</h6>
                                                                    </div>
                                                                </div>
                                                            @endif					
                                                        </div>
                                                    @endif	
                                                    
                                                    @if ($lifetime)
                                                        <div class="tab-pane @if ((!$monthly && $lifetime) && (!$yearly && $lifetime)) active @else '' @endif" id="lifetime">
                            
                                                            @if ($lifetime_subscriptions->count())                                                    
                                                                
                                                                <div class="row justify-content-md-center">
                                                                
                                                                    @foreach ( $lifetime_subscriptions as $subscription )																			
                                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                                            <div class="pt-2 h-100 prices-responsive pb-6">
                                                                                <div class="card p-5 mb-4 pl-6 pr-6 h-100 price-card @if ($subscription->featured) price-card-border @endif">
                                                                                    @if ($subscription->featured)
                                                                                        <span class="plan-featured">{{ __('Most Popular') }}</span>
                                                                                    @endif
                                                                                    <div class="plan">			
                                                                                        <div class="plan-title">{{ $subscription->plan_name }}</div>	
                                                                                        <p class="plan-cost mb-5">
                                                                                            @if ($subscription->free)
                                                                                                {{ __('Free') }}
                                                                                            @else
                                                                                                {!! config('payment.default_system_currency_symbol') !!}@if(config('payment.decimal_points') == 'allow'){{ number_format((float)$subscription->price, 2) }} @else{{ number_format($subscription->price) }} @endif<span class="fs-12 text-muted"><span class="mr-1">/</span> {{ __('forever') }}</span>
                                                                                            @endif
                                                                                        </p>																					
                                                                                        <div class="text-center action-button mt-2 mb-5">
                                                                                            @if (auth()->user()->plan_id == $subscription->id)
                                                                                                <a href="#" class="btn btn-primary-pricing"><i class="fa-solid fa-check fs-14 mr-2"></i>{{ __('Subscribed') }}</a> 
                                                                                            @else
                                                                                                <a href="{{ route('user.prepaid.checkout', ['type' => 'lifetime', 'id' => $subscription->id]) }}" class="btn btn-primary-pricing">@if (!is_null(auth()->user()->plan_id)) {{ __('Upgrade to') }} {{ $subscription->plan_name }} @else {{ __('Subscribe Now') }} @endif</a>
                                                                                            @endif                                                 														
                                                                                        </div>
                                                                                        <p class="fs-12 mb-3 text-muted">{{ __('Included Features') }}</p>																	
                                                                                        <ul class="fs-12 pl-3">	
                                                                                            @if ($subscription->token_credits == -1)
                                                                                                <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text"> @if ($settings->model_credit_name == 'words') {{ __('words') }} @else {{ __('tokens') }} @endif</span></li>
                                                                                            @else
                                                                                                @if($subscription->token_credits != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->token_credits) }}</span> <span class="plan-feature-text">@if ($settings->model_credit_name == 'words') {{ __('words') }} @else {{ __('tokens') }} @endif</span></li> @endif
                                                                                            @endif
                                                                                            @if ($subscription->image_credits == -1)
                                                                                                <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('media credits') }}</span></li>
                                                                                            @else
                                                                                                @if($subscription->image_credits != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->image_credits) }}</span> <span class="plan-feature-text">{{ __('media credits') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.whisper_feature_user') == 'allow')
                                                                                                @if ($subscription->minutes == -1)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('minutes') }}</span></li>
                                                                                                @else
                                                                                                    @if($subscription->minutes != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->minutes) }}</span> <span class="plan-feature-text">{{ __('minutes') }}</span></li> @endif
                                                                                                @endif																	
                                                                                            @endif
                                                                                            @if (config('settings.voiceover_feature_user') == 'allow')
                                                                                                @if ($subscription->characters == -1)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ __('Unlimited') }}</span> <span class="plan-feature-text">{{ __('characters') }}</span></li>
                                                                                                @else
                                                                                                    @if($subscription->characters != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->characters) }}</span> <span class="plan-feature-text">{{ __('characters') }}</span></li> @endif
                                                                                                @endif																	
                                                                                            @endif
                                                                                                @if($subscription->team_members != 0) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold">{{ number_format($subscription->team_members) }}</span> <span class="plan-feature-text">{{ __('team members') }}</span></li> @endif
                                                                                            
                                                                                            @if (config('settings.writer_feature_user') == 'allow')
                                                                                                @if($subscription->writer_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Writer Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.wizard_feature_user') == 'allow')
                                                                                                @if($subscription->wizard_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Article Wizard Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.smart_editor_feature_user') == 'allow')
                                                                                                @if($subscription->smart_editor_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Smart Editor Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.rewriter_feature_user') == 'allow')
                                                                                                @if($subscription->rewriter_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI ReWriter Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_feature_user') == 'allow')
                                                                                                @if($subscription->chat_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Chats Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.image_feature_user') == 'allow')
                                                                                                @if($subscription->image_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Images Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.voiceover_feature_user') == 'allow')
                                                                                                @if($subscription->voiceover_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Voiceover Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.video_feature_user') == 'allow')
                                                                                                @if($subscription->video_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Video Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.voice_clone_feature_user') == 'allow')
                                                                                                @if($subscription->voice_clone_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Voice Clone Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.sound_studio_feature_user') == 'allow')
                                                                                                @if($subscription->sound_studio_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Sound Studio Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.whisper_feature_user') == 'allow')
                                                                                                @if($subscription->transcribe_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Speech to Text Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.plagiarism_checker_feature_user') == 'allow')
                                                                                                @if($subscription->plagiarism_checker_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Plagiarism Checker Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.vision_feature_user') == 'allow')
                                                                                                @if($subscription->vision_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Vision Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.ai_detector_feature_user') == 'allow')
                                                                                                @if($subscription->ai_detector_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Detector Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_file_feature_user') == 'allow')
                                                                                                @if($subscription->chat_file_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI File Chat Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.chat_web_feature_user') == 'allow')
                                                                                                @if($subscription->chat_web_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Web Chat Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if (config('settings.code_feature_user') == 'allow')
                                                                                                @if($subscription->code_feature) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('AI Code Feature') }}</span></li> @endif
                                                                                            @endif
                                                                                            @if($subscription->team_members) <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text">{{ __('Team Members Option') }}</span></li> @endif
                                                                                            @foreach ( (explode(',', $subscription->plan_features)) as $feature )
                                                                                                @if ($feature)
                                                                                                    <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> {{ __($feature) }}</li>
                                                                                                @endif																
                                                                                            @endforeach															
                                                                                        </ul>																	
                                                                                    </div>					
                                                                                </div>	
                                                                            </div>							
                                                                        </div>											
                                                                    @endforeach					
                            
                                                                </div>
                            
                                                            @else
                                                                <div class="row text-center">
                                                                    <div class="col-sm-12 mt-6 mb-6">
                                                                        <h6 class="fs-12 font-weight-bold text-center">{{ __('No lifetime plans were set yet') }}</h6>
                                                                    </div>
                                                                </div>
                                                            @endif
                            
                                                        </div>	
                                                    @endif	
                                                </div>
                                            </div>
                                        
                                        @else
                                            <div class="row text-center">
                                                <div class="col-sm-12 mt-6 mb-6">
                                                    <h6 class="fs-12 font-weight-bold text-center">{{ __('No Subscription plans were set yet') }}</h6>
                                                </div>
                                            </div>
                                        @endif
            
                                        <div class="text-center">
                                            <p class="mb-0 mt-2"><i class="fa-solid fa-shield-check text-success mr-2"></i><span class="text-muted fs-12">{{ __('PCI DSS Compliant') }}</span></p>
                                        </div> 

                                        <div class="text-center">
                                            <a class="fs-12 font-weight-bold special-action-sign" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Sign Out') }}</a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>     
                                        </div>   
                                
                                    </div>

                                </div> 
                            </div>      
                        </div>
                    </div>
                </div>
            </div>
        @else
            <h5 class="text-center pt-9">{{__('New user registration is disabled currently')}}</h5>
        @endif
    @endif
@endsection


