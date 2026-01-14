<?php

namespace App\Services;

use App\Models\Payment;
use Stripe\StripeClient;

class PaymentService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function processWebhook($payload, $signature)
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                    
                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
                    
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;
                    
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Stripe webhook error: ' . $e->getMessage());
            return false;
        }
    }

    private function handlePaymentSucceeded($invoice)
    {
        // Find subscription
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $invoice->subscription)
            ->first();

        if (!$subscription) {
            return;
        }

        // Create payment record
        Payment::create([
            'company_id' => $subscription->company_id,
            'subscription_id' => $subscription->id,
            'stripe_payment_intent_id' => $invoice->payment_intent,
            'stripe_invoice_id' => $invoice->id,
            'amount' => $invoice->amount_paid / 100,
            'currency' => $invoice->currency,
            'status' => 'succeeded',
            'payment_method' => $invoice->payment_method_types[0] ?? 'card',
            'paid_at' => now(),
        ]);

        // Update subscription
        $subscription->update([
            'status' => 'active',
            'ends_at' => now()->addMonth(), // Update for next billing
        ]);
    }

    private function handlePaymentFailed($invoice)
    {
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $invoice->subscription)
            ->first();

        if (!$subscription) {
            return;
        }

        // Create failed payment record
        Payment::create([
            'company_id' => $subscription->company_id,
            'subscription_id' => $subscription->id,
            'stripe_invoice_id' => $invoice->id,
            'amount' => $invoice->amount_due / 100,
            'currency' => $invoice->currency,
            'status' => 'failed',
            'paid_at' => null,
        ]);

        // Update subscription status
        $subscription->update([
            'status' => 'past_due',
        ]);
    }

    private function handleSubscriptionUpdated($subscriptionData)
    {
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $subscriptionData->id)
            ->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => $subscriptionData->status,
            'trial_ends_at' => $subscriptionData->trial_end ? \Carbon\Carbon::createFromTimestamp($subscriptionData->trial_end) : null,
            'ends_at' => $subscriptionData->current_period_end ? \Carbon\Carbon::createFromTimestamp($subscriptionData->current_period_end) : null,
        ]);
    }

    private function handleSubscriptionDeleted($subscriptionData)
    {
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $subscriptionData->id)
            ->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => 'canceled',
            'cancelled_at' => now(),
            'ends_at' => now(),
        ]);
    }

    public function createPaymentIntent($amount, $currency = 'usd', $metadata = [])
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amount * 100,
                'currency' => $currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment($paymentIntentId, $amount = null)
    {
        try {
            $params = ['payment_intent' => $paymentIntentId];
            
            if ($amount) {
                $params['amount'] = $amount * 100;
            }

            $refund = $this->stripe->refunds->create($params);

            // Update payment record
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'refunded',
                    'refunded_at' => now(),
                ]);
            }

            return [
                'success' => true,
                'refund_id' => $refund->id,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}