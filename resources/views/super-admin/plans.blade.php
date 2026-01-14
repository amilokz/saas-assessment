@extends('layouts.app')

@section('title', 'Manage Plans')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subscription Plans
        </h2>
        <a href="{{ route('super-admin.plans.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Create New Plan
        </a>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border {{ $plan->is_active ? 'border-blue-200' : 'border-gray-200 opacity-75' }}">
                    <div class="p-6">
                        <!-- Plan Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $plan->description }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('super-admin.plans.toggle-status', $plan) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md {{ $plan->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                                @if($plan->is_trial)
                                    <span class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md bg-blue-100 text-blue-800">
                                        Trial
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="mb-6">
                            <div class="flex items-baseline">
                                <span class="text-3xl font-extrabold text-gray-900">
                                    ${{ number_format($plan->monthly_price, 2) }}
                                </span>
                                <span class="ml-1 text-lg text-gray-500">/month</span>
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                ${{ number_format($plan->yearly_price, 2) }} /year (save {{ round((1 - ($plan->yearly_price / ($plan->monthly_price * 12))) * 100) }}%)
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Features</h4>
                            <ul class="space-y-2">
                                @if($plan->features)
                                    @foreach(json_decode($plan->features, true) as $feature)
                                        <li class="flex items-center text-sm text-gray-600">
                                            <svg class="flex-shrink-0 h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                @endif
                                @if($plan->max_users)
                                    <li class="flex items-center text-sm text-gray-600">
                                        <svg class="flex-shrink-0 h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 3.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0l-1.5-.75"/>
                                        </svg>
                                        Up to {{ $plan->max_users }} users
                                    </li>
                                @endif
                                @if($plan->max_files)
                                    <li class="flex items-center text-sm text-gray-600">
                                        <svg class="flex-shrink-0 h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Up to {{ $plan->max_files }} files
                                    </li>
                                @endif
                                @if($plan->max_storage_mb)
                                    <li class="flex items-center text-sm text-gray-600">
                                        <svg class="flex-shrink-0 h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                        </svg>
                                        {{ $plan->max_storage_mb >= 1024 ? round($plan->max_storage_mb / 1024, 1) . ' GB' : $plan->max_storage_mb . ' MB' }} storage
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('super-admin.plans.edit', $plan) }}"
                                class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" class="inline" 
                                onsubmit="return confirm('Are you sure you want to delete this plan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    {{ $plan->subscriptions()->exists() ? 'disabled' : '' }}>
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection