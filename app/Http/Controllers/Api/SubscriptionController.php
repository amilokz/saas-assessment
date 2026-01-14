<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index()
    {
        $company = Auth::user()->company;
        
        $subscriptions = $company->subscriptions()
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $subscriptions,
        ]);
    }

    public function plans()
    {
        $plans = Plan::where('is_active', true)
            ->where('is_trial', false)
            ->orderBy('monthly_price')
            ->get();

        return response()->json([
            'data' => $plans,
        ]);
    }

    public function subscribe(Request $request)
    {
        $company = Auth::user()->company;

        // Check if company is approved
        if (!$company->isApproved()) {
            return response()->json([
                'error' => 'Your company must be approved before subscribing.'
            ], 403);
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan = Plan::find($validated['plan_id']);

        // Create subscription
        $result = $this->subscriptionService->createSubscription(
            $company,
            $plan,
            $validated['billing_cycle']
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['message']
            ], 400);
        }

        return response()->json([
            'message' => 'Subscription created successfully',
            'data' => [
                'subscription' => $result['subscription'],
                'client_secret' => $result['client_secret'],
            ],
        ]);
    }

    public function cancel(Subscription $subscription)
    {
        // Verify ownership
        if ($subscription->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $success = $this->subscriptionService->cancelSubscription($subscription);

        if (!$success) {
            return response()->json([
                'error' => 'Failed to cancel subscription'
            ], 400);
        }

        return response()->json([
            'message' => 'Subscription cancelled successfully',
        ]);
    }

    public function invoices()
    {
        $company = Auth::user()->company;
        
        $payments = $company->payments()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $payments,
        ]);
    }
}