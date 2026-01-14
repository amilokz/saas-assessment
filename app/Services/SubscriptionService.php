<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Stripe\StripeClient;

class SubscriptionService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createSubscription(Company $company, Plan $plan, string $billingCycle): array
    {
        try {
            // Create Stripe customer if not exists
            if (!$company->stripe_customer_id) {
                $customer = $this->stripe->customers->create([
                    'email' => $company->email,
                    'name' => $company->name,
                    'metadata' => [
                        'company_id' => $company->id,
                    ],
                ]);
                
                $company->update(['stripe_customer_id' => $customer->id]);
            }

            $priceId = $billingCycle === 'yearly' ? $plan->stripe_yearly_price_id : $plan->stripe_monthly_price_id;
            
            if (!$priceId) {
                throw new \Exception('Price ID not found for this plan');
            }

            // Create Stripe subscription
            $stripeSubscription = $this->stripe->subscriptions->create([
                'customer' => $company->stripe_customer_id,
                'items' => [
                    ['price' => $priceId],
                ],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Create local subscription record
            $subscription = Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'stripe_customer_id' => $company->stripe_customer_id,
                'stripe_price_id' => $priceId,
                'billing_cycle' => $billingCycle,
                'amount' => $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
                'status' => 'pending',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);

            return [
                'success' => true,
                'subscription' => $subscription,
                'client_secret' => $stripeSubscription->latest_invoice->payment_intent->client_secret,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        try {
            if ($subscription->stripe_subscription_id) {
                $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);
            }

            $subscription->update([
                'status' => 'canceled',
                'cancelled_at' => now(),
                'ends_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}