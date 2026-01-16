@extends('layouts.super-admin')

@section('title', 'Manage Plans')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Subscription Plans</h1>
            <p class="text-muted mb-0">Create and manage subscription plans for companies</p>
        </div>
        <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Create New Plan
        </a>
    </div>

    <!-- Plans Grid -->
    <div class="row">
        @if($plans->isEmpty())
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-credit-card fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Subscription Plans</h4>
                        <p class="text-muted mb-4">Create your first subscription plan to get started</p>
                        <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Create First Plan
                        </a>
                    </div>
                </div>
            </div>
        @else
            @foreach($plans as $plan)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card border-left-{{ $plan->is_active ? 'primary' : 'secondary' }} shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="m-0 font-weight-bold text-{{ $plan->is_active ? 'primary' : 'secondary' }}">
                                {{ $plan->name }}
                                @if($plan->is_trial)
                                    <span class="badge bg-info ms-2">Trial</span>
                                @endif
                            </h6>
                            @if($plan->description)
                                <p class="text-muted mb-0 small mt-1">{{ $plan->description }}</p>
                            @endif
                        </div>
                        <form action="{{ route('super-admin.plans.toggle-status', $plan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <!-- Pricing -->
                        <div class="text-center mb-4">
                            <h2 class="display-4 font-weight-bold text-primary">
                                ${{ number_format($plan->monthly_price, 2) }}
                            </h2>
                            <p class="text-muted">per month</p>
                            <div class="text-success">
                                <i class="fas fa-percentage me-1"></i>
                                Save {{ round((1 - ($plan->yearly_price / ($plan->monthly_price * 12))) * 100) }}% with yearly billing
                            </div>
                            <p class="text-muted small mt-1">${{ number_format($plan->yearly_price, 2) }} billed yearly</p>
                        </div>

                        <!-- Features -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold mb-3">Features Include:</h6>
                            @if($plan->features)
                                <ul class="list-unstyled">
                                    @foreach(json_decode($plan->features, true) as $feature)
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ $feature }}
                                    </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Limits -->
                        <div class="row text-center mb-4">
                            @if($plan->max_users)
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <i class="fas fa-users text-primary mb-2"></i>
                                    <div class="font-weight-bold">{{ $plan->max_users }}</div>
                                    <small class="text-muted">Users</small>
                                </div>
                            </div>
                            @endif
                            @if($plan->max_files)
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <i class="fas fa-file text-primary mb-2"></i>
                                    <div class="font-weight-bold">{{ $plan->max_files }}</div>
                                    <small class="text-muted">Files</small>
                                </div>
                            </div>
                            @endif
                            @if($plan->max_storage_mb)
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <i class="fas fa-database text-primary mb-2"></i>
                                    <div class="font-weight-bold">
                                        {{ $plan->max_storage_mb >= 1024 ? round($plan->max_storage_mb / 1024, 1) . ' GB' : $plan->max_storage_mb . ' MB' }}
                                    </div>
                                    <small class="text-muted">Storage</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <div class="d-flex gap-2">
                                <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this plan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            {{ $plan->subscriptions()->exists() ? 'disabled' : '' }}>
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                                <a href="#" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Plan Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Plan Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h5">
                                    {{ $plans->where('is_active', true)->count() }}
                                </div>
                                <div class="text-muted">Active Plans</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h5">
                                    {{ $plans->where('is_trial', true)->count() }}
                                </div>
                                <div class="text-muted">Trial Plans</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h5">
                                    ${{ number_format($plans->sum('monthly_price'), 2) }}
                                </div>
                                <div class="text-muted">Total Monthly Value</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h5">
                                    {{ $plans->count() }}
                                </div>
                                <div class="text-muted">Total Plans</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection