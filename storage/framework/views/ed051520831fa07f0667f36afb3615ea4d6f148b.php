

<?php $__env->startSection('page-header'); ?>
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><?php echo e(__('Finance Settings')); ?></h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i><?php echo e(__('Admin')); ?></a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="<?php echo e(route('admin.finance.dashboard')); ?>"> <?php echo e(__('Finance Management')); ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(url('#')); ?>"> <?php echo e(__('Finance Settings')); ?></a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>	
	<!-- ALL PAYMENT CONFIGURATIONS -->					
	<div class="row justify-content-center">

		<div class="col-lg-9 col-md-12 col-xm-12">

			<form action="<?php echo e(route('admin.finance.settings.store')); ?>" method="POST" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>

				<div class="card pt-4">	
					<div class="card-body">				

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">			
								<div class="input-box">	
									<h6><?php echo e(__('Default Currency')); ?> <span class="text-muted">(<?php echo e(__('Payments/Plans/System/Payouts')); ?>)</span></h6>
									<select id="currency" name="currency" class="form-select" data-placeholder="Choose Default Currency:">			
										<?php $__currentLoopData = config('currencies.all'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($key); ?>" <?php if(config('payment.default_system_currency') == $key): ?> selected <?php endif; ?>><?php echo e($value['name']); ?> - <?php echo e($key); ?> (<?php echo $value['symbol']; ?>)</option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>									
									<?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
										<p class="text-danger"><?php echo e($errors->first('currency')); ?></p>
									<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
								</div> 						
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6><?php echo e(__('Tax Rate')); ?> (%)</h6>
									<div class="form-group">							    
										<input type="text" class="form-control <?php $__errorArgs = ['tax'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tax" name="tax" placeholder="Enter Tax Rate" value="<?php echo e(config('payment.payment_tax')); ?>">
									</div>
									<?php $__errorArgs = ['tax'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
										<p class="text-danger"><?php echo e($errors->first('tax')); ?></p>
									<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> 
								</div>							
							</div>	
							
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">
									<h6><?php echo e(__('Decimal Points in Prices')); ?> <span class="text-muted">(<?php echo e(__('.00')); ?>)</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select id="chat-feature-user" name="decimal-points" class="form-select" data-placeholder="<?php echo e(__('Allow/Deny Decimal Points in Prices')); ?>">
										<option value='allow' <?php if(config('payment.decimal_points') == 'allow'): ?> selected <?php endif; ?>><?php echo e(__('Allow')); ?></option>
										<option value='deny' <?php if(config('payment.decimal_points') == 'deny'): ?> selected <?php endif; ?>> <?php echo e(__('Deny')); ?></option>																															
									</select>
								</div>
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">
									<h6><?php echo e(__('AI Vendor Service Costs')); ?></h6>
										<a href="<?php echo e(route('admin.finance.settings.costs')); ?>" class="btn btn-primary ripple pl-7 pr-7" ><?php echo e(__('Update AI Vendor Service Costs')); ?></a>		
								</div>
							</div>
						</div>

						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary" style="min-width: 200px"><?php echo e(__('Save')); ?></button>							
						</div>					
					</div>
				</div>

				<div class="card border-0">
					<div class="card-header border-0 justify-content-center">
						<h3 class="card-title mt-5 mb-5 text-muted"><?php echo e(__('Payment Gateways')); ?></h3>
					</div>
					<div class="card-body pb-6">
						<div class="row" id="gateways">
							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/paypal')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/paypal.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Paypal')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Paypal API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/stripe')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/stripe.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Stripe')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Stripe API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/paystack')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/paystack.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Paystack')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Paystack API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/razorpay')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/razorpay.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Razorpay')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Razorpay API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/mollie')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/mollie.jpg')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Mollie')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Mollie API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/flutterwave')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/flutterwave.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Flutterwave')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Flutterwave API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/yookassa')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/yookassa.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Yookassa')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Yookassa API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/paddle')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/paddle.webp')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Paddle')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Paddle API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/braintree')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/braintree.svg')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Braintree')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Braintree API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/iyzico')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/iyzico.svg')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Iyzico')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Iyzico API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/midtrans')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/midtrans.jpeg')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Midtrans')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Midtrans API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/coinbase')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/coinbase.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Coinbase')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Coinbase API settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/bank')); ?>'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="<?php echo e(theme_url('img/payments/bank.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Bank Transfer')); ?></h6>
											</div>
											<p class="fs-12 mb-0 text-muted"><?php echo e(__('Bank Transfer settings and configuration')); ?></p>
										</div>
									</div>							
								</div>
							</div>

							<?php if(App\Services\HelperService::extensionCoinremitter()): ?>
								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/coinremitter')); ?>'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="<?php echo e(theme_url('img/payments/coinremitter.webp')); ?>" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Coinremitter')); ?></h6>
												</div>
												<p class="fs-12 mb-0 text-muted"><?php echo e(__('Coinremitter API settings and configuration')); ?></p>
											</div>
										</div>							
									</div>
								</div>
							<?php endif; ?>

							<?php if(App\Services\HelperService::extensionWallet()): ?>
								<?php if(App\Services\HelperService::extensionWalletFeature()): ?>							
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/wallet')); ?>'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="<?php echo e(theme_url('img/payments/wallet.avif')); ?>" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Wallet')); ?></h6>
													</div>
													<p class="fs-12 mb-0 text-muted"><?php echo e(__('Wallet settings and configuration')); ?></p>
												</div>
											</div>							
										</div>
									</div>
								<?php endif; ?>
							<?php endif; ?>

							<?php if(App\Services\HelperService::extensionAwdpay()): ?>
								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='<?php echo e(url('/app/admin/finance/settings/awdpay')); ?>'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="<?php echo e(theme_url('img/payments/awdpay.png')); ?>" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3"><?php echo e(__('Awdpay')); ?></h6>
												</div>
												<p class="fs-12 mb-0 text-muted"><?php echo e(__('Awdpay API settings and configuration')); ?></p>
											</div>
										</div>							
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>		
			
			</form>
				
		</div>
		
	</div>
	<!-- END ALL PAYMENT CONFIGURATIONS -->	

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hootgpt-app/htdocs/app.hootgpt.com/resources/views/default/admin/finance/settings/finance_setting_index.blade.php ENDPATH**/ ?>