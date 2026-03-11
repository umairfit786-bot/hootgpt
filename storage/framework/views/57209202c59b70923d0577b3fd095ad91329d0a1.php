

<?php $__env->startSection('page-header'); ?>
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"> <?php echo e(__('Subscription Plans')); ?></h4>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="<?php echo e(route('user.dashboard')); ?>"><i class="fa-solid fa-id-badge mr-2 fs-12"></i><?php echo e(__('User')); ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(route('user.plans')); ?>"> <?php echo e(__('Pricing Plans')); ?></a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>	
	<div class="row justify-content-center">

		<div class="col-lg-11 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pl-6" id="subscription-header">
					<div class="row" id="subscription-header-inner">
						<div class="col-lg-4 col-md-4 col-sm-12" style="margin-top: auto; margin-bottom: auto">
							<?php if(is_null($plan)): ?>
								<h3 class="card-title"><i class="fa-solid fa-box-circle-check text-primary mr-2 fs-14"></i><?php echo e(__('Upgrade to premium plan')); ?></h3>
								<p class="fs-13 text-muted mb-0"><?php echo e(__('You are currently not subscribed to any plan, please select your plan below and get started!')); ?></p>
							<?php else: ?>
								<h3 class="card-title fs-16"><?php echo e(__('You are subscribed to')); ?> <span class="text-primary"><?php echo e($plan->plan_name); ?> </span> <?php echo e(__('plan')); ?></h3>
								<p class="fs-12 text-muted mb-5"><?php echo e(__('It is a')); ?> <span class="font-weight-bold"><?php echo e(__($plan->payment_frequency)); ?></span> <?php echo e(__('plan with a cost of')); ?> <span class="font-weight-bold"><?php echo e($plan->price); ?> <?php echo e($plan->currency); ?></span></p>

								<?php if($plan->payment_frequency != 'lifetime'): ?>
									<p class="fs-12 text-muted mb-2"><?php echo e(__('Next billing is on')); ?> <?php echo e($date); ?> <?php echo e(__('at')); ?> <?php echo e($time); ?></p>
								<?php endif; ?>								
								<a href="<?php echo e(route('user.purchases')); ?>" class="fs-12 cancel-subscription"><?php echo e(__('View Orders')); ?></a>
								<a href="#" class="fs-12 cancel-subscription initiate-cancellation" id="<?php echo e($subscriber->id); ?>"><?php echo e(__('Cancel Subscription')); ?></a>								
							<?php endif; ?>
							
						</div>
		
						<div class="col-lg-8 col-md-8 col-sm-12" style="margin-top: auto; margin-bottom: auto">
							<div class="row text-center">
								<div class="col-lg col-md-6 col-sm-6">									
									<h6 class="fs-12 mt-3 font-weight-bold"><?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('Words Left')); ?> <?php else: ?> <?php echo e(__('Tokens Left')); ?> <?php endif; ?></h6>	
									<h4 class="mb-0 font-weight-800 text-primary fs-20"><?php if(auth()->user()->tokens == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(number_format(auth()->user()->tokens + auth()->user()->tokens_prepaid)); ?> <?php endif; ?></h4>
									<div class="view-credits mb-3"><a class=" fs-11 text-muted" href="javascript:void(0)" id="view-credits" data-bs-toggle="modal" data-bs-target="#creditsModel"> <?php echo e(__('View All Credits')); ?></a></div> 									
								</div>
								<?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'user|subscriber|admin')): ?>
									<?php if(config('settings.image_feature_user') == 'allow'): ?>
										<div class="col-lg col-md-6 col-sm-6">
											<h6 class="fs-12 mt-3 font-weight-bold"><?php echo e(__('Images Left')); ?></h6>
											<h4 class="mb-3 font-weight-800 text-primary fs-20"><?php if(auth()->user()->images == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(number_format(auth()->user()->images + auth()->user()->images_prepaid)); ?> <?php endif; ?></h4>										
										</div>	
									<?php endif; ?>
								<?php endif; ?>	
								<?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'user|subscriber|admin')): ?>
									<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>				
										<div class="col-lg col-md-6 col-sm-6">
											<h6 class="fs-12 mt-3 font-weight-bold"><?php echo e(__('Characters Left')); ?></h6>
											<h4 class="mb-3 font-weight-800 text-primary fs-20"><?php if(auth()->user()->characters == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(number_format(auth()->user()->characters + auth()->user()->characters_prepaid)); ?> <?php endif; ?></h4>										
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'user|subscriber|admin')): ?>
									<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
										<div class="col-lg col-md-6 col-sm-6">
											<h6 class="fs-12 mt-3 font-weight-bold"><?php echo e(__('Minutes Left')); ?></h6>
											<h4 class="mb-3 font-weight-800 text-primary fs-20"><?php if(auth()->user()->minutes == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(number_format(auth()->user()->minutes + auth()->user()->minutes_prepaid)); ?> <?php endif; ?></h4>										
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-11 col-md-12 col-sm-12">
			<div class="card border-0 pt-2">
				<div class="card-body">		
					<div class="title text-center mt-5 mb-5 backend-title">
						<h3 class="mb-4 fs-25 super-strong"><?php echo e(__('Our Subscription Plans')); ?></h3>     
						<h6 class="font-weight-normal fs-14 text-center"><?php echo e(__('Upgrade to your preferred Subscription Plan or Top Up your credits and get started')); ?></h6>                   
					</div>
					<?php if($monthly || $yearly || $prepaid || $lifetime): ?>
		
						<div class="tab-menu-heading text-center mb-3">
							<div class="tabs-menu dark-theme-target" >								
								<ul class="nav">
									<?php if($prepaid): ?>						
										<li><a href="#prepaid" class="<?php if(!$monthly && !$yearly && $prepaid && !$lifetime): ?> active <?php else: ?> '' <?php endif; ?>" data-bs-toggle="tab"> <?php echo e(__('Prepaid Plans')); ?></a></li>
									<?php endif; ?>							
									<?php if($monthly): ?>
										<li><a href="#monthly_plans" class="<?php if(($monthly && $prepaid && $yearly) || ($monthly && !$prepaid && !$yearly) || ($monthly && $prepaid && !$yearly) || ($monthly && !$prepaid && $yearly)): ?> active <?php else: ?> '' <?php endif; ?>" data-bs-toggle="tab"> <?php echo e(__('Monthly Plans')); ?></a></li>
									<?php endif; ?>	
									<?php if($yearly): ?>
										<li><a href="#yearly_plans" class="<?php if((!$monthly && !$prepaid && $yearly && !$lifetime) || (!$monthly && $prepaid && $yearly && !$lifetime) || (!$monthly && $prepaid && $yearly && $lifetime)): ?>  active <?php else: ?> '' <?php endif; ?>" data-bs-toggle="tab"> <?php echo e(__('Yearly Plans')); ?></a></li>
									<?php endif; ?>
									<?php if($lifetime): ?>
										<li><a href="#lifetime" class="<?php if((!$monthly && !$yearly && !$prepaid &&  $lifetime) || (!$monthly && !$yearly && $prepaid &&  $lifetime)): ?> active <?php else: ?> '' <?php endif; ?>" data-bs-toggle="tab"> <?php echo e(__('Lifetime Plans')); ?></a></li>
									<?php endif; ?>								
								</ul>
							</div>
						</div>
		
					
		
						<div class="tabs-menu-body">
							<div class="tab-content">
		
								<?php if($prepaid): ?>
									<div class="tab-pane <?php if((!$monthly && $prepaid) && (!$yearly && $prepaid)): ?> active <?php else: ?> '' <?php endif; ?>" id="prepaid">
		
										<?php if($prepaids->count()): ?>
															
											<div class="row justify-content-md-center">
											
												<?php $__currentLoopData = $prepaids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prepaid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>																			
													<div class="col-lg-4 col-md-6 col-sm-12">
														<div class="price-card pl-3 pr-3 pt-2 mb-6">
															<div class="card p-4 pl-5 prepaid-cards <?php if($prepaid->featured): ?> price-card-border <?php endif; ?>">
																<?php if($prepaid->featured): ?>
																	<span class="plan-featured-prepaid"><?php echo e(__('Most Popular')); ?></span>
																<?php endif; ?>
																<div class="plan prepaid-plan">
																	<div class="plan-title"><?php echo e($prepaid->plan_name); ?> </div>
																	<div class="plan-cost-wrapper mt-2 text-center mb-3 p-1"><span class="plan-cost"><?php if(config('payment.decimal_points') == 'allow'): ?> <?php echo e(number_format((float)$prepaid->price, 2)); ?> <?php else: ?> <?php echo e(number_format($prepaid->price)); ?> <?php endif; ?></span><span class="prepaid-currency-sign text-muted"><?php echo e($prepaid->currency); ?></span></div>	
																	<p class="fs-12 mb-3 text-muted"><?php echo e(__('Included Credits')); ?></p>	
																	<div class="credits-box">										 
																		<?php if($prepaid->tokens != 0): ?> <p class="fs-12 mt-2 mb-0"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('Words Included')); ?> <?php else: ?> <?php echo e(__('Tokens Included')); ?> <?php endif; ?>: <span class="ml-2 font-weight-bold text-primary"><?php echo e(number_format($prepaid->tokens)); ?></span></p><?php endif; ?>                                                                    
																		<?php if($prepaid->images != 0): ?> <p class="fs-12 mt-2 mb-0"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <?php echo e(__('Media Credits Included')); ?>: <span class="ml-2 font-weight-bold text-primary"><?php echo e(number_format($prepaid->images)); ?></span></p><?php endif; ?>                                                                    
																		<?php if($prepaid->characters != 0): ?> <p class="fs-12 mt-2 mb-0"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <?php echo e(__('Characters Included')); ?>: <span class="ml-2 font-weight-bold text-primary"><?php echo e(number_format($prepaid->characters)); ?></span></p><?php endif; ?>																							
																		<?php if($prepaid->minutes != 0): ?> <p class="fs-12 mt-2 mb-0"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <?php echo e(__('Minutes Included')); ?>: <span class="ml-2 font-weight-bold text-primary"><?php echo e(number_format($prepaid->minutes)); ?></span></p><?php endif; ?>	
																	</div>
																	<div class="text-center action-button mt-2 mb-2">
																		<a href="<?php echo e(route('user.prepaid.checkout', ['type' => 'prepaid', 'id' => $prepaid->id])); ?>" class="btn btn-primary-pricing"><?php echo e(__('Select Package')); ?></a> 
																	</div>																								                                                                          
																</div>							
															</div>	
														</div>							
													</div>										
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>						
		
											</div>
		
										<?php else: ?>
											<div class="row text-center">
												<div class="col-sm-12 mt-6 mb-6">
													<h6 class="fs-12 font-weight-bold text-center"><?php echo e(__('No Prepaid plans were set yet')); ?></h6>
												</div>
											</div>
										<?php endif; ?>
		
									</div>			
								<?php endif; ?>	
		
								<?php if($monthly): ?>	
									<div class="tab-pane <?php if(($monthly && $prepaid) || ($monthly && !$prepaid) || ($monthly && !$yearly)): ?> active <?php else: ?> '' <?php endif; ?>" id="monthly_plans">
		
										<?php if($monthly_subscriptions->count()): ?>		
		
											<div class="row justify-content-md-center">
		
												<?php $__currentLoopData = $monthly_subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>																			
													<div class="col-lg-3 col-md-4 col-sm-12">
														<div class="pt-2 h-100 prices-responsive pb-6">
															<div class="card p-5 mb-4 pl-6 pr-6 h-100 price-card <?php if($subscription->featured): ?> price-card-border <?php endif; ?>">
																<?php if($subscription->featured): ?>
																	<span class="plan-featured"><?php echo e(__('Most Popular')); ?></span>
																<?php endif; ?>
																<div class="plan">			
																	<div class="plan-title"><?php echo e($subscription->plan_name); ?></div>	
																	<p class="plan-cost mb-5">																					
																		<?php if($subscription->free): ?>
																			<?php echo e(__('Free')); ?>

																		<?php else: ?>
																			<?php echo config('payment.default_system_currency_symbol'); ?><?php if(config('payment.decimal_points') == 'allow'): ?><?php echo e(number_format((float)$subscription->price, 2)); ?> <?php else: ?><?php echo e(number_format($subscription->price)); ?> <?php endif; ?><span class="fs-12 text-muted"><span class="mr-1">/</span> <?php echo e(__('monthly')); ?></span>
																		<?php endif; ?>   
																	</p>  																				
																	<div class="text-center action-button mt-2 mb-5">
																		<?php if(auth()->user()->plan_id == $subscription->id): ?>
																			<a href="#" class="btn btn-primary-pricing"><i class="fa-solid fa-check fs-14 mr-2"></i><?php echo e(__('Subscribed')); ?></a> 
																		<?php else: ?>
																			<a href="<?php echo e(route('user.plan.subscribe', $subscription->id)); ?>" class="btn btn-primary-pricing"><?php if(!is_null(auth()->user()->plan_id)): ?> <?php echo e(__('Upgrade to')); ?> <?php echo e($subscription->plan_name); ?> <?php else: ?> <?php echo e(__('Subscribe Now')); ?> <?php endif; ?></a>
																		<?php endif; ?>                                               														
																	</div>
																	<p class="fs-12 mb-3 text-muted"><?php echo e(__('Included Features')); ?></p>																		
																	<ul class="fs-12 pl-3">	
																		<?php if($subscription->token_credits == -1): ?>
																			<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"> <?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('words')); ?> <?php else: ?> <?php echo e(__('tokens')); ?> <?php endif; ?></span></li>
																		<?php else: ?>
																			<?php if($subscription->token_credits != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->token_credits)); ?></span> <span class="plan-feature-text"><?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('words')); ?> <?php else: ?> <?php echo e(__('tokens')); ?> <?php endif; ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if($subscription->image_credits == -1): ?>
																			<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits')); ?></span></li>
																		<?php else: ?>
																			<?php if($subscription->image_credits != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->image_credits)); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
																			<?php if($subscription->minutes == -1): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes')); ?></span></li>
																			<?php else: ?>
																				<?php if($subscription->minutes != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->minutes)); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes')); ?></span></li> <?php endif; ?>
																			<?php endif; ?>																	
																		<?php endif; ?>
																		<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
																			<?php if($subscription->characters == -1): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('characters')); ?></span></li>
																			<?php else: ?>
																				<?php if($subscription->characters != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->characters)); ?></span> <span class="plan-feature-text"><?php echo e(__('characters')); ?></span></li> <?php endif; ?>
																			<?php endif; ?>																	
																		<?php endif; ?>
																			<?php if($subscription->team_members != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->team_members)); ?></span> <span class="plan-feature-text"><?php echo e(__('team members')); ?></span></li> <?php endif; ?>
																		
																		<?php if(config('settings.writer_feature_user') == 'allow'): ?>
																			<?php if($subscription->writer_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Writer Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.wizard_feature_user') == 'allow'): ?>
																			<?php if($subscription->wizard_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Article Wizard Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.smart_editor_feature_user') == 'allow'): ?>
																			<?php if($subscription->smart_editor_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Smart Editor Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.rewriter_feature_user') == 'allow'): ?>
																			<?php if($subscription->rewriter_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI ReWriter Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Chats Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.image_feature_user') == 'allow'): ?>
																			<?php if($subscription->image_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Images Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
																			<?php if($subscription->voiceover_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Voiceover Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.video_feature_user') == 'allow'): ?>
																			<?php if($subscription->video_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Video Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.voice_clone_feature_user') == 'allow'): ?>
																			<?php if($subscription->voice_clone_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Voice Clone Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.sound_studio_feature_user') == 'allow'): ?>
																			<?php if($subscription->sound_studio_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Sound Studio Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
																			<?php if($subscription->transcribe_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Speech to Text Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.plagiarism_checker_feature_user') == 'allow'): ?>
																			<?php if($subscription->plagiarism_checker_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Plagiarism Checker Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.vision_feature_user') == 'allow'): ?>
																			<?php if($subscription->vision_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Vision Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.ai_detector_feature_user') == 'allow'): ?>
																			<?php if($subscription->ai_detector_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Detector Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_file_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_file_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI File Chat Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_web_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_web_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Web Chat Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.code_feature_user') == 'allow'): ?>
																			<?php if($subscription->code_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Code Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if($subscription->team_members): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Team Members Option')); ?></span></li> <?php endif; ?>
																		<?php $__currentLoopData = (explode(',', $subscription->plan_features)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<?php if($feature): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <?php echo e(__($feature)); ?></li>
																			<?php endif; ?>																
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>															
																	</ul>																
																</div>					
															</div>	
														</div>							
													</div>										
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		
											</div>	
										
										<?php else: ?>
											<div class="row text-center">
												<div class="col-sm-12 mt-6 mb-6">
													<h6 class="fs-12 font-weight-bold text-center"><?php echo e(__('No Subscriptions plans were set yet')); ?></h6>
												</div>
											</div>
										<?php endif; ?>					
									</div>	
								<?php endif; ?>	
								
								<?php if($yearly): ?>	
									<div class="tab-pane <?php if(($yearly && $prepaid) && ($yearly && !$prepaid) && ($yearly && !$monthly)): ?> active <?php else: ?> '' <?php endif; ?>" id="yearly_plans">
		
										<?php if($yearly_subscriptions->count()): ?>		
		
											<div class="row justify-content-md-center">
		
												<?php $__currentLoopData = $yearly_subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>																			
													<div class="col-lg-4 col-md-6 col-sm-12">
														<div class="pt-2 h-100 prices-responsive pb-6">
															<div class="card p-5 mb-4 pl-6 pr-6 h-100 price-card <?php if($subscription->featured): ?> price-card-border <?php endif; ?>">
																<?php if($subscription->featured): ?>
																	<span class="plan-featured"><?php echo e(__('Most Popular')); ?></span>
																<?php endif; ?>
																<div class="plan">			
																	<div class="plan-title"><?php echo e($subscription->plan_name); ?></div>	
																	<p class="plan-cost mb-5">
																		<?php if($subscription->free): ?>
																			<?php echo e(__('Free')); ?>

																		<?php else: ?>
																			<?php echo config('payment.default_system_currency_symbol'); ?><?php if(config('payment.decimal_points') == 'allow'): ?><?php echo e(number_format((float)$subscription->price, 2)); ?> <?php else: ?><?php echo e(number_format($subscription->price)); ?> <?php endif; ?><span class="fs-12 text-muted"><span class="mr-1">/</span> <?php echo e(__('yearly')); ?></span>
																		<?php endif; ?>    
																	</p> 																				
																	<div class="text-center action-button mt-2 mb-5">
																		<?php if(auth()->user()->plan_id == $subscription->id): ?>
																			<a href="#" class="btn btn-primary-pricing"><i class="fa-solid fa-check fs-14 mr-2"></i><?php echo e(__('Subscribed')); ?></a> 
																		<?php else: ?>
																			<a href="<?php echo e(route('user.plan.subscribe', $subscription->id)); ?>" class="btn btn-primary-pricing"><?php if(!is_null(auth()->user()->plan_id)): ?> <?php echo e(__('Upgrade to')); ?> <?php echo e($subscription->plan_name); ?> <?php else: ?> <?php echo e(__('Subscribe Now')); ?> <?php endif; ?></a>
																		<?php endif; ?>                                                														
																	</div>
																	<p class="fs-12 mb-3 text-muted"><?php echo e(__('Included Features')); ?></p>																	
																	<ul class="fs-12 pl-3">	
																		<?php if($subscription->token_credits == -1): ?>
																			<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"> <?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('words')); ?> <?php else: ?> <?php echo e(__('tokens')); ?> <?php endif; ?></span></li>
																		<?php else: ?>
																			<?php if($subscription->token_credits != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->token_credits)); ?></span> <span class="plan-feature-text"><?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('words')); ?> <?php else: ?> <?php echo e(__('tokens')); ?> <?php endif; ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if($subscription->image_credits == -1): ?>
																			<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits')); ?></span></li>
																		<?php else: ?>
																			<?php if($subscription->image_credits != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->image_credits)); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
																			<?php if($subscription->minutes == -1): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes')); ?></span></li>
																			<?php else: ?>
																				<?php if($subscription->minutes != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->minutes)); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes')); ?></span></li> <?php endif; ?>
																			<?php endif; ?>																	
																		<?php endif; ?>
																		<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
																			<?php if($subscription->characters == -1): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('characters')); ?></span></li>
																			<?php else: ?>
																				<?php if($subscription->characters != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->characters)); ?></span> <span class="plan-feature-text"><?php echo e(__('characters')); ?></span></li> <?php endif; ?>
																			<?php endif; ?>																	
																		<?php endif; ?>
																			<?php if($subscription->team_members != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->team_members)); ?></span> <span class="plan-feature-text"><?php echo e(__('team members')); ?></span></li> <?php endif; ?>
																		
																		<?php if(config('settings.writer_feature_user') == 'allow'): ?>
																			<?php if($subscription->writer_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Writer Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.wizard_feature_user') == 'allow'): ?>
																			<?php if($subscription->wizard_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Article Wizard Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.smart_editor_feature_user') == 'allow'): ?>
																			<?php if($subscription->smart_editor_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Smart Editor Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.rewriter_feature_user') == 'allow'): ?>
																			<?php if($subscription->rewriter_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI ReWriter Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Chats Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.image_feature_user') == 'allow'): ?>
																			<?php if($subscription->image_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Images Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
																			<?php if($subscription->voiceover_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Voiceover Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.video_feature_user') == 'allow'): ?>
																			<?php if($subscription->video_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Video Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.voice_clone_feature_user') == 'allow'): ?>
																			<?php if($subscription->voice_clone_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Voice Clone Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.sound_studio_feature_user') == 'allow'): ?>
																			<?php if($subscription->sound_studio_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Sound Studio Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
																			<?php if($subscription->transcribe_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Speech to Text Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.plagiarism_checker_feature_user') == 'allow'): ?>
																			<?php if($subscription->plagiarism_checker_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Plagiarism Checker Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.vision_feature_user') == 'allow'): ?>
																			<?php if($subscription->vision_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Vision Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.ai_detector_feature_user') == 'allow'): ?>
																			<?php if($subscription->ai_detector_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Detector Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_file_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_file_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI File Chat Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_web_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_web_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Web Chat Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.code_feature_user') == 'allow'): ?>
																			<?php if($subscription->code_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Code Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if($subscription->team_members): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Team Members Option')); ?></span></li> <?php endif; ?>
																		<?php $__currentLoopData = (explode(',', $subscription->plan_features)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<?php if($feature): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <?php echo e(__($feature)); ?></li>
																			<?php endif; ?>																
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>															
																	</ul>																	
																</div>					
															</div>	
														</div>							
													</div>											
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		
											</div>	
										
										<?php else: ?>
											<div class="row text-center">
												<div class="col-sm-12 mt-6 mb-6">
													<h6 class="fs-12 font-weight-bold text-center"><?php echo e(__('No Subscriptions plans were set yet')); ?></h6>
												</div>
											</div>
										<?php endif; ?>					
									</div>
								<?php endif; ?>	
								
								<?php if($lifetime): ?>
									<div class="tab-pane <?php if((!$monthly && $lifetime) && (!$yearly && $lifetime)): ?> active <?php else: ?> '' <?php endif; ?>" id="lifetime">
		
										<?php if($lifetime_subscriptions->count()): ?>                                                    
											
											<div class="row justify-content-md-center">
											
												<?php $__currentLoopData = $lifetime_subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>																			
													<div class="col-lg-4 col-md-6 col-sm-12">
														<div class="pt-2 h-100 prices-responsive pb-6">
															<div class="card p-5 mb-4 pl-6 pr-6 h-100 price-card <?php if($subscription->featured): ?> price-card-border <?php endif; ?>">
																<?php if($subscription->featured): ?>
																	<span class="plan-featured"><?php echo e(__('Most Popular')); ?></span>
																<?php endif; ?>
																<div class="plan">			
																	<div class="plan-title"><?php echo e($subscription->plan_name); ?></div>	
																	<p class="plan-cost mb-5">
																		<?php if($subscription->free): ?>
																			<?php echo e(__('Free')); ?>

																		<?php else: ?>
																			<?php echo config('payment.default_system_currency_symbol'); ?><?php if(config('payment.decimal_points') == 'allow'): ?><?php echo e(number_format((float)$subscription->price, 2)); ?> <?php else: ?><?php echo e(number_format($subscription->price)); ?> <?php endif; ?><span class="fs-12 text-muted"><span class="mr-1">/</span> <?php echo e(__('forever')); ?></span>
																		<?php endif; ?>
																	</p>																					
																	<div class="text-center action-button mt-2 mb-5">
																		<?php if(auth()->user()->plan_id == $subscription->id): ?>
																			<a href="#" class="btn btn-primary-pricing"><i class="fa-solid fa-check fs-14 mr-2"></i><?php echo e(__('Subscribed')); ?></a> 
																		<?php else: ?>
																			<a href="<?php echo e(route('user.prepaid.checkout', ['type' => 'lifetime', 'id' => $subscription->id])); ?>" class="btn btn-primary-pricing"><?php if(!is_null(auth()->user()->plan_id)): ?> <?php echo e(__('Upgrade to')); ?> <?php echo e($subscription->plan_name); ?> <?php else: ?> <?php echo e(__('Subscribe Now')); ?> <?php endif; ?></a>
																		<?php endif; ?>                                                 														
																	</div>
																	<p class="fs-12 mb-3 text-muted"><?php echo e(__('Included Features')); ?></p>																	
																	<ul class="fs-12 pl-3">	
																		<?php if($subscription->token_credits == -1): ?>
																			<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"> <?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('words')); ?> <?php else: ?> <?php echo e(__('tokens')); ?> <?php endif; ?></span></li>
																		<?php else: ?>
																			<?php if($subscription->token_credits != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->token_credits)); ?></span> <span class="plan-feature-text"><?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('words')); ?> <?php else: ?> <?php echo e(__('tokens')); ?> <?php endif; ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if($subscription->image_credits == -1): ?>
																			<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits')); ?></span></li>
																		<?php else: ?>
																			<?php if($subscription->image_credits != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->image_credits)); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
																			<?php if($subscription->minutes == -1): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes')); ?></span></li>
																			<?php else: ?>
																				<?php if($subscription->minutes != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->minutes)); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes')); ?></span></li> <?php endif; ?>
																			<?php endif; ?>																	
																		<?php endif; ?>
																		<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
																			<?php if($subscription->characters == -1): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('characters')); ?></span></li>
																			<?php else: ?>
																				<?php if($subscription->characters != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->characters)); ?></span> <span class="plan-feature-text"><?php echo e(__('characters')); ?></span></li> <?php endif; ?>
																			<?php endif; ?>																	
																		<?php endif; ?>
																			<?php if($subscription->team_members != 0): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($subscription->team_members)); ?></span> <span class="plan-feature-text"><?php echo e(__('team members')); ?></span></li> <?php endif; ?>
																		
																		<?php if(config('settings.writer_feature_user') == 'allow'): ?>
																			<?php if($subscription->writer_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Writer Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.wizard_feature_user') == 'allow'): ?>
																			<?php if($subscription->wizard_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Article Wizard Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.smart_editor_feature_user') == 'allow'): ?>
																			<?php if($subscription->smart_editor_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Smart Editor Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.rewriter_feature_user') == 'allow'): ?>
																			<?php if($subscription->rewriter_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI ReWriter Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Chats Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.image_feature_user') == 'allow'): ?>
																			<?php if($subscription->image_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Images Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
																			<?php if($subscription->voiceover_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Voiceover Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.video_feature_user') == 'allow'): ?>
																			<?php if($subscription->video_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Video Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.voice_clone_feature_user') == 'allow'): ?>
																			<?php if($subscription->voice_clone_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Voice Clone Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.sound_studio_feature_user') == 'allow'): ?>
																			<?php if($subscription->sound_studio_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Sound Studio Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
																			<?php if($subscription->transcribe_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Speech to Text Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.plagiarism_checker_feature_user') == 'allow'): ?>
																			<?php if($subscription->plagiarism_checker_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Plagiarism Checker Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.vision_feature_user') == 'allow'): ?>
																			<?php if($subscription->vision_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Vision Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.ai_detector_feature_user') == 'allow'): ?>
																			<?php if($subscription->ai_detector_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Detector Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_file_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_file_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI File Chat Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.chat_web_feature_user') == 'allow'): ?>
																			<?php if($subscription->chat_web_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Web Chat Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if(config('settings.code_feature_user') == 'allow'): ?>
																			<?php if($subscription->code_feature): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Code Feature')); ?></span></li> <?php endif; ?>
																		<?php endif; ?>
																		<?php if($subscription->team_members): ?> <li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Team Members Option')); ?></span></li> <?php endif; ?>
																		<?php $__currentLoopData = (explode(',', $subscription->plan_features)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																			<?php if($feature): ?>
																				<li class="fs-13 mb-3"><i class="fa-solid fa-check fs-14 mr-2 text-success"></i> <?php echo e(__($feature)); ?></li>
																			<?php endif; ?>																
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>															
																	</ul>																	
																</div>					
															</div>	
														</div>							
													</div>											
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>					
		
											</div>
		
										<?php else: ?>
											<div class="row text-center">
												<div class="col-sm-12 mt-6 mb-6">
													<h6 class="fs-12 font-weight-bold text-center"><?php echo e(__('No lifetime plans were set yet')); ?></h6>
												</div>
											</div>
										<?php endif; ?>
		
									</div>	
								<?php endif; ?>	
							</div>
						</div>
					
					<?php else: ?>
						<div class="row text-center">
							<div class="col-sm-12 mt-6 mb-6">
								<h6 class="fs-12 font-weight-bold text-center"><?php echo e(__('No Subscriptions or Prepaid plans were set yet')); ?></h6>
							</div>
						</div>
					<?php endif; ?>
		
				</div>
			</div>
		</div>

	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
	<script src="<?php echo e(URL::asset('plugins/sweetalert/sweetalert2.all.min.js')); ?>"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";


			// CANCEL SUBSCRIPTION
			$(document).on('click', '.initiate-cancellation', function(e) {

				e.preventDefault();

				Swal.fire({
					title: '<?php echo e(__('Confirm Subscription Cancellation')); ?>',
					text: '<?php echo e(__('It will cancel this subscription plan going forward')); ?>',
					icon: 'warning',
					showCancelButton: true,
					cancelButtonText: '<?php echo e(__('No Way')); ?>',
					confirmButtonText: '<?php echo e(__('Yes, I want to Cancel')); ?>',
					reverseButtons: true,
				}).then((result) => {
					if (result.isConfirmed) {
						var formData = new FormData();
						formData.append("id", $(this).attr('id'));
						$.ajax({
							headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
							method: 'post',
							url: '/app/user/purchases/subscriptions/cancel',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data['status'] == 200) {
									Swal.fire('<?php echo e(__('Subscription Cancelled')); ?>', data['message'], 'success');	
									$("#mySubscriptionsTable").DataTable().ajax.reload();								
								} else {
									Swal.fire('<?php echo e(__('Cancellation Failed')); ?>', data['message'], 'error');
								}      
							},
							error: function(data) {
								console.log(data)
								Swal.fire('Oops...','Something went wrong!', 'error')
							}
						})
					} 
				})
			});

		});
	</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hootgpt-app/htdocs/app.hootgpt.com/resources/views/default/user/plans/user_plan_index.blade.php ENDPATH**/ ?>