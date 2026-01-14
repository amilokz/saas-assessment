@extends('layouts.app')

@section('title', 'Subscription Management')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Subscription Management
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Current Subscription -->
        @if($currentSubscription)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Current Subscription</h3>
                            <p class="text-sm text-gray-500">Manage your active subscription</p>
                        </div>
                        <div>
                            @if($currentSubscription->isActive())
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @elseif($currentSubscription->isOnTrial())
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    On Trial
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Plan Details -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Plan Details</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Plan:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $currentSubscription->plan->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Billing Cycle:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ ucfirst($currentSubscription->billing_cycle) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Amount:</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        ${{ number_format($currentSubscription->amount, 2) }} / {{ $currentSubscription->billing_cycle === 'yearly' ? 'year' : 'month' }}
                                    </span>
                                </div>
                                @if($currentSubscription->isOnTrial() && $currentSubscription->trial_ends_at)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Trial Ends:</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $currentSubscription->trial_ends_at->format('M d, Y') }}
                                            ({{ $currentSubscription->trial_ends_at->diffForHumans() }})
                                        </span>
                                    </div>
                                @endif
                                @if($currentSubscription->ends_at)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Next Billing:</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $currentSubscription->ends_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Actions</h4>
                            <div class="space-y-3">
                                @if($currentSubscription->isActive() && !$currentSubscription->isOnTrial())
                                    <form action="{{ route('company.subscription.cancel', $currentSubscription) }}" method="POST" 
                                        onsubmit="return confirm('Are you sure you want to cancel your subscription?')">
                                        @csrf
                                        <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Cancel Subscription
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('company.invoices') }}"
                                    class="block w-full text-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View Invoices
                                </a>
                                @if($company->isOnTrial())
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                        <div class="flex">
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    You're currently on trial. Subscribe to continue using all features after trial ends.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Active Subscription -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            @if($company->isOnTrial())
                                You're currently on trial. Subscribe to continue using all features after trial ends.
                            @else
                                You don't have an active subscription. Choose a plan below to get started.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Available Plans -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Available Plans</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 hover:border-blue-300 transition">
                        <div class="p-6">
                            <!-- Plan Header -->
                            <div class="text-center mb-6">
                                <h4 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $plan->description }}</p>
                            </div>

                            <!-- Pricing -->
                            <div class="text-center mb-6">
                                <div class="flex items-baseline justify-center">
                                    <span class="text-3xl font-extrabold text-gray-900">
                                        ${{ number_format($plan->monthly_price, 2) }}
                                    </span>
                                    <span class="ml-1 text-lg text-gray-500">/month</span>
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    or ${{ number_format($plan->yearly_price, 2) }}/year
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="mb-6">
                                <ul class="space-y-3">
                                    @if($plan->features)
                                        @foreach(json_decode($plan->features, true) as $feature)
                                            <li class="flex items-start text-sm">
                                                <svg class="flex-shrink-0 h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="text-gray-600">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>

                            <!-- Subscribe Button -->
                            <div class="space-y-3">
                                <form action="{{ route('company.subscription.subscribe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="hidden" name="billing_cycle" value="monthly">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Subscribe Monthly
                                    </button>
                                </form>
                                <form action="{{ route('company.subscription.subscribe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="hidden" name="billing_cycle" value="yearly">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Subscribe Yearly
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Payments -->
        @if($payments->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Payments</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Invoice
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($payments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $payment->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'succeeded' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    'refunded' => 'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$payment->status] }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('company.invoice.download', $payment) }}"
                                                class="text-blue-600 hover:text-blue-900">
                                                Download
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($payments->count() >= 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('company.invoices') }}"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                View All Payments →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection