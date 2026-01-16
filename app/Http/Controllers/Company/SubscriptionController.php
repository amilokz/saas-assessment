<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is company admin
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can manage subscription.');
        }
        
        $company = $user->company;
        
        $plans = Plan::where('is_active', true)
            ->where('is_trial', false)
            ->orderBy('monthly_price')
            ->get();

        $currentSubscription = $company->activeSubscription;

        return view('company.subscription', compact(
            'company',
            'plans',
            'currentSubscription'
        ));
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can subscribe.');
        }
        
        $company = $user->company;

        if (!$company->isApproved()) {
            return redirect()->back()
                ->with('error', 'Company must be approved to subscribe.');
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        // Check if already subscribed
        $existingSubscription = $company->subscriptions()
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return redirect()->back()
                ->with('error', 'Already subscribed to this plan.');
        }

        // Create subscription (in real app, integrate with Stripe)
        $subscription = Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $validated['billing_cycle'],
            'amount' => $validated['billing_cycle'] === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
            'status' => 'pending',
            'trial_ends_at' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Subscription created. Payment integration required.');
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        $user = Auth::user();
        
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can cancel subscription.');
        }
        
        // Verify ownership
        if ($subscription->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Subscription cancelled successfully.');
    }

    public function invoices()
    {
        $user = Auth::user();
        
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can view invoices.');
        }
        
        $company = $user->company;
        
        $payments = $company->payments()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('company.invoices', compact('payments'));
    }

    public function downloadInvoice(Payment $payment)
    {
        $user = Auth::user();
        
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can download invoices.');
        }
        
        // Verify ownership
        if ($payment->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        // In a real app, generate and download PDF invoice
        // For now, show a message
        return redirect()->back()
            ->with('info', 'Invoice download feature requires PDF generation.');
    }
}