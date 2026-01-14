<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\AuditLogService;
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
        $plans = Plan::where('is_active', true)
            ->where('is_trial', false)
            ->orderBy('monthly_price')
            ->get();

        $currentSubscription = $company->activeSubscription;
        $payments = $company->payments()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('company.subscription', compact(
            'company', 
            'plans', 
            'currentSubscription',
            'payments'
        ));
    }

    public function subscribe(Request $request)
    {
        $company = Auth::user()->company;

        // Check if company is approved
        if (!$company->isApproved()) {
            return redirect()->back()
                ->with('error', 'Your company must be approved before subscribing.');
        }

        // Check trial limitations
        if ($company->isOnTrial()) {
            return redirect()->back()
                ->with('error', 'Cannot subscribe while on trial. Please wait for trial to end or contact support.');
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan = Plan::find($validated['plan_id']);

        // Check if already subscribed to this plan
        $existingSubscription = $company->subscriptions()
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return redirect()->back()
                ->with('error', 'You are already subscribed to this plan.');
        }

        // Create subscription
        $result = $this->subscriptionService->createSubscription(
            $company,
            $plan,
            $validated['billing_cycle']
        );

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        // Log subscription creation
        AuditLogService::logSubscriptionCreated($result['subscription']);

        return view('company.subscription-checkout', [
            'clientSecret' => $result['client_secret'],
            'subscription' => $result['subscription'],
        ]);
    }

    public function cancel(Subscription $subscription)
    {
        // Verify ownership
        if ($subscription->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $success = $this->subscriptionService->cancelSubscription($subscription);

        if ($success) {
            return redirect()->route('company.subscription')
                ->with('success', 'Subscription cancelled successfully.');
        }

        return redirect()->route('company.subscription')
            ->with('error', 'Failed to cancel subscription. Please try again.');
    }

    public function invoices()
    {
        $company = Auth::user()->company;
        $payments = $company->payments()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('company.invoices', compact('payments'));
    }

    public function downloadInvoice(Payment $payment)
    {
        // Verify ownership
        if ($payment->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // In a real application, you would generate a PDF invoice here
        // For now, we'll just show the payment details
        
        return view('company.invoice-detail', compact('payment'));
    }
}