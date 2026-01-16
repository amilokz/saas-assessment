@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Subscription Management</h4>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        @if(!$company->isApproved())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            Your company needs approval from Super Admin before you can subscribe to paid plans.
        </div>
        @endif
        
        @if($currentSubscription)
        <div class="alert alert-info">
            <h5>Current Subscription</h5>
            <p><strong>Plan:</strong> {{ $currentSubscription->plan->name }}</p>
            <p><strong>Billing Cycle:</strong> {{ ucfirst($currentSubscription->billing_cycle) }}</p>
            <p><strong>Amount:</strong> ${{ number_format($currentSubscription->amount, 2) }}</p>
            <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($currentSubscription->status) }}</span></p>
            
            @if($currentSubscription->isActive())
            <form method="POST" action="{{ route('company.subscription.cancel', $currentSubscription) }}" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm" 
                        onclick="return confirm('Cancel subscription?')">
                    Cancel Subscription
                </button>
            </form>
            @endif
        </div>
        @else
        <div class="alert alert-info">
            <p>No active subscription. Choose a plan below.</p>
        </div>
        @endif
        
        <h5 class="mt-4">Available Plans</h5>
        <div class="row">
            @foreach($plans as $plan)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $plan->name }}</h5>
                    </div>
                    <div class="card-body">
                        <h3>${{ number_format($plan->monthly_price, 2) }}<small>/month</small></h3>
                        <p class="text-muted">or ${{ number_format($plan->yearly_price, 2) }}/year</p>
                        
                        <ul class="list-unstyled">
                            <li>✓ {{ $plan->max_users }} Users</li>
                            <li>✓ {{ $plan->max_storage_mb }} MB Storage</li>
                            @if($plan->features)
                                @foreach(json_decode($plan->features) as $feature)
                                <li>✓ {{ $feature }}</li>
                                @endforeach
                            @endif
                        </ul>
                        
                        @if($company->isApproved())
                        <form method="POST" action="{{ route('company.subscription.subscribe') }}">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <div class="mb-3">
                                <select class="form-select" name="billing_cycle" required>
                                    <option value="monthly">Monthly Billing</option>
                                    <option value="yearly">Yearly Billing (Save 20%)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Subscribe Now</button>
                        </form>
                        @else
                        <button class="btn btn-secondary w-100" disabled>Pending Approval</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            <a href="{{ route('company.invoices') }}" class="btn btn-outline-primary">View Invoices</a>
        </div>
    </div>
</div>
@endsection