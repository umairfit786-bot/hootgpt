

<?php $__env->startSection('css'); ?>
	<!-- Telephone Input CSS -->
	<link href="<?php echo e(URL::asset('plugins/telephoneinput/telephoneinput.css')); ?>" rel="stylesheet" >
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-header'); ?>
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><i class="fa-solid fa-box-circle-check mr-2"></i><?php echo e(__('Secure Checkout')); ?></h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="<?php echo e(route('user.dashboard')); ?>"><i class="fa-solid fa-id-badge mr-2 fs-12"></i><?php echo e(__('User')); ?></a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="<?php echo e(route('user.plans')); ?>"> <?php echo e(__('Pricing Plans')); ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(url('#')); ?>"> <?php echo e(__('Subscription Plan Checkout')); ?></a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>	
	<div class="row justify-content-center">
		<div class="col-lg-10 col-md-12 col-sm-12">
			<div class="card border-0 pt-2">
				<div class="card-body">	
					
					<form id="payment-form" action="<?php echo e(route('user.payments.pay', $id)); ?>" method="POST" enctype="multipart/form-data">
						<?php echo csrf_field(); ?>

						<div class="row">
							<div class="col-lg-8 col-md-6 col-sm-12 pr-4">
								<div class="checkout-wrapper-box pb-0">							

									<p class="checkout-title mt-2"><i class="fa-solid fa-lock-hashtag mr-2 text-success"></i><?php echo e(__('Secure Checkout')); ?></p>

									<div class="divider mb-5">
										<div class="divider-text text-muted">
											<small><?php echo e(__('Billing Details')); ?></small>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-6 col-md-6">
											<div class="input-box">
												<div class="form-group">
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('First Name')); ?> <span class="text-required-register"><i class="fa-solid fa-asterisk"></i></span></label>
													<input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e(auth()->user()->name); ?>" required>
													<?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('name')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>									
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-md-6">
											<div class="input-box">
												<div class="form-group">
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('Last Name')); ?> <span class="text-required-register"><i class="fa-solid fa-asterisk"></i></span></label>
													<input type="text" class="form-control <?php $__errorArgs = ['lastname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="lastname" value="<?php echo e(auth()->user()->name); ?>" required>
													<?php $__errorArgs = ['lastname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('lastname')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>									
												</div>
											</div>
										</div>						
										<div class="col-sm-6 col-md-6">
											<div class="input-box">
												<div class="form-group">
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('Email Address')); ?> <span class="text-required-register"><i class="fa-solid fa-asterisk"></i></span></label>
													<input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(auth()->user()->email); ?>">
													<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('email')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
												</div>
											</div>
										</div>
											
										<div class="col-sm-6 col-md-6">
											<div class="input-box">
												<div class="form-group">								
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('Phone Number')); ?></label>
													<input type="tel" class="fs-12 form-control <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="phone-number" name="phone_number" value="<?php echo e(auth()->user()->phone_number); ?>">
													<?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('phone_number')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
												</div>
											</div>
										</div>				
										<div class="col-md-12">
											<div class="input-box">
												<div class="form-group">
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('Billing Address')); ?> <span class="text-required-register"><i class="fa-solid fa-asterisk"></i></span></label>
													<input type="text" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="address" value="<?php echo e(auth()->user()->address); ?>">
													<?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('address')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-md-4">
											<div class="input-box">
												<div class="form-group">
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('City')); ?> <span class="text-required-register"><i class="fa-solid fa-asterisk"></i></span></label>
													<input type="text" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="city" value="<?php echo e(auth()->user()->city); ?>">
													<?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('city')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-md-3">
											<div class="input-box">
												<div class="form-group">
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('Postal Code')); ?> <span class="text-required-register"><i class="fa-solid fa-asterisk"></i></span></label>
													<input type="text" class="form-control <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="postal_code" value="<?php echo e(auth()->user()->postal_code); ?>">
													<?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('postal_code')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
												</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group">
												<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('Country')); ?> <span class="text-required-register"><i class="fa-solid fa-asterisk"></i></span></label>
												<select id="user-country" name="country" class="form-select">	
													<?php $__currentLoopData = config('countries'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($value); ?>" <?php if(auth()->user()->country == $value): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>										
												</select>
												<?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
													<p class="text-danger"><?php echo e($errors->first('country')); ?></p>
												<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
											</div>
										</div>
										<div class="col-md-12">
											<div class="input-box">
												<div class="form-group">
													<label class="form-label fs-11 font-weight-semibold"><?php echo e(__('VAT Number')); ?></label>
													<input type="text" class="form-control <?php $__errorArgs = ['vat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="vat">
													<?php $__errorArgs = ['vat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
														<p class="text-danger"><?php echo e($errors->first('vat')); ?></p>
													<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
												</div>
											</div>
										</div>
									</div>

									<div class="divider mb-6">
										<div class="divider-text text-muted">
											<small><?php echo e(__('Select Payment Option')); ?></small>
										</div>
									</div>

									<div class="form-group" id="toggler">
										<div class="text-center">
											<div class="btn-group btn-group-toggle w-100" data-toggle='buttons'>
												<div class="row w-100">
													<?php $__currentLoopData = $payment_platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment_platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<div class="col-lg-4 col-md-6 col-sm-12">
															<label class="gateway btn rounded p-0" href="#<?php echo e($payment_platform->name); ?>Collapse" data-bs-toggle="collapse">
																<input type="radio" class="gateway-radio" name="payment_platform" value="<?php echo e($payment_platform->id); ?>">
																<img src="<?php echo e(theme_url($payment_platform->image)); ?>" 
																class="<?php if($payment_platform->name == 'Paystack' || $payment_platform->name == 'Razorpay' || $payment_platform->name == 'PayPal'): ?> payment-image	
																<?php elseif($payment_platform->name == 'Braintree'): ?> payment-image-braintree
																<?php elseif($payment_platform->name == 'Mollie'): ?> payment-image-mollie
																<?php elseif($payment_platform->name == 'Stripe'): ?> payment-image-stripe	
																<?php elseif($payment_platform->name == 'Iyzico'): ?> payment-image-iyzico
																<?php elseif($payment_platform->name == 'Paddle'): ?> payment-image-paddle
																<?php elseif($payment_platform->name == 'Flutterwave'): ?> payment-image-flutterwave
																<?php elseif($payment_platform->name == 'Wallet'): ?> payment-image-paddle
																<?php endif; ?>" alt="<?php echo e($payment_platform->name); ?>">
															</label>	
														</div>									
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>		
												</div>							
											</div>
										</div>

										<?php $__currentLoopData = $payment_platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment_platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($payment_platform->name !== 'BankTransfer'): ?>
												<div id="<?php echo e($payment_platform->name); ?>Collapse" class="collapse" data-bs-parent="#toggler">
													<?php if ($__env->exists('components.'.strtolower($payment_platform->name).'-collapse')) echo $__env->make('components.'.strtolower($payment_platform->name).'-collapse', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
												</div>
											<?php else: ?>
												<div id="<?php echo e($payment_platform->name); ?>Collapse" class="collapse" data-bs-parent="#toggler">
													<div class="text-center pb-2">
														<p class="text-muted fs-12 mb-4"><?php echo e($bank['bank_instructions']); ?></p>
														<p class="text-muted fs-12 mb-4">Order ID: <span class="font-weight-bold text-primary"><?php echo e($bank_order_id); ?></span></p>
														<pre class="text-muted fs-12 mb-4"><?php echo e($bank['bank_requisites']); ?></pre>															
													</div>
												</div>																										
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</div>

									<input type="hidden" name="value" value="<?php echo e($total_value); ?>">
									<input type="hidden" name="currency" value="<?php echo e($currency); ?>">																															
									
								</div>

								<div class="text-center pt-4 pb-1">
									<button type="submit" id="payment-button" class="btn btn-primary pl-8 pr-8 mb-4 fs-11 ripple"><?php echo e(__('Checkout Now')); ?></button>
								</div>

							</div>

							<div class="col-lg-4 col-md-6 col-sm-12 pl-4">
								<div class="checkout-wrapper-box">

									<div class="divider mb-4">
										<div class="divider-text text-muted">
											<small><?php echo e(__('Order Details')); ?></small>
										</div>
									</div>

									<p class="checkout-title mt-2 text-center"><i class="fa fa-archive mr-2"></i><?php echo e(__('Subscription Plan Details')); ?></p>
									<p class="checkout-title text-center mt-3"><span class="text-info fs-14"><?php echo e($id->plan_name); ?></span></p>

									<div class="plan">				
										<p class="plan-cost mb-3 mt-3 fs-14 font-weight-bold text-primary text-center">
											<?php if($id->free): ?>
												<?php echo e(__('Free')); ?>

											<?php else: ?>
												<?php if(config('payment.decimal_points') == 'allow'): ?><?php echo e(number_format((float)$id->price, 2)); ?> <?php else: ?><?php echo e(number_format($id->price)); ?> <?php endif; ?> <?php echo e($id->currency); ?> <span class="fs-12 text-muted"><span class="mr-1">/</span> <?php echo e($id->payment_frequency); ?></span>
											<?php endif; ?>
										</p>																					
										<p class="fs-12 mb-3 text-muted"><?php echo e(__('Included Features')); ?></p>																	
										<ul class="fs-12 pl-3">	
												
											<?php if($id->token_credits == -1): ?>
												<li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('words / month')); ?></span></li>
											<?php else: ?>	
												<?php if($id->token_credits != 0): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($id->token_credits)); ?></span> <span class="plan-feature-text"><?php echo e(__('words / month')); ?></span></li> <?php endif; ?>
											<?php endif; ?>
											<?php if($id->image_credits == -1): ?>
												<li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits / month')); ?></span></li>
											<?php else: ?>
												<?php if($id->image_credits != 0): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($id->image_credits)); ?></span> <span class="plan-feature-text"><?php echo e(__('media credits / month')); ?></span></li> <?php endif; ?>
											<?php endif; ?>
											<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
												<?php if($id->minutes == -1): ?>
													<li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes / month')); ?></span></li>
												<?php else: ?>
													<?php if($id->minutes != 0): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($id->minutes)); ?></span> <span class="plan-feature-text"><?php echo e(__('minutes / month')); ?></span></li> <?php endif; ?>
												<?php endif; ?>																	
											<?php endif; ?>
											<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
												<?php if($id->characters == -1): ?>
													<li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(__('Unlimited')); ?></span> <span class="plan-feature-text"><?php echo e(__('characters / month')); ?></span></li>
												<?php else: ?>
													<?php if($id->characters != 0): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e(number_format($id->characters)); ?></span> <span class="plan-feature-text"><?php echo e(__('characters / month')); ?></span></li> <?php endif; ?>
												<?php endif; ?>																	
											<?php endif; ?>
												<?php if($id->team_members != 0): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="font-weight-bold"><?php echo e($id->team_members); ?></span> <span class="plan-feature-text"><?php echo e(__('team members')); ?></span></li> <?php endif; ?>
											
											<?php if(config('settings.chat_feature_user') == 'allow'): ?>
												<?php if($id->chat_feature): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Chats Feature')); ?></span></li> <?php endif; ?>
											<?php endif; ?>
											<?php if(config('settings.image_feature_user') == 'allow'): ?>
												<?php if($id->image_feature): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Images Feature')); ?></span></li> <?php endif; ?>
											<?php endif; ?>
											<?php if(config('settings.voiceover_feature_user') == 'allow'): ?>
												<?php if($id->voiceover_feature): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Voiceover Feature')); ?></span></li> <?php endif; ?>
											<?php endif; ?>
											<?php if(config('settings.whisper_feature_user') == 'allow'): ?>
												<?php if($id->transcribe_feature): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Speech to Text Feature')); ?></span></li> <?php endif; ?>
											<?php endif; ?>
											<?php if(config('settings.code_feature_user') == 'allow'): ?>
												<?php if($id->code_feature): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('AI Code Feature')); ?></span></li> <?php endif; ?>
											<?php endif; ?>
											<?php if($id->team_members): ?> <li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <span class="plan-feature-text"><?php echo e(__('Team Members Option')); ?></span></li> <?php endif; ?>
											<?php $__currentLoopData = (explode(',', $id->plan_features)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if($feature): ?>
													<li class="fs-12 mb-3"><i class="fa-solid fa-check fs-12 mr-2 text-success"></i> <?php echo e($feature); ?></li>
												<?php endif; ?>																
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>															
										</ul>																
									</div>

									<div class="divider mb-4">
										<div class="divider-text text-muted">
											<small><?php echo e(__('Purchase Summary')); ?></small>
										</div>
									</div>

									<div>
										<p class="fs-12 p-family"><?php echo e(__('Subtotal')); ?> <span class="checkout-cost"><?php if(config('payment.decimal_points') == 'allow'): ?> <?php echo e(number_format((float)$id->price, 2, '.', '')); ?> <?php else: ?> <?php echo e(number_format($id->price)); ?> <?php endif; ?> <?php echo e($id->currency); ?></span></p>
										<p class="fs-12 p-family"><?php echo e(__('Taxes')); ?> <span class="text-muted">(<?php echo e(config('payment.payment_tax')); ?>%)</span><span class="checkout-cost"><?php if(config('payment.decimal_points') == 'allow'): ?> <?php echo e(number_format((float)$tax_value, 2, '.', '')); ?> <?php else: ?> <?php echo e(number_format($tax_value)); ?> <?php endif; ?> <?php echo e($id->currency); ?></span></p>
									</div>

									<div class="divider mb-5">
										<div class="divider-text text-muted">
											<small><?php echo e(__('Total')); ?></small>
										</div>
									</div>

									<div>
										<p class="fs-12 p-family"><?php echo e(__('Total Due')); ?> </span><span class="checkout-cost text-info"><span id="total_payment"> <?php if(config('payment.decimal_points') == 'allow'): ?> <?php echo e(number_format((float)$total_value, 2, '.', '')); ?> <?php else: ?> <?php echo e(number_format($total_value)); ?> <?php endif; ?></span> <?php echo e($currency); ?></span></p>
									</div>
								</div>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
	<!-- Telephone Input JS -->
	<script src="<?php echo e(URL::asset('plugins/telephoneinput/telephoneinput.js')); ?>"></script>
	<script>
		$(function() {
			"use strict";
			
			$("#phone-number").intlTelInput();
		});

		$('#payment-button').on('click', function(e) {
			
			e.preventDefault();

			if($("input[type='radio'][name=payment_platform]:checked").val()) {
				document.getElementById("payment-form").submit();
			} else {
				toastr.warning('<?php echo e(__('Please select a payment gateway first')); ?>');
			}

		});


	</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hootgpt-app/htdocs/app.hootgpt.com/resources/views/default/user/plans/user_plan_subscribe-checkout.blade.php ENDPATH**/ ?>