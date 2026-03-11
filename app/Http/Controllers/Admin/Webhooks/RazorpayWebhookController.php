<?php

namespace App\Http\Controllers\Admin\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\PaymentProcessed;
use App\Events\PaymentReferrerBonus;
use App\Models\Subscriber;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    /**
     * Razorpay Webhook processing
     * Handles various webhook events from Razorpay
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleRazorpay(Request $request)
    {
        // Get the raw payload and signature
        $input = $request->getContent();
        $webhook_signature = $request->header('x-razorpay-signature');

        // Get the webhook secret from config
        $webhook_secret = config('services.razorpay.webhook_secret');

        if (empty($webhook_secret)) {
            Log::channel('razorpay')->error('Webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        // Verify signature
        $generated_signature = hash_hmac('sha256', $input, $webhook_secret);

        if (!hash_equals($generated_signature, $webhook_signature)) {
            Log::channel('razorpay')->error('Signature mismatch', [
                'generated_signature' => $generated_signature,
                'webhook_signature' => $webhook_signature,
            ]);
            
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        // Parse the webhook payload
        $payload = json_decode($input, true);
        
        // Log the webhook for debugging
        Log::channel('razorpay')->info('Webhook received', [
            'event' => $payload['event'] ?? 'unknown',
            'payload_id' => $payload['payload']['payment']['entity']['id'] ?? $payload['payload']['subscription']['entity']['id'] ?? 'unknown'
        ]);
        
        // Check if the required data exists
        if (!isset($payload['event'])) {
            Log::channel('razorpay')->error('Invalid webhook payload: event missing');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Handle different event types
        try {
            switch ($payload['event']) {
                case 'payment.authorized':
                    $this->handlePaymentAuthorized($payload['payload']['payment']['entity']);
                    break;
                    
                case 'payment.captured':
                    $this->handlePaymentCaptured($payload['payload']['payment']['entity']);
                    break;
                    
                case 'payment.failed':
                    $this->handlePaymentFailed($payload['payload']['payment']['entity']);
                    break;
                    
                case 'subscription.activated':
                    $this->handleSubscriptionActivated($payload['payload']['subscription']['entity']);
                    break;
                    
                case 'subscription.charged':
                    $this->handleSubscriptionCharged($payload['payload']['subscription']['entity'], 
                                                    $payload['payload']['payment']['entity'] ?? null);
                    break;
                    
                case 'subscription.cancelled':
                    $this->handleSubscriptionCancelled($payload['payload']['subscription']['entity']);
                    break;
                    
                case 'subscription.paused':
                    $this->handleSubscriptionPaused($payload['payload']['subscription']['entity']);
                    break;
                    
                case 'subscription.resumed':
                    $this->handleSubscriptionResumed($payload['payload']['subscription']['entity']);
                    break;
                    
                case 'subscription.pending':
                    $this->handleSubscriptionPending($payload['payload']['subscription']['entity']);
                    break;
                    
                case 'refund.created':
                    $this->handleRefundCreated($payload['payload']['refund']['entity'], 
                                              $payload['payload']['payment']['entity'] ?? null);
                    break;
                    
                default:
                    Log::channel('razorpay')->info('Unhandled webhook event', [
                        'event' => $payload['event']
                    ]);
                    break;
            }
            
            // Return a 200 response to acknowledge receipt of the webhook
            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            Log::channel('razorpay')->error('Error processing webhook', [
                'event' => $payload['event'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Error processing webhook'], 500);
        }
    }
    
    /**
     * Handle payment.authorized event
     * This is triggered when a payment is authorized but not yet captured
     */
    protected function handlePaymentAuthorized(array $paymentEntity)
    {
        Log::channel('razorpay')->info('Payment authorized', [
            'payment_id' => $paymentEntity['id'],
            'amount' => $paymentEntity['amount'] / 100, // Convert from paise to rupees
            'currency' => $paymentEntity['currency'],
            'status' => $paymentEntity['status']
        ]);
        
        // You might want to automatically capture the payment here
        // or wait for the payment.captured event
    }
    
    /**
     * Handle payment.captured event
     * This is triggered when a payment is successfully captured
     */
    protected function handlePaymentCaptured(array $paymentEntity)
    {
        Log::channel('razorpay')->info('Payment captured', [
            'payment_id' => $paymentEntity['id'],
            'amount' => $paymentEntity['amount'] / 100, // Convert from paise to rupees
            'currency' => $paymentEntity['currency']
        ]);
        
        // Extract metadata to identify what this payment is for
        $notes = $paymentEntity['notes'] ?? [];
        $orderId = $notes['order_id'] ?? null;
        $planId = $notes['plan_id'] ?? null;
        $userId = $notes['user_id'] ?? null;
        
        // If this is a one-time payment (not subscription)
        if ($userId && $planId) {
            $this->processOneTimePayment($paymentEntity, $userId, $planId);
        }
    }
    
    /**
     * Handle payment.failed event
     * This is triggered when a payment fails
     */
    protected function handlePaymentFailed(array $paymentEntity)
    {
        Log::channel('razorpay')->info('Payment failed', [
            'payment_id' => $paymentEntity['id'],
            'error_code' => $paymentEntity['error_code'] ?? null,
            'error_description' => $paymentEntity['error_description'] ?? null
        ]);
        
        // You might want to notify the user or update your database
    }
    
    /**
     * Handle subscription.activated event
     * This is triggered when a subscription is activated
     */
    protected function handleSubscriptionActivated(array $subscriptionEntity)
    {
        $subscriptionId = $subscriptionEntity['id'];
        $planId = $subscriptionEntity['plan_id'];
        $customerId = $subscriptionEntity['customer_id'];
        
        Log::channel('razorpay')->info('Subscription activated', [
            'subscription_id' => $subscriptionId,
            'plan_id' => $planId,
            'customer_id' => $customerId
        ]);
        
        // Find the subscription in our database
        $subscription = Subscriber::where('subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            // Try to find by customer ID
            $subscription = Subscriber::where('razorpay_customer_id', $customerId)->first();
            
            if (!$subscription) {
                Log::channel('razorpay')->error('Subscription not found', [
                    'subscription_id' => $subscriptionId,
                    'customer_id' => $customerId
                ]);
                return;
            }
            
            // Update the subscription ID
            $subscription->subscription_id = $subscriptionId;
        }
        
        // Get the plan details
        $plan = SubscriptionPlan::where('razorpay_plan_id', $planId)->first();
        
        if (!$plan) {
            Log::channel('razorpay')->error('Plan not found', [
                'razorpay_plan_id' => $planId
            ]);
            return;
        }
        
        // Calculate subscription end date
        $duration = ($plan->payment_frequency == 'monthly') ? 30 : 365;
        
        // Update subscription status
        $subscription->update([
            'status' => 'Active',
            'active_until' => Carbon::now()->addDays($duration),
            'plan_id' => $plan->id,
            'razorpay_plan_id' => $planId
        ]);
        
        // Update user role and plan
        $user = User::find($subscription->user_id);
        
        if ($user) {
            $group = ($user->hasRole('admin')) ? 'admin' : 'subscriber';
            
            $user->syncRoles($group);    
            $user->group = $group;
            $user->plan_id = $plan->id;
            $user->tokens = $plan->token_credits;
            $user->characters = $plan->characters;
            $user->minutes = $plan->minutes;
            $user->images = $plan->image_credits;
            $user->member_limit = $plan->team_members;
            $user->save();
            
            // Trigger payment processed event
            event(new PaymentProcessed($user));
        }
    }
    
    /**
     * Handle subscription.charged event
     * This is triggered when a subscription payment is charged
     */
    protected function handleSubscriptionCharged(array $subscriptionEntity, ?array $paymentEntity = null)
    {
        $subscriptionId = $subscriptionEntity['id'];
        $planId = $subscriptionEntity['plan_id'];
        
        Log::channel('razorpay')->info('Subscription charged', [
            'subscription_id' => $subscriptionId,
            'plan_id' => $planId,
            'payment_id' => $paymentEntity['id'] ?? 'unknown'
        ]);
        
        // Find the subscription in our database
        $subscription = Subscriber::where('subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::channel('razorpay')->error('Subscription not found for charging', [
                'subscription_id' => $subscriptionId
            ]);
            return;
        }
        
        // Get the plan details
        $plan = SubscriptionPlan::find($subscription->plan_id);
        
        if (!$plan) {
            Log::channel('razorpay')->error('Plan not found for subscription', [
                'plan_id' => $subscription->plan_id
            ]);
            return;
        }
        
        // Calculate subscription end date
        $duration = ($plan->payment_frequency == 'monthly') ? 30 : 365;
        
        // Update subscription status
        $subscription->update([
            'status' => 'Active',
            'active_until' => Carbon::now()->addDays($duration)
        ]);
        
        // Get the user
        $user = User::find($subscription->user_id);
        
        if (!$user) {
            Log::channel('razorpay')->error('User not found for subscription', [
                'user_id' => $subscription->user_id
            ]);
            return;
        }
        
        // Calculate tax
        $tax_value = (config('payment.payment_tax') > 0) ? $plan->price * config('payment.payment_tax') / 100 : 0;
        $total_price = $tax_value + $plan->price;
        
        // Process referral bonus if enabled
        if (config('payment.referral.enabled') == 'on') {
            if (config('payment.referral.payment.policy') == 'first') {
                if (!Payment::where('user_id', $user->id)->where('status', 'completed')->exists()) {
                    event(new PaymentReferrerBonus($user, $subscription->plan_id, $total_price, 'Razorpay'));
                }
            } else {
                event(new PaymentReferrerBonus($user, $subscription->plan_id, $total_price, 'Razorpay'));
            }
        }
        
        // Record the payment
        $payment_id = $paymentEntity['id'] ?? ('rp_sub_' . $subscriptionId . '_' . time());
        
        $record_payment = new Payment();
        $record_payment->user_id = $user->id;
        $record_payment->plan_id = $plan->id;
        $record_payment->order_id = $payment_id;
        $record_payment->plan_name = $plan->plan_name;
        $record_payment->price = $total_price;
        $record_payment->currency = $plan->currency;
        $record_payment->gateway = 'Razorpay';
        $record_payment->frequency = $plan->payment_frequency;
        $record_payment->status = 'completed';
        $record_payment->tokens = $plan->token_credits;
        $record_payment->characters = $plan->characters;
        $record_payment->minutes = $plan->minutes;
        $record_payment->images = $plan->image_credits;
        $record_payment->save();
        
        // Update user details
        $group = ($user->hasRole('admin')) ? 'admin' : 'subscriber';
        
        $user->syncRoles($group);    
        $user->group = $group;
        $user->plan_id = $plan->id;
        $user->tokens = $plan->token_credits;
        $user->characters = $plan->characters;
        $user->minutes = $plan->minutes;
        $user->images = $plan->image_credits;
        $user->member_limit = $plan->team_members;
        $user->save();
        
        // Trigger payment processed event
        event(new PaymentProcessed($user));
    }
    
    /**
     * Handle subscription.cancelled event
     * This is triggered when a subscription is cancelled
     */
    protected function handleSubscriptionCancelled(array $subscriptionEntity)
    {
        $subscriptionId = $subscriptionEntity['id'];
        
        Log::channel('razorpay')->info('Subscription cancelled', [
            'subscription_id' => $subscriptionId
        ]);
        
        // Find the subscription in our database
        $subscription = Subscriber::where('subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::channel('razorpay')->error('Subscription not found for cancellation', [
                'subscription_id' => $subscriptionId
            ]);
            return;
        }
        
        // Update subscription status
        $subscription->update([
            'status' => 'Cancelled',
            'active_until' => Carbon::now()->endOfMonth()
        ]);
        
        // Update user role
        $user = User::find($subscription->user_id);
        
        if ($user) {
            $group = ($user->hasRole('admin')) ? 'admin' : 'user';
            
            if ($group == 'user') {
                $user->syncRoles($group);    
                $user->group = $group;
                $user->plan_id = null;
                $user->member_limit = null;
                $user->save();
            } else {
                $user->syncRoles($group);    
                $user->group = $group;
                $user->plan_id = null;
                $user->save();
            }
        }
    }
    
    /**
     * Handle subscription.paused event
     * This is triggered when a subscription is paused
     */
    protected function handleSubscriptionPaused(array $subscriptionEntity)
    {
        $subscriptionId = $subscriptionEntity['id'];
        
        Log::channel('razorpay')->info('Subscription paused', [
            'subscription_id' => $subscriptionId
        ]);
        
        // Find the subscription in our database
        $subscription = Subscriber::where('subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::channel('razorpay')->error('Subscription not found for pausing', [
                'subscription_id' => $subscriptionId
            ]);
            return;
        }
        
        // Update subscription status
        $subscription->update([
            'status' => 'Paused'
        ]);
    }
    
    /**
     * Handle subscription.resumed event
     * This is triggered when a paused subscription is resumed
     */
    protected function handleSubscriptionResumed(array $subscriptionEntity)
    {
        $subscriptionId = $subscriptionEntity['id'];
        
        Log::channel('razorpay')->info('Subscription resumed', [
            'subscription_id' => $subscriptionId
        ]);
        
        // Find the subscription in our database
        $subscription = Subscriber::where('subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::channel('razorpay')->error('Subscription not found for resuming', [
                'subscription_id' => $subscriptionId
            ]);
            return;
        }
        
        // Update subscription status
        $subscription->update([
            'status' => 'Active'
        ]);
    }
    
    /**
     * Handle subscription.pending event
     * This is triggered when a subscription payment is pending
     */
    protected function handleSubscriptionPending(array $subscriptionEntity)
    {
        $subscriptionId = $subscriptionEntity['id'];
        
        Log::channel('razorpay')->info('Subscription pending', [
            'subscription_id' => $subscriptionId
        ]);
        
        // Find the subscription in our database
        $subscription = Subscriber::where('subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::channel('razorpay')->error('Subscription not found for pending status', [
                'subscription_id' => $subscriptionId
            ]);
            return;
        }
        
        // Update subscription status
        $subscription->update([
            'status' => 'Pending'
        ]);
    }
    
    /**
     * Handle refund.created event
     * This is triggered when a refund is created
     */
    protected function handleRefundCreated(array $refundEntity, ?array $paymentEntity = null)
    {
        $refundId = $refundEntity['id'];
        $paymentId = $refundEntity['payment_id'];
        $amount = $refundEntity['amount'] / 100; // Convert from paise to rupees
        
        Log::channel('razorpay')->info('Refund created', [
            'refund_id' => $refundId,
            'payment_id' => $paymentId,
            'amount' => $amount
        ]);
        
        // Find the payment in our database
        $payment = Payment::where('order_id', $paymentId)->first();
        
        if (!$payment) {
            Log::channel('razorpay')->error('Payment not found for refund', [
                'payment_id' => $paymentId
            ]);
            return;
        }
        
        // Update payment status
        $payment->status = 'refunded';
        $payment->save();
        
        // If this was a subscription payment, you might want to update the subscription
        $subscription = Subscriber::where('user_id', $payment->user_id)
            ->where('plan_id', $payment->plan_id)
            ->where('status', 'Active')
            ->first();
            
        if ($subscription) {
            // Handle subscription refund logic if needed
        }
    }
    
    /**
     * Process a one-time payment
     */
    protected function processOneTimePayment(array $paymentEntity, $userId, $planId)
    {
        // Find the user
        $user = User::find($userId);
        
        if (!$user) {
            Log::channel('razorpay')->error('User not found for payment', [
                'user_id' => $userId
            ]);
            return;
        }
        
        // Find the plan
        $plan = SubscriptionPlan::find($planId);
        
        if (!$plan) {
            Log::channel('razorpay')->error('Plan not found for payment', [
                'plan_id' => $planId
            ]);
            return;
        }
        
        // Calculate tax
        $tax_value = (config('payment.payment_tax') > 0) ? $plan->price * config('payment.payment_tax') / 100 : 0;
        $total_price = $tax_value + $plan->price;
        
        // Process referral bonus if enabled
        if (config('payment.referral.enabled') == 'on') {
            if (config('payment.referral.payment.policy') == 'first') {
                if (!Payment::where('user_id', $user->id)->where('status', 'completed')->exists()) {
                    event(new PaymentReferrerBonus($user, $planId, $total_price, 'Razorpay'));
                }
            } else {
                event(new PaymentReferrerBonus($user, $planId, $total_price, 'Razorpay'));
            }
        }
        
        // Record the payment
        $record_payment = new Payment();
        $record_payment->user_id = $user->id;
        $record_payment->plan_id = $plan->id;
        $record_payment->order_id = $paymentEntity['id'];
        $record_payment->plan_name = $plan->plan_name;
        $record_payment->price = $total_price;
        $record_payment->currency = $plan->currency;
        $record_payment->gateway = 'Razorpay';
        $record_payment->frequency = 'one-time';
        $record_payment->status = 'completed';
        $record_payment->tokens = $plan->token_credits;
        $record_payment->characters = $plan->characters;
        $record_payment->minutes = $plan->minutes;
        $record_payment->images = $plan->image_credits;
        $record_payment->save();
        
        // For one-time payments, create a subscription record with an end date
        $duration = ($plan->payment_frequency == 'monthly') ? 30 : 365;
        
        // Check if subscription already exists
        $subscription = Subscriber::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->first();
            
        if ($subscription) {
            // Update existing subscription
            $subscription->update([
                'status' => 'Active',
                'active_until' => Carbon::now()->addDays($duration),
                'razorpay_customer_id' => $paymentEntity['customer_id'] ?? null,
                'subscription_id' => null // This is a one-time payment, not a subscription
            ]);
        } else {
            // Create new subscription
            Subscriber::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'Active',
                'active_until' => Carbon::now()->addDays($duration),
                'razorpay_customer_id' => $paymentEntity['customer_id'] ?? null,
                'subscription_id' => null // This is a one-time payment, not a subscription
            ]);
        }
        
        // Update user details
        $group = ($user->hasRole('admin')) ? 'admin' : 'subscriber';
        
        $user->syncRoles($group);    
        $user->group = $group;
        $user->plan_id = $plan->id;
        $user->tokens = $plan->token_credits;
        $user->characters = $plan->characters;
        $user->minutes = $plan->minutes;
        $user->images = $plan->image_credits;
        $user->member_limit = $plan->team_members;
        $user->save();
        
        // Trigger payment processed event
        event(new PaymentProcessed($user));
    }
}
